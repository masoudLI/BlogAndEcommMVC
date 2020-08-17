<?php

namespace App\Basket\Actions;

use App\Basket\Basket;
use App\Basket\Repository\BasketRepository;
use App\Shop\Model\Product;
use App\Shop\Repository\ProductRepository;
use Framework\Api\Stripe;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Staaky\VATRates\VATRates;

class OrderRecapAction
{

    private $renderer;


    private $table;


    private $stripe;


    private $basket;

    public function __construct(
        RendererInterface $renderer,
        BasketRepository $table,
        Stripe $stripe,
        Basket $basket
    ) {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->stripe = $stripe;
        $this->basket = $basket;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $stripeToken = $params['stripeToken'];
        $card = $this->stripe->getCardFromToken($stripeToken);
        $vat = (new VATRates())->getStandardRate($card->country);
        $basket = $this->basket;
        $this->table->hydrateBasket($basket);
        $price = floor($basket->getTotal() * ((100 + $vat) / 100));
        return $this->renderer->render('@basket/recap', compact(
            'basket',
            'vat',
            'stripeToken',
            'price',
            'card'
        ));
    }
}
