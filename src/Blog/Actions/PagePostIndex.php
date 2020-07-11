<?php

namespace App\Blog\Actions;

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


    public function __construct(RendererInterface $renderer, PostRepository $postRepository, Router $router)
    {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->router = $router;
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
        $posts = $this->postRepository->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/index', [
            'posts' => $posts
        ]);
    }

    public function show(Request $request)
    {
        $id = $request->getAttribute('id');
        $post = $this->postRepository->find($id);
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
