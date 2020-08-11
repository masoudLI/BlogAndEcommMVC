<?php

declare(strict_types=1);

namespace Framework;

use Framework\Middleware\CombinedMiddleware;
use Framework\Middleware\RoutePrefixedMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class App implements Handler
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string tableaux de module
     */
    private string $definitions;

    /**
     * @param string[] module list a charger
     */
    private array $modules = [];


    /**
     * @param string[] module list a charger
     */
    private array $middlewares = [];



    private int $index = 0;


    public function __construct(string $definitions)
    {
        $this->definitions = $definitions;
    }


    /**
     * addModule
     *
     * @param  string $module
     * @return self
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }


    /**
     * pipe
     *
     * @param  $middleware
     * @return self
     */
    public function pipe(string $routePrefix, ?object $middleware = null): self
    {
        if ($middleware  === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->container, $routePrefix, $middleware);
        }
        return $this;
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->index++;
        if ($this->index > 1) {
            throw new \Exception();
        }
        $middleware = new CombinedMiddleware($this->container, $this->middlewares);
        return $middleware->process($request, $this);
    }


    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
        /*  $parsedBody = (array)$request->getParsedBody();
        if (array_key_exists('_method', $parsedBody) &&
            in_array($parsedBody['_method'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        $route = $this->container->get(Router::class)->match($request);
        if ($route === null) {
            return new Response(404, [], (new ModuleError($this->container))->error());
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        $callable = $route->getCallback();
        if (is_string($callable)) {
            $callable = $this->container->get($callable);
        }
        $response = call_user_func_array($callable, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception("The response is not string Or an instance of ResponseInterface");
        } */
    }

    /**
     * Get the value of container
     *
     * @return  ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new \DI\ContainerBuilder();
            $builder->addDefinitions($this->definitions);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    /**
     * Get the value of modules
     */
    public function getModules()
    {
        return $this->modules;
    }
}
