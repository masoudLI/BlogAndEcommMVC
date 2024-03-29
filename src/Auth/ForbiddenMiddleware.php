<?php

namespace App\Auth;

use Framework\Auth\BadRoleException;
use Framework\Auth\ForbiddenException;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ForbiddenMiddleware implements MiddlewareInterface
{

    private $loginPath;

    public function __construct($loginPath, SessionInterface $session)
    {
        $this->loginPath = $loginPath;
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ForbiddenException $e) {
            return $this->redirect($request);
        } catch (BadRoleException $e) {
            return $this->redirectProfile($request);
        }
        //throw $e;
    }

    private function redirectProfile($request)
    {
        $path = $request->getUri()->getPath();
        $this->session->set('auth_redirect', $path);
        (new FlashService($this->session))->error("Vous devez posséder un compte pour acceder a la page admin");
        return new RedirectResponse('/profile');
    }


    private function redirect($request)
    {
        $path = $request->getUri()->getPath();
        $this->session->set('auth_redirect', $path);
        (new FlashService($this->session))->error("Vous devez posséder un compte pour acceder a cette page");
        return new RedirectResponse($this->loginPath);
    }
}
