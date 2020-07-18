<?php

declare(strict_types=1);

namespace Framework;

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
    private string $configDefinition;

    /**
     * @param string[] module list a charger
     */
    private array $modules = [];


    /**
     * @param string[] module list a charger
     */
    private array $middlewares = [];


    private int $index = 0;


    public function __construct(string $configDefinition)
    {
        $this->configDefinition = $configDefinition;
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
     * @param  string $middleware
     * @return self
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if ($middleware === null) {
            throw new \Exception();
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
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
     * @return object
     */
    private function getMiddleware(): ?object
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
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
            $builder->addDefinitions($this->configDefinition);
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
