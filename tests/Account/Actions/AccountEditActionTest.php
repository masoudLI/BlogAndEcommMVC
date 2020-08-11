<?php

namespace Tests\Account\Actions;

use App\Account\Actions\AccountEditAction;
use App\Account\Actions\SignupAction;
use App\Auth\DatabaseAuth;
use App\Auth\Model\User;
use App\Auth\Repository\UserRepository;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use PDO;
use PDOStatement;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Tests\ActionTestCase;

class AccountEditActionTest extends ActionTestCase
{

    private $renderer;

    private $auth;

    private $userRepository;

    private $flashService;

    private $router;

    use ProphecyTrait;

    protected function setUp(): void
    {
        //userRepository
        $this->userRepository = $this->prophesize(UserRepository::class);
        //Router
        // Renderer
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any(), Argument::any())->willReturn('');

        //FlashService
        $this->flashService = $this->prophesize(FlashService::class);

        // Auth
        $this->auth = $this->prophesize(DatabaseAuth::class);

        $this->action = new AccountEditAction(
            $this->renderer->reveal(),
            $this->auth->reveal(),
            $this->userRepository->reveal(),
            $this->flashService->reveal()
        );
    }


    public function testValid()
    {

        $this->userRepository->update(3, [
            'firstname' => 'massoud',
            'lastname' => 'emami'
        ])->shouldBeCalled();
        $response = \call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname' => 'massoud',
            'lastname' => 'emami'
        ]));

        $this->assertRedirect($response, '/demo');
    }

    public function testValidWithPassword()
    {

        $this->userRepository->update(3, Argument::that(function ($params) {
            $this->assertEquals(['firstname', 'lastname', 'password'], array_keys($params));
            return true;
        }))->shouldBeCalled();

        $response = \call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname' => 'massoud',
            'lastname' => 'emami',
            'password' => '0000',
            'password_confirm' => '0000'
        ]));

        $this->assertRedirect($response, '/demo');
    }
}
