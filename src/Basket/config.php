<?php

use App\Basket\Basket;
use App\Basket\SessionBasket;
use App\Basket\Twig\BasketTwigExtension;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [

    'twig.extensions' => add(
        get(BasketTwigExtension::class)
    ),
    Basket::class => autowire(SessionBasket::class)

];
