<?php

namespace App\Error;

use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class ModuleError
{

    private RendererInterface $renderer;

    public function __construct(ContainerInterface $container)
    {
        $this->renderer = $container->get(RendererInterface::class);
        $router = $container->get(Router::class);
        $this->renderer->addPath('error', __DIR__ . '/views');
        $router->get(null, null, [$this, 'error'], []);
    }

    public function error()
    {
        return $this->renderer->render('@error/error');
    }
}
