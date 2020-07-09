<?php

namespace Framework\Renderer;

class TwigRenderer implements RendererInterface
{

  /**
   * @param \Twig\Loader\FilesystemLoader
   */
    private $loader;

  /**
   * @param \Twig\Environment
   */
    private $twig;

  
    public function __construct(string $path)
    {
        $this->loader = new \Twig\Loader\FilesystemLoader($path);
        $this->twig = new \Twig\Environment($this->loader, []);
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
