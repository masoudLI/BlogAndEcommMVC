<?php

declare(strict_types=1);

use App\Admin\AdminModule;
use App\Blog\Actions\PagePostIndex;
use Framework\App;
use App\Blog\BlogModule;
use App\Error\ModuleError;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Renderer\TwigRenderer;
use Framework\Router;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$modules = [
    AdminModule::class,
    BlogModule::class
];

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions('config/config.php');
foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$container = $builder->build();
$app = new App($container, $modules);
$app->getContainer()->get(Router::class)->get('home', '/', PagePostIndex::class, []);

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
