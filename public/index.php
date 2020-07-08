<?php declare(strict_types=1);

use Framework\App;
use App\Blog\BlogModule;
use GuzzleHttp\Psr7\ServerRequest;

require '../vendor/autoload.php';


$app = new App([
  BlogModule::class
]);

$response = $app->run(ServerRequest::fromGlobals());
\Http\Response\send($response);
