<?php

namespace App\Shop\Actions;

use App\Auth\Model\User;
use App\Shop\Model\Purchase;
use App\Shop\Repository\PurchaseRepository;
use Framework\Auth;
use Framework\Auth\ForbiddenException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class InvoiceAction
{
    private $renderer;

    private $purchasesRepository;

    private $auth;

    public function __construct(RendererInterface $renderer, PurchaseRepository $purchasesRepository, Auth $auth)
    {
        $this->renderer = $renderer;
        $this->purchasesRepository = $purchasesRepository;
        $this->auth = $auth;
    }


    public function __invoke(ServerRequestInterface $request)
    {
        /** @var Purchase */
        $purchase = $this->purchasesRepository->findWithProduct($request->getAttribute('id'));
        /** @var User */
        $user = $this->auth->getUser();
        if ($user->getId() !== $purchase->getUserId()) {
            throw new ForbiddenException("Vous ne pouvez pas telecharger cette facture");
        }
        return $this->renderer->render('@shop/invoice', compact('purchase', 'user'));
    }
}
