<?php declare(strict_types=1);

namespace App\Blog;

use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule
{


    private $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        //dd($renderer);
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $router->get('blog', '/blog', [$this, 'index'], []);
        $router->get('blog_show', '/blog/{slug}', [$this, 'show'], [
        'slug' => '[a-z\-0-9]+'
        ]);
    }

    public function index()
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(ServerRequestInterface $request)
    {
        return $this->renderer->render('@blog/show');
    }
}
