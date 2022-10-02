<?php

namespace Framework\Auth;

use Framework\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoleMiddleware implements MiddlewareInterface
{

    private Auth $auth;

    private $role;

    public function __construct(Auth $auth, $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->getUser();
        if ($user === null || !in_array($this->role, $user->getRoles())) {
            throw new BadRoleException();
        }
        return $handler->handle($request);
    }
}
