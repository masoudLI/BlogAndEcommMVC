<?php

namespace App\Shop\Actions;

use App\Shop\Exception\AlreadyPurchasedException;
use App\Shop\Model\Product;
use App\Shop\PurchaseProduct;
use App\Shop\Repository\ProductRepository;
use App\Shop\Table\ProductTable;
use Framework\Actions\RouterAwareAction;
use Framework\Auth;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class PurchaseProcessAction
{

    /**
     * @var ProductTable
     */
    private $productTable;
    /**
     * @var PurchaseProduct
     */
    private $purchaseProduct;
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var FlashService
     */
    private $flashService;

    use RouterAwareAction;

    public function __construct(
        ProductRepository $productTable,
        PurchaseProduct $purchaseProduct,
        Auth $auth,
        Router $router,
        FlashService $flashService
    ) {
        $this->productTable = $productTable;
        $this->purchaseProduct = $purchaseProduct;
        $this->auth = $auth;
        $this->router = $router;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /** @var Product $product */
        $product = $this->productTable->find((int)$request->getAttribute('id'));
        $params = $request->getParsedBody();
        $stripeToken = $params['stripeToken'];
        try {
            $this->purchaseProduct->process($product, $this->auth->getUser(), $stripeToken);
            $this->flashService->success('Merci pour votre achat');
            return $this->redirect('shop_download', ['id' => $product->getId()]);
        } catch (AlreadyPurchasedException $e) {
            return $this->redirect('shop_show', ['slug' => $product->getSlug()]);
        }
    }
}
