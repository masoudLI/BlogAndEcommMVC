<?php

namespace App\Shop\Actions;

use App\Shop\Repository\ProductRepository;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductListingAction
{
    private $renderer;

    private $productRepository;

    public function __construct(RendererInterface $renderer, ProductRepository $productRepository)
    {
        $this->renderer = $renderer;
        $this->productRepository = $productRepository;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        $products = $this->productRepository->findPublic()->paginate(12, $params['p'] ?? 1);
        return $this->renderer->render('@shop/index', [
            'products' => $products
        ]);
    }

}
