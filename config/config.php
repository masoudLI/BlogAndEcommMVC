<?php

use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;
use Framework\Session\PHPSession;
use Framework\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Framework\Twig\{
    CsrfExtension,
    FlashExtension,
    FormExtension,
    PagerFantaExtension,
    TextExtension,
    TimeExtension
};

use function DI\{autowire, create, factory, get};


return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'monframework',
    'views_path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
        get(PagerFantaExtension::class),
        get(TextExtension::class),
        get(TimeExtension::class),
        get(FlashExtension::class),
        get(FormExtension::class),
        get(CsrfExtension::class)
    ],
    RendererInterface::class => factory(TwigRendererFactory::class),
    Router::class => create(),
    SessionInterface::class => create(PHPSession::class),
    CsrfMiddleware::class => autowire()->constructor(get(SessionInterface::class)),
    \PDO::class => function (ContainerInterface $c) {
        return new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
];
