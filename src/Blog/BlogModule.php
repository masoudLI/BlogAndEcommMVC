<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
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


        $router->get('blog_category_index', $prefix . '/category/{slug}-{id}', CategoryShowAction::class, [
            'slug' => '[a-z\-0-9]+',
            'id' => '[0-9]+'
        ]);


        if ($container->has('admin_prefix')) {
            $prefixAdmin = $container->get('admin_prefix');
            $router->get('blog_admin_posts_index', $prefixAdmin , PostCrudAction::class, []);
            $router->get('blog_admin_posts_edit', $prefixAdmin . '/posts/edit/{id}', PostCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->post(null, $prefixAdmin . '/posts/edit/{id}', PostCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->get('blog_admin_posts_create', $prefixAdmin . '/posts/create', PostCrudAction::class, []);
            $router->post(null, $prefixAdmin . '/posts/create', PostCrudAction::class, []);
            $router->delete('blog_admin_posts_delete', $prefixAdmin . '/posts/delete/{id}', PostCrudAction::class, [
                'id' => '[0-9]+'
            ]);

            // categories

            $router->get('blog_admin_category_index', $prefixAdmin . '/category', CategoryCrudAction::class, []);
            $router->get('blog_admin_category_edit', $prefixAdmin . '/category/edit/{id}', CategoryCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->post(null, $prefixAdmin . '/category/edit/{id}', CategoryCrudAction::class, [
                'id' => '[0-9]+'
            ]);
            $router->get('blog_admin_category_create', $prefixAdmin . '/category/create', CategoryCrudAction::class, []);
            $router->post(null, $prefixAdmin . '/category/create', CategoryCrudAction::class, []);
            $router->delete('blog_admin_category_delete', $prefixAdmin . '/category/delete/{id}', CategoryCrudAction::class, [
                'id' => '[0-9]+'
            ]);
        }
    }
}
