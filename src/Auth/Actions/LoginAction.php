<?php

namespace App\Auth\Actions;

use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction
{

    /**
     * @var RendererInterface
     */
    private RendererInterface $renderer;


    use RouterAwareAction;


    public function __construct(RendererInterface $renderer, Router $router)
    {
        $this->renderer = $renderer;
        $this->router = $router;
    }

    public function __invoke(Request $request)
    {
        return $this->renderer->render('@auth/login');
    }
}
