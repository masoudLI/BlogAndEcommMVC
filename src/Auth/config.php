<?php

use App\Auth\DatabaseAuth;
use App\Auth\ForbidddenMiddleware;
use App\Auth\Twig\AuthTwigExtension;
use Framework\Auth;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    'auth_login' => '/login',
    'twig.extensions' => add([
        get(AuthTwigExtension::class)
    ]),
    Auth::class => get(DatabaseAuth::class),
    ForbidddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth_login'))
];
