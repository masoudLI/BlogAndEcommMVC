<?php

namespace App\Auth;

use App\Auth\Model\User;
use App\Auth\Repository\UserRepository;
use Framework\Auth;
use Framework\Session\SessionInterface;

class DatabaseAuth implements Auth
{
    private $userRepository;

    private $session;

    private $user;

    public function __construct(UserRepository $userRepository, SessionInterface $session)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function login(string $username, string $password)
    {
        if (empty($username) || empty($password)) {
            return null;
        }
        /** @var User */
        $user = $this->userRepository->findBy('username', $username);
        if ($user && \password_verify($password, $user->getPassword())) {
            $this->setUser($user);
            return $user;
        }

        return null;
    }

    public function logout(): void
    {
        $this->session->clear('auth_user');
    }

    public function getUser(): ?User
    {
        if ($this->user) {
            return $this->user;
        }
        $userId = $this->session->get('auth_user');
        if ($userId) {
            try {
                $this->user = $this->userRepository->find($userId);
                return $this->user;
            } catch (\Throwable $th) {
                $this->session->clear('auth_user');
                return null;
            }
        }
        return null;
    }

    public function setUser(User $user)
    {
        $this->session->set('auth_user', $user->getId());
        $this->user = $user;
    }
}
