<?php

declare(strict_types=1);

namespace Tests;

use Framework\App;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class AppTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->container = $this->getMockBuilder(ContainerInterface::class)->getMock();
  }

  public function testBlog(): void
  {
    $app = new App($this->container);
    $request = new ServerRequest('GET', '/blog');
    $this->container->get(Router::class)->match($request);
    $response = $app->run($request);
    $this->assertEquals('<h1>Bonjour tout le monde</h1>', $response->getBody());
  }

  public function testPageNotExiste(): void
  {
    $app = new App($this->container);
    $request = new ServerRequest('GET', '/bloggg');
    $response = $app->run($request);
    $this->assertEquals('<h1>Erreur 404</h1>', $response->getBody());
  }
}
