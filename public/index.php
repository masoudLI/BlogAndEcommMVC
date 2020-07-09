<?php

declare(strict_types=1);

use Framework\App;
use App\Blog\BlogModule;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Renderer\TwigRenderer;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';


$renderer = new TwigRenderer(dirname(__DIR__) . '/views');

$app = new App([

  BlogModule::class
], [
  'renderer' => $renderer
]);

$response = $app->run(ServerRequest::fromGlobals());
\Http\Response\send($response);
