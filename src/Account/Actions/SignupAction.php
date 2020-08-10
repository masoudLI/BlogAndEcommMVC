<?php

namespace App\Account\Actions;

use App\Auth\DatabaseAuth;
use App\Auth\Model\User;
use App\Auth\Repository\UserRepository;
use Framework\Auth;
use Framework\Database\Hydrator;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class SignupAction
{
    private $renderer;

    private $auth;

    private $userRepository;

    private $flashService;

    private $router;

    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, UserRepository $userRepository, FlashService $flashService, Router $router)
    {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->userRepository = $userRepository;
        $this->flashService = $flashService;
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@account/signup');
        }

        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->required('username', 'email', 'password', 'password_confirm')
            ->length('username', 5)
            ->email('email')
            ->confirm('password')
            ->length('password', 4)
            ->unique('username', $this->userRepository)
            ->unique('email', $this->userRepository);

        if ($validator->isValid()) {
            $userParams = [
                'username' => $params['username'],
                'email' => $params['email'],
                'password' => \password_hash($params['password'], PASSWORD_DEFAULT)
            ];
            $this->userRepository->insert($userParams);
            $user = Hydrator::hydrate($userParams, User::class);
            $user->setId($this->userRepository->getPdo()->lastInsertId());
            $this->auth->setUser($user);
            $this->flashService->success('Votre compte a bien été créé');
            return new RedirectResponse($this->router->generateUri('account_profile'));
        }
        $errors = $validator->getErrors();
        return $this->renderer->render('@account/signup', [
            'errors' => $errors,
            'user'   => [
                'username' => $params['username'],
                'email'    => $params['email']
            ]
        ]);
    }
}
