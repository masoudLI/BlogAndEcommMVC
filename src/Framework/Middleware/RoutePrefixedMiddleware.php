<?php

namespace Framework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutePrefixedMiddleware implements MiddlewareInterface
{
    private $container;
    private $routePrefix;
    private $middleware;

    public function __construct(ContainerInterface $container, $routePrefix, $middleware)
    {
        $this->container = $container;
        $this->routePrefix = $routePrefix;
        $this->middleware = $middleware;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, $this->routePrefix) === 0) {
            return $this->container->get($this->middleware)->process($request, $handler);
        }
        return $handler->handle($request);
    }
}
