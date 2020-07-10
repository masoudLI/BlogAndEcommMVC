<?php

declare(strict_types=1);

use Framework\App;
use App\Blog\BlogModule;
use App\Error\ModuleError;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Renderer\TwigRenderer;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$modules = [
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

if (php_sapi_name() !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    \Http\Response\send($response);
}
