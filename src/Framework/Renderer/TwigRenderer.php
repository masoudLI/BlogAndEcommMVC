<?php

namespace Framework\Renderer;

use Twig\Environment;

class TwigRenderer implements RendererInterface
{

  /**
   * @var Environment
   */
    private $twig;

  
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath($path, $namespace);
    }

    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
