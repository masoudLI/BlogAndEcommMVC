<?php

use App\Shop\Actions\ProductShowAction;
use Framework\Api\Stripe;

use function DI\autowire;
use function DI\create;
use function DI\get;

return [
    ProductShowAction::class => autowire()->constructorParameter('stripeKey', get(('stripe_key'))),
    Stripe::class => create()->constructor(get('stripe_secret'))
];
