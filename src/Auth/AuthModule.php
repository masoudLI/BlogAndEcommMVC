<?php

namespace App\Auth;

use App\Auth\Actions\LoginAction;
use App\Auth\Actions\LoginAttemptAction;
use App\Auth\Actions\LogoutAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AuthModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS  = __DIR__ . '/db/migrations';
    const SEEDS  = __DIR__ . '/db/seeds';

    public function __construct(ContainerInterface $container, RendererInterface $renderer, Router $router)
    {
        $renderer->addPath('auth', __DIR__ . '/views');
        $prefix = $container->get('auth_login');
        $router->get('auth_login', $prefix, LoginAction::class);
        $router->post(null, $prefix, LoginAttemptAction::class);
        $router->post('auth_logout', '/logout', LogoutAction::class);
        $router->get('auth_password', '/password', PasswordForgetAction::class);
        $router->post(null, '/password', PasswordForgetAction::class);
        $router->get('auth_reset', '/password/reset/{id}/{token}', PasswordResetAction::class, [
            'id' => '[0-9]+',
            'token' => '[a-z\-0-9]+'
        ]);
        $router->post(null, '/password/reset/{id}/{token}', PasswordResetAction::class, [
            'id' => '[0-9]+',
            'token' => '[a-z\-0-9]+'
        ]);
    }
}
