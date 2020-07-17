<?php

declare(strict_types=1);

use App\Admin\AdminModule;
use App\Blog\Actions\PagePostIndex;
use Framework\App;
use App\Blog\BlogModule;
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
    ->addModule(BlogModule::class);

$app->getContainer()->get(Router::class)->get('home', '/', PagePostIndex::class, []);

// middleware
$app
    ->pipe(\Franzl\Middleware\Whoops\WhoopsMiddleware::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);


if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
