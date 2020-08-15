<?php

namespace App\Basket;

use App\Auth\Model\User;
use App\Basket\Repository\BasketRepository;
use Framework\Auth;
use Psr\Container\ContainerInterface;

class BasketFactory
{

    public function __invoke(ContainerInterface $container)
    {
        /** @var User $user */
        $user = $container->get(Auth::class)->getUser();
        if ($user) {
            return new DatabaseBasket($user->getId(), $container->get(BasketRepository::class));
        } else {
            return $container->get(SessionBasket::class);
        }
    }
}
