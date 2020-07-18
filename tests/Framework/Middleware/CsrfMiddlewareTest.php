<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddlewareTest extends TestCase
{
    private $middleware;

    private $session;

    public function setUp(): void
    {
        parent::setUp();
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
    }


    public function testRequestPass()
    {
        $handeler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handeler->expects($this->once())
            ->method('handle');

        $request = (new ServerRequest('GET', '/demo'));

        $this->middleware->process($request, $handeler);
    }

    public function testPostRequestWithOutCrsfMiddleware()
    {
        $handeler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handeler->expects($this->never())
            ->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $this->expectException(\Exception::class);
        $this->middleware->process($request, $handeler);
    }

    public function testPassRequestWithToken()
    {
        $handeler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handeler->expects($this->once())->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);

        $this->middleware->process($request, $handeler);
    }

    public function testPostRequestWithInvalideCrsf()
    {
        $handeler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handeler->expects($this->never())
            ->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => 'azeaez']);
        $this->expectException(\Exception::class);
        $this->middleware->process($request, $handeler);
    }

    public function testPostRequestWithInvalideCrsfOnceAppelle()
    {
        $handeler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handeler->expects($this->once())
            ->method('handle');

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $handeler);
        $this->expectException(\Exception::class);
        $this->middleware->process($request, $handeler);
    }

    public function testLimitTheTokenNumber()
    {
        for ($i = 0; $i < 100; ++$i) {
            $token = $this->middleware->generateToken();
        }
        $this->assertCount(50, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }
}
