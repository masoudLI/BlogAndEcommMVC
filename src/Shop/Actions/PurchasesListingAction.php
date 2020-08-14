<?php

namespace App\Shop\Actions;

use App\Shop\Repository\PurchaseRepository;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class PurchasesListingAction
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
        $purchases = $this->purchasesRepository->findForUsePurchase($this->auth->getUser());
        return $this->renderer->render('@shop/commandes', [
            'purchases' => $purchases
        ]);
    }
}
