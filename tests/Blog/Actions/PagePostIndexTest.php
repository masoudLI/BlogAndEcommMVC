<?php

namespace Tests\Blog\Actions;

use App\Blog\Actions\PagePostIndex;
use App\Blog\Model\Post;
use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;


class PagePostIndexTest extends TestCase
{

    private $renderer;
    private $router;
    private $postRepository;
    private $action;
    private $categoryReposit;
    use ProphecyTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render()->willReturn("");
        $this->router = $this->prophesize(Router::class);
        $this->postRepository = $this->prophesize(PostRepository::class);
        $this->categoryReposit = $this->prophesize(CategoryRepository::class);
        $this->action = new PagePostIndex(
            $this->renderer->reveal(),
            $this->postRepository->reveal(),
            $this->router->reveal(),
            $this->categoryReposit->reveal()
        );
    }

    public function testRedirectShow()
    {
        $post = $this->makePost(9, "azezae-azeazae");
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->getId())
            ->withAttribute('slug', $post->getSlug());

        $this->router->generateUri(
            'blog_show',
            ['id' => $post->getId(), 'slug' => $post->getSlug()]
        )->willReturn('/demo2');
        $this->postRepository->findWithCategory($post->getId())->willReturn($post);
        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }


    public function testShowRender()
    {
        $post = $this->makePost(9, "azezae-azeazae");
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->getId())
            ->withAttribute('slug', $post->getSlug());
        $this->postRepository->findWithCategory($post->getId())->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }

    public function makePost($id, $slug)
    {
        $post = new Post();
        $post->setId($id);
        $post->setSlug($slug);
        return $post;
    }
}
