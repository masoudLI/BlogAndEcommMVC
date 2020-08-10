<?php

use App\Auth\DatabaseAuth;
use App\Auth\ForbidddenMiddleware;
use App\Auth\Model\User;
use App\Auth\Repository\UserRepository;
use App\Auth\Twig\AuthTwigExtension;
use Framework\Auth;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    'auth_login' => '/login',
    'auth_entity' => User::class,
    'twig.extensions' => add([
        get(AuthTwigExtension::class)
    ]),
    Auth::class => get(DatabaseAuth::class),
    UserRepository::class => autowire()->constructorParameter('entity', get('auth_entity')),
    ForbidddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth_login'))
];
