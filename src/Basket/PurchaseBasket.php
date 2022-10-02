<?php

namespace App\Basket;

use App\Auth\Model\User;
use App\Basket\Repository\BasketRepository;
use App\Basket\Repository\OrderRepository;
use App\Shop\Repository\StripeUserRepository;
use Framework\Api\Stripe;
use Staaky\VATRates\VATRates;
use Stripe\Card;
use Stripe\Customer;

class PurchaseBasket
{

    private $orderRepository;
    private $stripeUserTable;
    private $stripe;

    public function __construct(
        OrderRepository $orderRepository,
        Stripe $stripe,
        StripeUserRepository $stripeUserTable,
        BasketRepository $basketRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->stripe = $stripe;
        $this->stripeUserTable = $stripeUserTable;
        $this->basketRepository = $basketRepository;
    }

    public function createPaymentSession(Basket $basket, User $user)
    {
        $this->basketRepository->hydrateBasket($basket);
        $result = null;
        foreach ($basket->getRows() as $row) {
            $result = $row;
        }
        $session = $this->stripe->createSeesionCheck([
            'success_url' => 'http://localhost:8000/success?success=1',
            'cancel_url' => 'http://localhost:8000/cancel',
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'shipping_address_collection' => [
                'allowed_countries' => ['FR']
            ],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $result->getProduct()->getTitle(),
                    ],
                    'unit_amount' => $basket->getTotal() * 100,
                ],
                'quantity' => $result->getQuantity(),
            ]],
        ]);
        return $session->id;
    }

    public function process(Basket $basket, User $user, string $token)
    {

        // calculer le prix TTC
        $this->basketRepository->hydrateBasket($basket);
        $card = $this->stripe->getCardFromToken($token);
        $vatRate = (new VATRates())->getStandardRate(($card->country)) ?: 0;

        $price = floor($basket->getTotal() * ((100 + $vatRate) / 100));

        // Créer ou récupérer le customer de l'utilisateur
        $customer = $this->findCustomerForUser($user, $token);
        $card = $this->getMatchingCard($customer, $card);
        if ($card === null) {
            $card = $this->stripe->createCardForCustomer($customer, $token);
        }

        $charge = $this->stripe->createCharge([
            "amount" => $price * 100,
            "currency" => "eur",
            "source" => $card->id,
            "customer" => $customer->id,
            "description" => "Achat sur monsite.com",
        ]);

        $this->orderRepository->createFromBasket($basket, [
            'user_id' => $user->getId(),
            'vat' => $vatRate,
            'country' => $card->country,
            'charge_id' => $charge->id,
        ]);
    }

    /**
     * @param Customer $customer
     * @param Card $card
     * @return bool
     */
    private function hasCard(Customer $customer, Card $card): bool
    {
        $fingerprints = array_map(function ($source) {
            return $source->fingerprint;
        }, (array) $customer->sources->all());
        return in_array($card->fingerprint, $fingerprints);
    }

    /**
     * @param Customer $customer
     * @param Card $card
     * @return null|Card
     */
    private function getMatchingCard(Customer $customer, Card $card): ?Card
    {
        foreach ($customer->sources->data as $datum) {
            if ($datum->fingerprint === $card->fingerprint) {
                return $datum;
            }
        }
        return null;
    }
    /**
     * Génère le client à partir de l'utilisateur
     * @param User $user
     * @param $token
     * @return Customer
     */
    private function findCustomerForUser(User $user, $token): Customer
    {
        $customerId = $this->stripeUserTable->findCustomerForUser($user);
        if ($customerId) {
            $customer = $this->stripe->getCustomer($customerId);
        } else {
            $customer = $this->stripe->createCustomer([
                'name' => $user->getUsername(),
                'email' => $user->getEmail(),
                'source' => $token,
            ]);
            $this->stripeUserTable->insert([
                'user_id' => $user->getId(),
                'customer_id' => $customer->id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $customer;
    }
}
