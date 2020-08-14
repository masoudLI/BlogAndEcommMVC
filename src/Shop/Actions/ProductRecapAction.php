<?php

namespace App\Shop\Actions;

use App\Shop\Model\Product;
use App\Shop\Repository\ProductRepository;
use Framework\Api\Stripe;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Staaky\VATRates\VATRates;

class ProductRecapAction
{

    private $renderer;


    private $table;


    private $stripe;


    public function __construct(RendererInterface $renderer, ProductRepository $table, Stripe $stripe)
    {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->stripe = $stripe;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $stripeToken = $params['stripeToken'];
        $card = $this->stripe->getCardFromToken($stripeToken);
        $vat = (new VATRates())->getStandardRate($card->country);
        /** @var Product */
        $product = $this->table->find((int)$request->getAttribute('id'));
        $price = floor($product->getPrice() * ((100 + $vat) / 100));
        return $this->renderer->render('@shop/recap', compact('product', 'stripeToken', 'vat', 'price', 'card'));
    }
}
