<?php declare(strict_types=1);

namespace Framework;

use Aura\Router\RouterContainer;
use Aura\Router\Route as AuraRoute;
use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Router
 */
class Router
{

  /**
   * @var RouterContainer
   */
    private $routerContainer;

  /**
   * @var \Aura\Router\Map
   */
    private $map;


    public function __construct()
    {
        $this->routerContainer = new RouterContainer();
        $this->map = $this->routerContainer->getMap();
    }


  /**
   * @param string $path
   * @param string|callable $callable
   * @param string $name
   * @param array|null $tokens
   */

    public function get(?string $name = null, string $path, $callable, ?array $tokens = [])
    {
        $this->map->get($name, $path, $callable)->tokens((array) $tokens);
    }



    public function match(ServerRequestInterface $request): ?Route
    {
        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($request);
        if (!$route) {
            return  null;
        }
        return new Route($route->name, $route->handler, $route->attributes);
    }

  /**
   * @param string $name
   * @param array $params
   * @param array $queryParams
   * @return null|string
   * @throws \Aura\Router\Exception\RouteNotFound
   */
    public function generateUri(string $name, array $params = []): string
    {
        return $this->routerContainer->getGenerator()->generate($name, $params);
    }
}
