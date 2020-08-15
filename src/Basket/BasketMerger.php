<?php


namespace App\Basket;

use App\Auth\Event\LoginEvent;
use App\Basket\Repository\BasketRepository;

class BasketMerger
{

    /**
     * @var SessionBasket
     */
    private $basket;
    /**
     * @var BasketRepository
     */
    private $basketTable;

    public function __construct(SessionBasket $basket, BasketRepository $basketTable)
    {
        $this->basket = $basket;
        $this->basketTable = $basketTable;
    }

    public function __invoke(LoginEvent $event)
    {
        /** @var User */
        $user = $event->getTarget();
        (new DatabaseBasket($user->getId(), $this->basketTable))->merge($this->basket);
        $this->basket->empty();
    }
}
