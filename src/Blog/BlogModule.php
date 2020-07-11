<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Actions\PagePostIndex;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS  = __DIR__ . '/db/migrations';
    const SEEDS  = __DIR__ . '/db/seeds';

    private RendererInterface $renderer;


    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get('blog_index', $prefix, PagePostIndex::class, []);
        $router->get('blog_show', $prefix . '/{slug}-{id}', PagePostIndex::class, [
            'slug' => '[a-z\-0-9]+',
            'id' => '[0-9]+'
        ]);
    }
}
