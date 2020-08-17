<?php

use App\Basket\Actions\BasketAction;
use App\Basket\Basket;
use App\Basket\BasketFactory;
use App\Basket\Twig\BasketTwigExtension;
use Stripe\Stripe;

use function DI\add;
use function DI\autowire;
use function DI\create;
use function DI\factory;
use function DI\get;

return [

    'twig.extensions' => add(
        get(BasketTwigExtension::class)
    ),
    Basket::class => factory(BasketFactory::class),
    BasketAction::class => autowire()->constructorParameter('stripeKey', get(('stripe_key'))),
    Stripe::class => create()->constructor(get('stripe_secret'))
];
