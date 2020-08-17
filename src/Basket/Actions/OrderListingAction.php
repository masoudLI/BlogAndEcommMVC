<?php

namespace App\Basket\Actions;

use App\Basket\Repository\OrderRepository;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderListingAction
{
    private $renderer;

    private $orderRepository;

    private $auth;

    public function __construct(RendererInterface $renderer, OrderRepository $orderRepository, Auth $auth)
    {
        $this->renderer = $renderer;
        $this->orderRepository = $orderRepository;
        $this->auth = $auth;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        $orders = $this->orderRepository->findForUser($this->auth->getUser())->paginate(10, $params['p'] ?? 1);
        $this->orderRepository->findRows($orders);
        return $this->renderer->render('@basket/orders', [
            'orders' => $orders
        ]);
    }
}
