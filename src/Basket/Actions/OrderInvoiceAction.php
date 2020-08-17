<?php

namespace App\Basket\Actions;

use App\Auth\Model\User;
use App\Basket\Model\Order;
use App\Basket\Repository\OrderRepository;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderInvoiceAction
{

    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var OrderRepository
     */
    private $orderTable;

    public function __construct(
        RendererInterface $renderer,
        OrderRepository $orderTable,
        Auth $auth
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->orderTable = $orderTable;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /** @var Order $order */
        $order = $this->orderTable->find($request->getAttribute('id'));
        $this->orderTable->findRows([$order]);
        /** @var User */
        $user = $this->auth->getUser();
        if ($user->getId() !== $order->getUserId()) {
            throw new Auth\ForbiddenException('Vous ne pouvez pas télécharger cette facture');
        }
        return $this->renderer->render('@basket/invoice', compact('order', 'user'));
    }
}
