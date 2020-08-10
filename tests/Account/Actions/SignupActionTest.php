<?php

namespace Tests\Account\Actions;

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

class SignupActionTest extends ActionTestCase
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

        //PDO
        $pdo = $this->prophesize(PDO::class);
        $statement = $this->prophesize(PDOStatement::class);
        $pdo->prepare(Argument::any())->willReturn($statement->reveal());
        $pdo->lastInsertId()->willReturn(3);
        $statement->execute(Argument::any())->willReturn(false);
        $statement->fetchColumn()->willReturn(false);

        $this->userRepository->getTable()->willReturn('fake');
        $this->userRepository->getPdo()->willReturn($pdo->reveal());

        //Router

        $this->router = $this->prophesize(Router::class);
        $this->router->generateUri(Argument::any())->will(function ($args) {
            return $args[0];
        });

        // Renderer
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any(), Argument::any())->willReturn('');

        //FlashService
        $this->flashService = $this->prophesize(FlashService::class);

        // Auth
        $this->auth = $this->prophesize(DatabaseAuth::class);

        $this->action = new SignupAction(
            $this->renderer->reveal(),
            $this->auth->reveal(),
            $this->userRepository->reveal(),
            $this->flashService->reveal(),
            $this->router->reveal()
        );
    }

    public function testGet()
    {
        \call_user_func($this->action, $this->makeRequest('/inscription'));
        $this->renderer->render('@account/signup')->shouldHaveBeenCalled();
    }

    public function testPostInvalid()
    {
        call_user_func($this->action, $this->makeRequest('/demo', [
            'username' => 'massoud',
            'email' => 'azeaze',
            'password' => '0000',
            'password_confirm' => '000'
        ]));

        $this->renderer->render('@account/signup', Argument::that(function ($params) {
            $this->assertArrayHasKey('errors', $params);
            $this->assertEquals(['email', 'password'], array_keys($params['errors']));
            return true;
        }))->shouldHaveBeenCalled();
    }

    public function testPostValid()
    {
        $this->userRepository->insert(Argument::that(function (array $params) {
            $this->assertArrayHasKey('username', $params);
            $this->assertArrayHasKey('email', $params);
            $this->assertTrue(\password_verify('0000', $params['password']));
            return true;
        }))->shouldBeCalled();

        $this->auth->setUser(Argument::that(function(User $user) {
            $this->assertEquals('massoud', $user->getUsername());
            $this->assertEquals('massoud@yahoo.fr', $user->getEmail());
            $this->assertEquals(3, $user->getId());
            return true;
        }))->shouldBeCalled();

        $this->renderer->render()->shouldNotBeCalled();
        $this->flashService->success(Argument::type('string'))->shouldBeCalled();
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'username' => 'massoud',
            'email' => 'massoud@yahoo.fr',
            'password' => '0000',
            'password_confirm' => '0000'
        ]));
        $this->assertRedirect($response, 'account_profile');
    }
}
