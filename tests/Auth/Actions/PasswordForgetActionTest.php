<?php

namespace Tests\Auth\Actions;

use App\Auth\Actions\PasswordForgetAction;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\Model\User;
use App\Auth\Repository\UserRepository;
use Framework\Exceptions\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Tests\ActionTestCase;

class PasswordForgetActionTest extends ActionTestCase
{

    private $renderer;
    private $action;
    private $userTable;
    private $mailer;
    private $flash;

    use ProphecyTrait;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->userTable = $this->prophesize(UserRepository::class);
        $this->mailer = $this->prophesize(PasswordResetMailer::class);
        $this->flash = $this->prophesize(FlashService::class);
        $this->action = new PasswordForgetAction(
            $this->renderer->reveal(),
            $this->userTable->reveal(),
            $this->mailer->reveal(),
            $this->flash->reveal()
        );
    }


    public function testEmailInvalid()
    {
        $this->renderer
            ->render(Argument::type('string'), Argument::that(function ($params) {
                $this->assertArrayHasKey('errors', $params);
                $this->assertEquals(['email'], array_keys($params['errors']));
                return true;
            }))
            ->shouldBeCalled()
            ->willReturnArgument();

        $response = \call_user_func($this->action, $this->makeRequest('/demo', [
            'email' => 'azeaze'
        ]));
        $this->assertEquals($response, '@auth/password');
    }

    public function testEmailDoseNotExist()
    {
        $this->userTable->findBy('email', 'massoud@yahoo.fr')->willThrow(NoRecordException::class);
        $this->renderer
            ->render(Argument::type('string'), Argument::withEntry('errors', Argument::withKey('email')))
            ->shouldBeCalled()
            ->willReturnArgument();

        $response = \call_user_func($this->action, $this->makeRequest('/demo', [
            'email' => 'massoud@yahoo.fr'
        ]));
        $this->assertEquals($response, '@auth/password');
    }

    public function testEmailValidPost()
    {
        $user = new User();
        $user->setId(3);
        $user->setEmail('massoud@yahoo.fr');
        $token = 'fake';
        $this->userTable->findBy('email', $user->getEmail())->willReturn($user);
        $this->userTable->resetPassword(3)->willReturn($token);
        $this->mailer->send($user->getEmail(), [
            'id' => $user->getId(),
            'token' => $token
        ])->shouldBeCalled();
        $this->renderer->render()->shouldNotBeCalled();
        $response = \call_user_func($this->action, $this->makeRequest('/demo', [
            'email' => 'massoud@yahoo.fr'
        ]));
        $this->assertRedirect($response, '/demo');
    }
}
