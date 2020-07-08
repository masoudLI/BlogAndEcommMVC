<?php declare(strict_types=1);

namespace Tests;

use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

  private $router;


  protected function setUp(): void
  {
    parent::setUp();
    $this->router = new Router();
  }


  public function testGetRouter()
  {
    $fake = function () {
      return 'hello';
    };
    $request = new ServerRequest('GET', '/blog');
    $this->router->get('blog', '/blog', $fake, []);
    $route = $this->router->match($request);
    $this->assertEquals('blog', $route->getName());
    $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
  }

  public function testGetMethodIfURLDoesNotExists()
  {
    $request = new ServerRequest('GET', '/blog');
    $this->router->get('blog', '/blogaz', function () {
      return 'hello';
    }, []);
    $route = $this->router->match($request);
    $this->assertEquals(null, $route);
  }

  public function testGetMethodWithParameter()
  {
    $fake = function () {
      return 'hello';
    };
    $request = new ServerRequest('GET', '/blog/mon-slug-8');
    $this->router->get('posts', '/blog', function () {
      return 'azeze';
    }, []);
    $this->router->get('posts_show', '/blog/{slug}-{id}', $fake, [
      'slug' => '[a-z\-0-9]+',
      'id' => '[0-9]+'
    ]);
    $route = $this->router->match($request);
    $this->assertEquals('posts_show', $route->getName());
    $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    $this->assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
  }

  public function getGenerateUri()
  {
    $fake = function () {
      return 'hello';
    };
    $this->router->get('posts', '/blog', function () {
      return 'azeze';
    }, []);
    $this->router->get('posts_show', '/blog/{slug}-{id}', $fake, [
      'slug' => '[a-z\-0-9]+',
      'id' => '[0-9]+'
    ]);

    $uri = $this->router->generateUri('posts_show', ['slug' => 'mon-article', 'id' => '18']);
    $this->assertEquals('/blog/mon-article-18', $uri);
  }
}
