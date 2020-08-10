<?php

namespace Framework\Middleware;

use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatcherMiddleware implements MiddlewareInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(Router\Route::class);
        if ($route === null) {
            return $handler->handle($request);
        }
        $callable = $route->getCallback();
        if (!is_array($callable)) {
            $callable = [$callable];
        }
        return (new CombinedMiddleware($this->container, $callable))->process($request, $handler);
    }
}
