<?php

namespace App\Blog\Actions;

use App\Blog\Model\Post;
use App\Blog\Repository\CategoryRepository;
use Framework\Router;
use Framework\Actions\CrudAction;
use Framework\Session\FlashService;
use App\Blog\Repository\PostRepository;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{

    protected $viewPath = "@blog/admin/posts";

    protected $routePrefix = "blog_admin_posts";

    private $categoryRepository;

    public function __construct(
        RendererInterface $renderer,
        PostRepository $postRepository,
        Router $router,
        FlashService $flash,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($renderer, $postRepository, $router, $flash);
        $this->categoryRepository = $categoryRepository;
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryRepository->findList();
        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->setCreated_at(new \DateTime());
        return $post;
    }

    protected function prePersist(Request $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'cretaed_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);
    }


    protected function getValidator(Request $request)
    {
        return parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->exists('category_id', $this->categoryRepository->getTable(), $this->categoryRepository->getPdo())
            ->dateTime('created_at');
    }
}
