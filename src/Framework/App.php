<?php

declare(strict_types=1);

namespace Framework;

use App\Error\ModuleError;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @var string[] tableaux de module
     */
    private array $modules = [];

    /**
     * @param string[] module list a charger
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $parsedBody = (array)$request->getParsedBody();
        if (array_key_exists('_method', $parsedBody) &&
            in_array($parsedBody['_method'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        if (empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
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
        }
    }

    /**
     * Get the value of container
     *
     * @return  ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
