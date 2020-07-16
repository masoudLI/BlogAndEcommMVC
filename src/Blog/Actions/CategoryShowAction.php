<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;


class CategoryShowAction
{

    private $categoryReposit;
    /**
     * @var RendererInterface
     */
    private RendererInterface $renderer;

    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;


    use RouterAwareAction;

    public function __construct(RendererInterface $renderer, PostRepository $postRepository, Router $router, CategoryRepository $categoryReposit)

    {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->router = $router;
        $this->categoryReposit = $categoryReposit;
    }

    public function __invoke(Request $request)
    {
        $category = $this->categoryReposit->findBy('slug', $request->getAttribute('slug'));
        $params = $request->getQueryParams();
        $posts = $this->postRepository->findPaginatedPublicForCategory(12, $params['p'] ?? 1, $category->getId());
        $categories = $this->categoryReposit->findAll();
        return $this->renderer->render('@blog/category/index', [
            'posts' => $posts,
            'category' => $category,
            'categories' => $categories
        ]);
    }
}
