<?php

namespace App\Basket;

use App\Basket\Model\Basket;
use App\Shop\Model\Product;
use App\Basket\Basket as BasketClass;
use App\Basket\Repository\BasketRepository;

class DatabaseBasket extends BasketClass
{


    private $userId;

    private $basketTable;

    private $basketEntity;

    public function __construct(int $userId, BasketRepository $basketTable)
    {
        $this->basketTable = $basketTable;
        $this->userId = $userId;
        /** @var Basket */
        $this->basketEntity = $this->basketTable->findUserForBasket($userId);
        if ($this->basketEntity) {
            $this->rows = $basketTable->findRows($this->basketEntity);
        }
    }

    /**
     * Ajoute un produit au panier
     * @param Product $product
     */
    public function addProduct(Product $product, ?int $quantity = null): void
    {
        if ($this->basketEntity === null) {
            $this->basketEntity = $this->basketTable->createUserForBasket($this->userId);
        }
        if ($quantity === 0) {
            $this->removeProduct($product);
        } else {
            $row = $this->getRow($product);
            if ($row === null) {
                $this->rows[] = $this->basketTable->addRow($this->basketEntity, $product, $quantity ?: 1);
            } else {
                $this->basketTable->updateRowQuantity($row, $quantity ?: ($row->getQuantity() + 1));
            }
        }
    }

    /**
     * Supprime un produit du panier
     * @param Product $product
     */
    public function removeProduct(Product $product): void
    {
        $row = $this->getRow($product);
        $this->basketTable->deleteRow($row);
        parent::removeProduct($product);
    }


    /**
     * Permets de fusionner les panier
     * panier en session avec le panier en base de donne
     */
    public function merge(\App\Basket\Basket $basket)
    {
        $rows = $basket->getRows();
        foreach ($rows as $r) {
            $row = $this->getRow($r->getProduct());
            if ($row) {
                $this->addProduct($r->getProduct(), $row->getQuantity() + $r->getQuantity());
            } else {
                $this->addProduct($r->getProduct(), $r->getQuantity());
            }
        }
    }

    /**
     * Ca vide le panier
     */
    public function empty()
    {
        $this->basketTable->deleteRows($this->basketEntity);
        parent::empty();
    }
}
