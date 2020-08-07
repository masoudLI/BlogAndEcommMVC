<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogoutAction
{

    /**
     * @var RendererInterface
     */
    private RendererInterface $renderer;


    private $auth;

    private $session;

    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, SessionInterface $session)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->session = $session;
    }

    public function __invoke(Request $request)
    {
        $this->auth->logout();
        (new FlashService($this->session))->success('Vous vous etes deconnecté');
        return new RedirectResponse('/');
    }
}
