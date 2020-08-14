<?php

namespace App\Basket;

use App\Shop\Model\Product;
use Framework\Session\SessionInterface;

class SessionBasket extends Basket
{

    private $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $rows = $session->get('basket', []);
        $this->rows = array_map(function ($row) {
            $r = new BasketRow();
            $r->setProductId($row['id']);
            $r->setQuantity($row['quantity']);
            return $r;
        }, $rows);
    }

    public function addProduct(Product $product, ?int $quantity = null): void
    {
        parent::addProduct($product, $quantity);
        $this->persist();
    }

    /**
     * removeProduct
     *
     * @param  mixed $product
     * @return void
     */
    public function removeProduct(Product $product): void
    {
        parent::removeProduct($product);
        $this->persist();
    }


    private function persist()
    {
        $this->session->set('basket', $this->serialize());
    }


    private function serialize(): array
    {
        return array_map(function (BasketRow $row) {
            return [
                'id' => $row->getProductId(),
                'quantity' => $row->getQuantity()
            ];
        }, $this->rows);
    }
}
