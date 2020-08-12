<?php

namespace App\Auth\Actions;

use App\Auth\Entity\User;
use App\Auth\Repository\UserRepository;
use App\Auth\Table\UserTable;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PasswordResetAction
{

    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var UserRepository
     */
    private $userTable;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(
        RendererInterface $renderer,
        UserRepository $userTable,
        FlashService $flashService,
        Router $router
    ) {

        $this->renderer = $renderer;
        $this->userTable = $userTable;
        $this->router = $router;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /** @var User $user */
        $user = $this->userTable->find($request->getAttribute('id'));
        if ($user->getPasswordReset() !== null &&
            $user->getPasswordReset() === $request->getAttribute('token') &&
            time() - $user->getPasswordResetAt()->getTimestamp() < 600
        ) {
            if ($request->getMethod() === 'GET') {
                return $this->renderer->render('@auth/reset');
            } else {
                $params = $request->getParsedBody();
                $validator = (new Validator($params))
                    ->length('password', 4)
                    ->confirm('password');
                if ($validator->isValid()) {
                    $this->userTable->updatePassword($user->getId(), $params['password']);
                    $this->flashService->success('Votre mot de passe a bien été changé');
                    return new RedirectResponse($this->router->generateUri('auth_login'));
                } else {
                    $errors = $validator->getErrors();
                    return $this->renderer->render('@auth/reset', compact('errors'));
                }
            }
        } else {
            $this->flashService->error('Token invalid');
            return new RedirectResponse($this->router->generateUri('auth_password'));
        }
    }
}
