<?php

namespace App\Account;

use App\Account\Actions\AccountAction;
use App\Account\Actions\AccountEditAction;
use App\Account\Actions\SignupAction;
use Framework\Auth\LoggedinMiddleware;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AccountModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(ContainerInterface $container, RendererInterface $renderer, Router $router)
    {
        $renderer->addPath('account', __DIR__ . '/views');
        $prefix = $container->get('auth_login');
        $router->get('account_signup', '/inscription', SignupAction::class);
        $router->post(null, '/inscription', SignupAction::class);
        $router->get('account_profile', '/profile', [LoggedinMiddleware::class, AccountAction::class]);
        $router->post(null, '/profile', AccountEditAction::class);
    }
}
