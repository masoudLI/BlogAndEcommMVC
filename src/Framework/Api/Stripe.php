<?php

namespace Framework\Api;

use Stripe\{Charge, Customer, Card};

class Stripe
{

    private $stripe;

    public function __construct(string $token)
    {
        $this->stripe = new \Stripe\StripeClient($token);
    }

    public function getCardFromToken(string $token): Card
    {
        return $this->stripe->tokens->retrieve($token)->card;
    }

    public function getCustomer($customerId): Customer
    {
        return $this->stripe->customers->retrieve($customerId);
    }

    public function createCustomer(array $params): Customer
    {
        return $this->stripe->customers->create($params);
    }

    public function createCardForCustomer(Customer $customer, string $token)
    {
        return $customer->createSource(
            ['source' => $token]
        );
    }

    public function createCharge(array $params): Charge
    {
        return $this->stripe->charges->create($params);
    }
}
