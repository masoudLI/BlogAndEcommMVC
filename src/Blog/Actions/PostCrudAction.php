<?php

namespace App\Blog\Actions;

use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction
{
    private $renderer;

    private $postRepository;

    private array $paramsAccepets = ['name', 'slug', 'content'];

    use RouterAwareAction;

    private $router;

    public function __construct(RendererInterface $renderer, PostRepository $postRepository, Router $router)
    {
        $this->renderer = $renderer;
        $this->postRepository = $postRepository;
        $this->router = $router;
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        if (substr((string)$request->getUri(), -6) === 'create') {
            return $this->create($request);
        }
        return $this->index($request);
    }


    public function index(Request $request)
    {
        $params = $request->getQueryParams();
        $items = $this->postRepository->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/admin/posts/index', [
            'items' => $items
        ]);
    }

    public function create(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->prePersist($request);
            $params = array_merge($params, [
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->postRepository->insert($params);
            return $this->redirect('blog_admin_index');
        }
        return $this->renderer->render('@blog/admin/posts/create');
    }

    public function edit(Request $request)
    {
        $id = (int)$request->getAttribute('id');
        $item = $this->postRepository->find($id);
        if ($request->getMethod() === 'POST') {
            $params = $this->prePersist($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->postRepository->update($id, $params);
            return $this->redirect('blog_admin_index');
        }
        return $this->renderer->render('@blog/admin/posts/edit', [
            'item' => $item
        ]);
    }

    public function delete(Request $request)
    {
        $this->postRepository->delete($request->getAttribute('id'));
        return $this->redirect('blog_admin_index');
    }

    /**
     * Filtre les paramètres reçu par la requête
     *
     * @param  Request $request
     * @return array
     */
    public function prePersist(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
