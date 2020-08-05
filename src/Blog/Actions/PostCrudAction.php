<?php

namespace App\Blog\Actions;

use App\Blog\Model\Post;
use App\Blog\PostUploadImage;
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

    private $postUploadImage;

    private $repository;

    public function __construct(
        RendererInterface $renderer,
        PostRepository $repository,
        Router $router,
        FlashService $flash,
        CategoryRepository $categoryRepository,
        PostUploadImage $postUploadImage
    ) {
        parent::__construct($renderer, $repository, $router, $flash);
        $this->categoryRepository = $categoryRepository;
        $this->postUploadImage = $postUploadImage;
        $this->repository = $repository;
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryRepository->findList();
        return $params;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->setCreatedAt(new \DateTime());
        return $post;
    }

    protected function delete(Request $request)
    {
        $post = $this->repository->find($request->getAttribute('id'));
        $this->postUploadImage->delete($post->getImage());
        return parent::delete($request);
    }

    protected function prePersist(Request $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        // UPload les fichises
        $image = $this->postUploadImage->upload($params['image'], $post->getImage());
        if ($image) {
            $params['image'] = $image;
        } else {
            unset($params['image']);
        }
        $params = array_filter($params, function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id', 'image', 'published']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);
    }


    protected function getValidator(Request $request)
    {
        $validator = parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->exists('category_id', $this->categoryRepository->getTable(), $this->categoryRepository->getPdo())
            ->dateTime('created_at')
            ->extension('image', ['jpg', 'png', 'jpeg']);
        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
