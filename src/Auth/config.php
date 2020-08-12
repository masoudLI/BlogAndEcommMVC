<?php

use App\Auth\DatabaseAuth;
use App\Auth\ForbiddenMiddleware;
use App\Auth\Mailer\PasswordResetMailer;
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
    ForbiddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth_login')),
    PasswordResetMailer::class => autowire()->constructorParameter('from', get('mail_from'))
];
