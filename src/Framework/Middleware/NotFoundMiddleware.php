<?php

namespace Framework\Middleware;

use App\Error\ModuleError;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundMiddleware
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return new Response(404, [], (new ModuleError($this->container))->error());
    }
}
