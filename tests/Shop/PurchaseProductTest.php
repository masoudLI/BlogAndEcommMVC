<?php

namespace Tests\Shop;

use App\Auth\Model\User;
use App\Shop\Exception\AlreadyPurchasedException;
use App\Shop\Model\Product;
use App\Shop\Model\Purchase;
use App\Shop\PurchaseProduct;
use App\Shop\Repository\PurchaseRepository;
use App\Shop\Repository\StripeUserRepository;
use Framework\Api\Stripe;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Argument\Token\LogicalAndToken;
use Prophecy\PhpUnit\ProphecyTrait;
use Stripe\Card;
use Stripe\Charge;
use Stripe\Collection;
use Stripe\Customer;

class PurchaseProductTest extends TestCase
{

    private $purchaseRepository;
    private $purchase;
    private $stripe;
    private $stripeUserRepository;


    use ProphecyTrait;


    public function setUp(): void
    {
        $this->purchaseRepository = $this->prophesize(PurchaseRepository::class);
        $this->stripe = $this->prophesize(Stripe::class);
        $this->stripeUserRepository = $this->prophesize(StripeUserRepository::class);
        $this->purchase = new PurchaseProduct(
            $this->purchaseRepository->reveal(),
            $this->stripe->reveal(),
            $this->stripeUserRepository->reveal()
        );

        $this->stripe->getCardFromToken(Argument::any())->will(function ($args) {
            $card = new Card();
            $card->fingerprint = "a";
            $card->country = $args[0];
            $card->id = "tokencard";
            return $card;
        });
    }

    public function testAlreadyPurchaseProduct()
    {

        $user = $this->makeUser();
        $product = $this->makeProduct();
        $this->purchaseRepository->findForAlreadyPurchase($product, $user)
            ->shouldBeCalled()
            ->willReturn($this->makePurchase());
        $this->expectException(AlreadyPurchasedException::class);
        $this->purchase->process($product, $user, 'FR');
    }


    public function testPurchaseFrance()
    {
        $customerId = 'cuz_12312312';
        $token = 'FR';
        $product = $this->makeProduct();
        $card = $this->makeCard();
        $user = $this->makeUser();
        $customer = $this->makeCustomer();
        $charge = $this->makeCharge();

        $this->purchaseRepository->findFor($product, $user)->willReturn(null);
        $this->stripeUserTable->findCustomerForUser($user)->willReturn($customerId);
        $this->stripe->getCustomer($customerId)->willReturn($customer);
        $this->stripe->createCardForCustomer($customer, $token)
            ->shouldBeCalled()
            ->willReturn($card);
        $this->stripe->createCharge(new Argument\Token\LogicalAndToken([
            Argument::withEntry('amount', 6000),
            Argument::withEntry('source', $card->id)
        ]))->shouldBeCalled()
            ->willReturn($charge);
        $this->purchaseRepository->insert([
            'user_id' => $user->getId(),
            'product_id' => $product->getId(),
            'price' => 50.00,
            'vat' => 20,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'charge_id' => $charge->id
        ])->shouldBeCalled();
        // On lance l'achat
        $this->purchase->process($product, $user, $token);
    }


    private function makePurchase(): Purchase
    {
        $purchase = new Purchase();
        $purchase->setId(3);
        return $purchase;
    }

    private function makeUser(): User
    {
        $user = new User();
        $user->setId(4);
        return $user;
    }

    private function makeCustomer(array $sources = []): Customer
    {
        $customer = new Customer();
        $customer->id = "cus_1233";
        $collection = $this->prophesize(Collection::class);
        $collection->all()->willReturn($sources);
        $customer->sources = $collection->reveal();
        return $customer;
    }

    private function makeProduct(): Product
    {
        $product = new Product();
        $product->setId(4);
        $product->setPrice(50);
        return $product;
    }

    private function makeCard(): Card
    {
        $card = new Card();
        $card->id = "card_13123";
        $card->fingerprint = "a";
        return $card;
    }

    private function makeCharge(): Charge
    {
        $charge = new Charge();
        $charge->id = "azeaz_13123";
        return $charge;
    }
}
