<?php

namespace Framework\Renderer;

class PHPRenderer implements RendererInterface
{

    private array $paths = [];

    private array $globals = [];

    const DEFAULT_NAMESPACE = '__MAIN';


    public function __construct(?string $defaulltPath = null)
    {
        if (!is_null($defaulltPath)) {
            $this->addPath($defaulltPath);
        }
    }


    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        }
        $this->paths[$namespace] = $path;
    }

    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    private function replaceNamespace($view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
