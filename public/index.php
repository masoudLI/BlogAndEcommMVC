<?php declare(strict_types=1);

use Framework\App;
use App\Blog\BlogModule;
use Framework\Renderer\PHPRenderer;
use GuzzleHttp\Psr7\ServerRequest;

require '../vendor/autoload.php';

$renderer = new PHPRenderer();
$renderer->addPath(dirname(__DIR__) . '/views');
$app = new App([
  BlogModule::class
], [
  'renderer' => $renderer
]);

$response = $app->run(ServerRequest::fromGlobals());
\Http\Response\send($response);
