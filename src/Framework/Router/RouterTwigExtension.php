<?php

namespace Framework\Router;

use Framework\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterTwigExtension extends AbstractExtension
{

    /**
     * @var Router
     */
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_subpath', [$this, 'isSubPath'])
        ];
    }

    public function pathFor(string $path, array $params = [])
    {
        return $this->router->generateUri($path, $params);
    }

    public function isSubPath(string $path)
    {
        $uri = $_SERVER['REQUEST_URI'] ?: '/';
        $expected = $this->router->generateUri($path);
        return \strpos($uri, $expected) !== false;
        
    }
}
