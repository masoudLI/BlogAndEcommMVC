<?php

namespace Framework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CombinedMiddleware implements MiddlewareInterface
{


    private ContainerInterface $container;

    /**
     * @param string[] module list a charger
     */
    private array $middlewares = [];


    public function __construct(ContainerInterface $container, array $middlewares)
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $handleMiddleware = new CombinedMiddlewareHandle($this->container, $this->middlewares, $handler);
        return $handleMiddleware->handle($request);
    }
}
