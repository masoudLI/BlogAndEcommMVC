<?php

namespace App\Basket\Actions;

use App\Basket\Basket;
use App\Basket\PurchaseBasket;
use App\Basket\Repository\OrderRepository;
use Framework\Actions\RouterAwareAction;
use Framework\Auth;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class OrderProcessAction
{

    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var purchaseBasket
     */
    private $purchaseBasket;
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var FlashService
     */
    private $flashService;

    use RouterAwareAction;

    private $basket;

    public function __construct(
        OrderRepository $orderRepository,
        PurchaseBasket $purchaseBasket,
        Auth $auth,
        FlashService $flashService,
        Basket $basket
    ) {
        $this->orderRepository = $orderRepository;
        $this->purchaseBasket = $purchaseBasket;
        $this->auth = $auth;
        $this->flashService = $flashService;
        $this->basket = $basket;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $stripeToken = $params['stripeToken'];
        $basket = $this->basket;
        $this->purchaseBasket->process($basket, $this->auth->getUser(), $stripeToken);
        $this->basket->empty();
        $this->flashService->success('Merci pour votre achat');
        return new RedirectResponse('/');
        
    }
}
