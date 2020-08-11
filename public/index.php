<?php

declare(strict_types=1);

use App\Account\AccountModule;
use App\Admin\AdminModule;
use App\Blog\BlogModule;
use App\Auth\AuthModule;
use App\Auth\ForbiddenMiddleware;
use App\Blog\Actions\PagePostIndex;
use App\Contact\ContactModule;
use Framework\App;
use Framework\Auth\LoggedinMiddleware;
use Framework\Auth\RoleMiddlewareFactory;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\RouterMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Router;
use Framework\Middleware\TrailingSlashMiddleware;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';


$app = (new App('config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class)
    ->addModule(AuthModule::class)
    ->addModule(ContactModule::class)
    ->addModule(AccountModule::class);

$app->getContainer()->get(Router::class)->get('home', '/', PagePostIndex::class, []);

// middleware
$container = $app->getContainer();
$app
    ->pipe(\Franzl\Middleware\Whoops\WhoopsMiddleware::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe(
        $container->get('admin_prefix'),
        $container->get(RoleMiddlewareFactory::class)->makeForRole('admin')
    )
    ->pipe(MethodMiddleware::class)
    //->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);


if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
