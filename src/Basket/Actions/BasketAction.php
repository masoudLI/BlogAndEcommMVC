<?php

namespace App\Basket\Actions;

use App\Basket\Basket;
use App\Shop\Model\Product;
use App\Basket\Repository\BasketRepository;
use App\Shop\Repository\ProductRepository;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectBackResponse;
use Psr\Http\Message\ServerRequestInterface;

class BasketAction
{
    private $renderer;

    private $basket;

    private $productRepository;

    private $basketRepository;

    public function __construct(RendererInterface $renderer, Basket $basket, ProductRepository $productRepository, BasketRepository $basketRepository)
    {
        $this->renderer = $renderer;
        $this->basket = $basket;
        $this->productRepository = $productRepository;
        $this->basketRepository = $basketRepository;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->show($request);
        } else {
            if ($request->getMethod() === 'POST') {
                $params = $request->getParsedBody();
                /** @var Product */
                $product = $this->productRepository->find((int)$request->getAttribute('id'));
                $this->basket->addProduct($product, $params['quantity'] ?? null);
                return new RedirectBackResponse($request);
            }
        }
    }


    public function show(ServerRequestInterface $request)
    {
        $this->basketRepository->hydrateBasket($this->basket);
        return $this->renderer->render('@basket/show', [
            'basket' => $this->basket
        ]);
    }
}
