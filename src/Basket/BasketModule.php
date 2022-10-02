<?php

namespace App\Basket;

use App\Basket\Actions\BasketAction;
use App\Basket\Actions\OrderInvoiceAction;
use App\Basket\Actions\OrderListingAction;
use App\Basket\Actions\OrderProcessAction;
use App\Basket\Actions\OrderRecapAction;
use Framework\Auth\LoggedinMiddleware;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Massoud\EventManager;

class BasketModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS  = __DIR__ . '/db/migrations';

    const NAME = 'basket';

    public function __construct(Router $router, RendererInterface $renderer, EventManager $eventManager, BasketMerger $basketMerger)
    {
        $renderer->addPath('basket', __DIR__ . '/views');
        $router->post('shop_basket_add', '/boutique/panier/add/{id}', BasketAction::class, [
            'id' => '[0-9]+'
        ]);
        $router->post('shop_basket_change', '/boutique/panier/change/{id}', BasketAction::class, [
            'id' => '[0-9]+'
        ]);
        $router->get('shop_basket', '/panier', BasketAction::class, []);


        // Tunnel d'achat
        $router->post('basket_recap', '/panier/recap', [LoggedinMiddleware::class, OrderRecapAction::class]);
        $router->post('basket_order_process', '/panier/commander', [LoggedInMiddleware::class, OrderProcessAction::class]);

        // Gestion des commandes
        $router->get('basket_orders', '/mes-commandes', [LoggedInMiddleware::class, OrderListingAction::class]);
        $router->get(
            'basket_order_invoice',
            '/mes-commandes/{id}',
            [LoggedInMiddleware::class, OrderInvoiceAction::class],
            [
                'id' => '[0-9]+'
            ]
        );

        //  event manager
        $eventManager->attach('auth_login', $basketMerger);
    }
}
