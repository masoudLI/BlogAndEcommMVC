<?php

namespace App\Shop;

use App\Shop\Actions\InvoiceAction;
use App\Shop\Actions\ProductListingAction;
use App\Shop\Actions\ProductShowAction;
use App\Shop\Actions\ProductDowonloadAction;
use App\Shop\Actions\ProductRecapAction;
use App\Shop\Actions\PurchaseProcessAction;
use App\Shop\Actions\PurchasesListingAction;
use App\Shop\Actions\ShopCrudAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class ShopModule extends Module
{


    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS =  __DIR__ . '/db/migrations';

    const SEEDS =  __DIR__ . '/db/seeds';


    public function __construct(ContainerInterface $container)
    {

        $container->get(RendererInterface::class)->addPath('shop', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get('shop', '/boutique', ProductListingAction::class, []);

        $router->get('shop_download', '/boutique/{id}/download', ProductDowonloadAction::class, [
            'id' => '[0-9]+'
        ]);

        $router->post('shop_recap', '/boutique/{id}/recap', ProductRecapAction::class, [
            'id' => '[0-9]+'
        ]);

        $router->post('shop_process', '/boutique/{id}/process', PurchaseProcessAction::class, [
            'id' => '[0-9]+'
        ]);

        $router->get('shop_purchases', '/boutique/mes-achats', PurchasesListingAction::class, []);
        $router->get('shop_invoice', '/boutique/facture/{id}', InvoiceAction::class, [
            'id' => '[0-9]+'
        ]);

        $router->get('shop_show', '/boutique/{slug}/{id}', ProductShowAction::class, [
            'slug' => '[a-z\-0-9]+',
            'id' => '[0-9]+'
        ]);

        if ($container->has('admin_prefix')) {
            // produits
            $prefix = $container->get('admin_prefix');
            $router->get('shop_admin_products_index', "$prefix/products", ShopCrudAction::class, []);
            $router->get('shop_admin_products_edit', "$prefix/products/edit/{id}", ShopCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->post(null, "$prefix/products/edit/{id}", ShopCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->get('shop_admin_products_create', "$prefix/products/create", ShopCrudAction::class, []);
            $router->post(null, "$prefix/products/create", ShopCrudAction::class, []);
            $router->delete(
                'shop_admin_products_delete',
                "$prefix/products/{id}",
                ShopCrudAction::class,
                [
                    'id' => '[0-9]+'
                ]
            );
        }
    }
}
