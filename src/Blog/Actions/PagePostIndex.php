<?php

namespace App\Blog\Actions;

use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class PagePostIndex
{

    /**
     * @var RendererInterface
     */
    private RendererInterface $renderer;

    /**
     * @var PostRepository
     */
    private PostRepository $postRepository;


    use RouterAwareAction;


    private $categoryReposit;

    public function __construct(RendererInterface $renderer, PostRepository $postRepository, Router $router, CategoryRepository $categoryReposit)
    {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->router = $router;
        $this->categoryReposit = $categoryReposit;
    }

    public function __invoke(Request $request)
    {
        if ($request->getAttribute('slug')) {
            return $this->show($request);
        }
        return $this->index($request);
    }


    public function index(Request $request)
    {
        $params = $request->getQueryParams();
        $posts = $this->postRepository->findPaginatedPublic(12, $params['p'] ?? 1);
        $categories = $this->categoryReposit->findAll();
        return $this->renderer->render('@blog/index', [
            'posts' => $posts,
            'categories' => $categories
        ]);
    }

    public function show(Request $request)
    {
        $id = $request->getAttribute('id');
        $post = $this->postRepository->findWithCategory($id);
        $slug = $request->getAttribute('slug');

        if ($slug !== $post->getSlug()) {
            return $this->redirect('blog_show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
