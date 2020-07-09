<?php declare(strict_types=1);

namespace App\Blog;

use App\Blog\Actions\BlogAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    private RendererInterface $renderer;


    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get('blog', $prefix, BlogAction::class, []);
        $router->get('blog_show', $prefix . '/{slug}', BlogAction::class, [
        'slug' => '[a-z\-0-9]+'
        ]);
    }
}
