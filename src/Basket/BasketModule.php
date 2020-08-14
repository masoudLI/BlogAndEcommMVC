<?php

namespace App\Basket;

use App\Basket\Actions\BasketAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;

class BasketModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    const NAME = 'basket';

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('basket', __DIR__ . '/views');
        $router->post('shop_basket_add', '/boutique/panier/add/{id}', BasketAction::class, [
            'id' => '[0-9]+'
        ]);
        $router->post('shop_basket_change', '/boutique/panier/change/{id}', BasketAction::class, [
            'id' => '[0-9]+'
        ]);
        $router->get('shop_basket', '/panier', BasketAction::class, []);
    }
}
