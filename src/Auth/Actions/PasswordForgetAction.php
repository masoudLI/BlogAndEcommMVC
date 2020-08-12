<?php

namespace App\Auth\Actions;

use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\Repository\UserRepository;
use Framework\Exceptions\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;

class PasswordForgetAction
{
    /**
     * @var RendererInterface
     */
    private RendererInterface $renderer;


    private UserRepository $userRepository;


    private FlashService $flashService;

    private $mailer;

    public function __construct(
        RendererInterface $renderer,
        UserRepository $userRepository,
        PasswordResetMailer $mailer,
        FlashService $flashService
    ) {

        $this->renderer = $renderer;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->flashService = $flashService;
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@auth/password');
        }
        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->notEmpty('email')
            ->email('email');
        if ($validator->isValid()) {
            try {
                $user = $this->userRepository->findBy('email', $params['email']);
                $token = $this->userRepository->resetPassword((int)$user->getId());
                $this->mailer->send($user->getEmail(), [
                    'id' => $user->getId(),
                    'token' => $token
                ]);
                $this->flashService->success('Un email vous a été envoyé');
                return new RedirectResponse($request->getUri()->getPath());
            } catch (NoRecordException $th) {
                $errors = ['email' => 'Aucun utilisateur ne corresbonds à cet email'];
            }
        } else {
            $errors = $validator->getErrors();
        }
        return $this->renderer->render('@auth/password', [
            'errors' => $errors
        ]);
    }
}
