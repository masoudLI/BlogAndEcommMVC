<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PagePostIndex;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class BlogModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';
    const MIGRATIONS  = __DIR__ . '/db/migrations';
    const SEEDS  = __DIR__ . '/db/seeds';

    public function __construct(string $prefix, ContainerInterface $container)
    {
        $renderer = $container->get(RendererInterface::class);
        $router = $container->get(Router::class);
        $renderer->addPath('blog', __DIR__ . '/views');
        $router->get('blog_index', $prefix, PagePostIndex::class, []);
        $router->get('blog_show', $prefix . '/{slug}-{id}', PagePostIndex::class, [
            'slug' => '[a-z\-0-9]+',
            'id' => '[0-9]+'
        ]);

        if ($container->has('admin')) {
            $prefixAdmin = $container->get('admin');
            $router->get('blog_admin_index', $prefixAdmin, PostCrudAction::class, []);
            $router->get('blog_admin_edit', $prefixAdmin . '/edit/{id}', PostCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->post(null, $prefixAdmin . '/edit/{id}', PostCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->get('blog_admin_create', $prefixAdmin . '/create', PostCrudAction::class, []);
            $router->post(null, $prefixAdmin . '/create', PostCrudAction::class, []);
            $router->delete('blog_admin_delete', $prefixAdmin . '/delete/{id}', PostCrudAction::class, [
                'id' => '[0-9]+'
            ]);
        }
    }
}
