<?php

namespace Tests\Framework\Auth;

use App\Auth\Model\User;
use Framework\Auth;
use Framework\Auth\BadRoleException;
use Framework\Auth\ForbiddenException;
use Framework\Auth\RoleMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Server\RequestHandlerInterface;

class RoleMiddlewareTest extends TestCase
{

    use ProphecyTrait; 

    private $auth;

    protected function setUp(): void
    {
        $this->auth = $this->prophesize(Auth::class);
        $this->middleware = new RoleMiddleware(
            $this->auth->reveal(),
            'admin'
        );
    }

    public function testUnAutentictedUser()
    {
        $this->auth->getUser()->willReturn(null);
        $this->expectException(BadRoleException::class);
        $this->middleware->process(new ServerRequest('GET', '/demo'), $this->makeHandler()->reveal());
    }

    public function testWithBadRole()
    {
        $user = $this->prophesize(User::class);
        $user->getRoles()->willReturn(['user']);
        $this->auth->getUser()->willReturn($user->reveal());
        $this->expectException(BadRoleException::class);
        $this->middleware->process(new ServerRequest('GET', '/demo'), $this->makeHandler()->reveal());
    }

    public function testGoodRole()
    {
        $user = $this->prophesize(User::class);
        $user->getRoles()->willReturn(['admin']);
        $this->auth->getUser()->willReturn($user->reveal());
        $handler = $this->makeHandler();
        $handler
            ->handle(Argument::any())
            ->shouldBeCalled()
            ->willReturn(new Response());

        $this->middleware->process(new ServerRequest('GET', '/demo'), $handler->reveal());
    }

    public function makeHandler()
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle(Argument::any())->willReturn(new Response());
        return $handler;
    }
}
