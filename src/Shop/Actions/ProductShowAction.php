<?php

namespace App\Shop\Actions;

use App\Shop\Model\Product;
use App\Shop\Repository\ProductRepository;
use App\Shop\Repository\PurchaseRepository;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductShowAction
{
    private $renderer;

    private $productRepository;

    private $stripeKey;

    private $auth;

    private $purchaseRepository;

    public function __construct(
        RendererInterface $renderer,
        ProductRepository $productRepository,
        string $stripeKey,
        Auth $auth,
        PurchaseRepository $purchaseRepository
    ) {
        $this->renderer = $renderer;
        $this->productRepository = $productRepository;
        $this->stripeKey = $stripeKey;
        $this->auth = $auth;
        $this->purchaseRepository = $purchaseRepository;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $download = false;
        $stripeKey = $this->stripeKey;
        /** @var Product  */
        $product = $this->productRepository->find((int)$request->getAttribute('id'));
        $user = $this->auth->getUser();
        if ($user !== null && $this->purchaseRepository->findForAlreadyPurchase($product, $user)) {
            $download = true;
        }
        return $this->renderer->render('@shop/show', compact('product', 'stripeKey', 'download'));
    }
}
