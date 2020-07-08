<?php declare(strict_types=1);

namespace Tests;

use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
  public function testBlog(): void
  {
    $app = new App();
    $request = new ServerRequest('GET', '/blog');
    $response = $app->run($request);
    $this->assertEquals('<h1>Bonjour tout le monde</h1>', $response->getBody());
  }

  public function testPageNotExiste(): void
  {
    $app = new App();
    $request = new ServerRequest('GET', '/bloggg');
    $response = $app->run($request);
    $this->assertEquals('<h1>Erreur 404</h1>', $response->getBody());
  }
}
