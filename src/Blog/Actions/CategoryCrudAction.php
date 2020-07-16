<?php

namespace App\Blog\Actions;

use Framework\Router;
use App\Blog\Model\Post;
use Framework\Actions\CrudAction;
use Framework\Session\FlashService;
use Framework\Renderer\RendererInterface;
use App\Blog\Repository\CategoryRepository;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryCrudAction extends CrudAction
{

    protected $viewPath = "@blog/admin/categories";

    protected $routePrefix = "blog_admin_category";

    public function __construct(
        RendererInterface $renderer,
        CategoryRepository $categoryRepository,
        Router $router,
        FlashService $flash
    ) {
        parent::__construct($renderer, $categoryRepository , $router, $flash);
    }

    protected function prePersist(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);

    }

    protected function getValidator(Request $request)
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->slug('slug');
    }
}
