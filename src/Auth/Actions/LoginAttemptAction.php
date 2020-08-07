<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use App\Blog\Repository\CategoryRepository;
use App\Blog\Repository\PostRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAttemptAction
{

    /**
     * @var RendererInterface
     */
    private RendererInterface $renderer;


    use RouterAwareAction;


    private $databaseAuth;

    private $session;

    public function __construct(RendererInterface $renderer, Router $router, DatabaseAuth $databaseAuth, SessionInterface $session)
    {
        $this->renderer = $renderer;
        $this->router = $router;
        $this->databaseAuth = $databaseAuth;
        $this->session = $session;
    }

    public function __invoke(Request $request)
    {
        $params = $request->getParsedBody();
        $user = $this->databaseAuth->login($params['password'], $params['username']);
        if ($user) {
            $path = $this->session->get('auth_redirect') ?: $this->router->generateUri('blog_admin_posts_index');
            $this->session->clear('auth_redirect');
            return new RedirectResponse($path);
        } else {
            (new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');
            return $this->redirect('auth_login');
        }
    }
}
