<?php declare(strict_types=1);

namespace App\Blog;

use Framework\Renderer\PHPRenderer;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule
{



    public function __construct(Router $router, PHPRenderer $renderer)
    {
        $router->get('blog', '/blog', [$this, 'index'], []);
        $router->get('blog_show', '/blog/{slug}', [$this, 'show'], [
        'slug' => '[a-z\-0-9]+'
        ]);
    }


    public function index()
    {
        return 'Bonjour tout le moaaaa';
    }

    public function show(ServerRequestInterface $request)
    {
        return 'Bienvenu sur l\'article' . $request->getAttribute('slug');
    }
}
