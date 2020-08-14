<?php

namespace App\Shop;

use App\Auth\Model\User;
use App\Shop\Exception\AlreadyPurchasedException;
use App\Shop\Model\Product;
use App\Shop\Repository\PurchaseRepository;
use App\Shop\Repository\StripeUserRepository;
use Framework\Api\Stripe;
use Staaky\VATRates\VATRates;
use Stripe\Card;
use Stripe\Customer;

class PurchaseProduct
{
    private $purchaseRepository;

    private $stripe;

    private $stripeUserRepository;

    private $purchaseTable;

    public function __construct(PurchaseRepository $purchaseRepository, Stripe $stripe, StripeUserRepository $stripeUserRepository, PurchaseRepository $purchaseTable)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->stripe = $stripe;
        $this->stripeUserRepository = $stripeUserRepository;
        $this->purchaseTable = $purchaseTable;
    }

    public function process(Product $product, User $user, string $token)
    {
        // verifier si l'utilisateur n'a pas deja acheté le produit
        if ($this->purchaseRepository->findForAlreadyPurchase($product, $user) !== null) {
            throw new AlreadyPurchasedException();
        }

        // calculer le prix TTC
        $card = $this->stripe->getCardFromToken($token);
        $vatRate = (new VATRates())->getStandardRate(($card->country)) ?: 0;
        $price = floor($product->getPrice() * ((100 + $vatRate) / 100));

        // Créer ou récupérer le customer de l'utilisateur
        $customer = $this->findCustomerForUser($user, $token);

        // creer ou recuperer la carte de user
        $card = $this->getMatchingCard($customer, $card);

        if ($card === null) {
            $card = $this->stripe->createCardForCustomer($customer, $token);
        }

        // facturer le user (creer charge)
        $charge = $this->stripe->createCharge([
            "amount" => $price * 100,
            "currency" => "eur",
            "source" => $card->id,
            "customer" => $customer->id,
            "description" => "Achat sur monsite.com {$product->getTitle()}"
        ]);

        // sauvegarder  la transaction
        $this->purchaseTable->insert([
            'user_id' => $user->getId(),
            'product_id' => $product->getId(),
            'price' => $product->getPrice(),
            'vat'   => $vatRate,
            'country' => $card->country,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'charge_id' => $charge->id
        ]);
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


    public function findCustomerForUser(User $user, $token)
    {
        $customerId = $this->stripeUserRepository->findCustomerForUser($user);
        if ($customerId) {
            $customer = $this->stripe->getCustomer($customerId);
        } else {
            $customer = $this->stripe->createCustomer([
                'name'  => $user->getUsername(),
                'email'  => $user->getEmail(),
                'source' => $token
            ]);
            $this->stripeUserRepository->insert([
                'user_id' => $user->getId(),
                'customer_id' => $customer->id,
                'created_at' => date('Y-m-d H:i:s')
            ]); 
        }
        return $customer;
    }
}
