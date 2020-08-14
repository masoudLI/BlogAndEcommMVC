<?php

namespace App\Shop\Actions;

use Psr\Http\Message\ServerRequestInterface;
use App\Shop\Table\ProductTable;
use App\Shop\Table\PurchaseTable;
use Framework\Auth\ForbiddenException;
use GuzzleHttp\Psr7\Response;
use App\Shop\Model\Product;
use App\Shop\PurchaseProduct;
use App\Shop\Repository\ProductRepository;
use App\Shop\Repository\PurchaseRepository;
use Framework\Auth;

class ProductDowonloadAction
{

    private $table;


    private $purchase;


    private $auth;


    private $purchaseTable;



    public function __construct(
        ProductRepository $table,
        PurchaseProduct $purchase,
        Auth $auth,
        PurchaseRepository $purchaseTable
    ) {
        $this->table = $table;
        $this->purchase = $purchase;
        $this->auth = $auth;
        $this->purchaseTable = $purchaseTable;
    }


    public function __invoke(ServerRequestInterface $request)
    {

        /** @var Product $product */
        $product = $this->table->find((int)$request->getAttribute('id'));
        $user = $this->auth->getUser();
        if ($this->purchaseTable->findForAlreadyPurchase($product, $user)) {
            $source = fopen('downloads/' . $product->getPdf(), 'r');
            return new Response(200, ['Content-Type' => 'application/pdf'], $source);
        } else {
            throw new ForbiddenException();
        }
    }
}
