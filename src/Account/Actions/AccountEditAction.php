<?php

namespace App\Account\Actions;

use App\Auth\Model\User;
use App\Auth\Repository\UserRepository;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class AccountEditAction
{

    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var Auth
     */
    private $auth;

    private UserRepository $userRepository;

    private $flashService;

    public function __construct(
        RendererInterface $renderer,
        Auth $auth,
        UserRepository $userRepository,
        FlashService $flashService
    ) {

        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->userRepository = $userRepository;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $user = $this->auth->getUser();
        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->confirm('password')
            ->required('firstname', 'lastname')
            ->notEmpty('firstname', 'lastname');
        if ($validator->isValid()) {
            $userParams = [
                'firstname' => $params['firstname'],
                'lastname'  => $params['lastname'],
            ];
            if (!empty($params['password'])) {
                $userParams['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            }
            /** @var User $user */
            $this->userRepository->update($user->getId(), $userParams);
            $this->flashService->success('Votre compte a bien été mis à jour');
            return new RedirectResponse($request->getUri()->getPath());
        }
        $errors = $validator->getErrors();
        return $this->renderer->render('@account/account', compact('user', 'errors'));
    }
}
