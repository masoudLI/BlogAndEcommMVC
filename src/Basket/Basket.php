<?php

namespace App\Basket;

use App\Shop\Model\Product;

class Basket
{

    public $rows = [];

    /**
     * addProduct
     *
     * @param  mixed $product
     * @param  mixed $quentity
     * @return void
     */
    public function addProduct(Product $product, ?int $quantity = null): void
    {
        if ($quantity === 0) {
            $this->removeProduct($product);
        } else {
            $row = $this->getRow($product);
            if ($row === null) {
                $row = new BasketRow();
                $row->setProduct($product);
                $this->rows[] = $row;
            } else {
                $row->setQuantity($row->getQuantity() + 1);
            }
            if ($quantity !== null) {
                $row->setQuantity($quantity);
            }
        }
    }

    /**
     * removeProduct
     *
     * @param  mixed $product
     * @return void
     */
    public function removeProduct(Product $product): void
    {
        $this->rows = array_filter($this->rows, function ($row) use ($product) {
            return $row->getProductId() !== $product->getId();
        });
    }


    /**
     * count
     *
     * @return int
     */
    public function count(): int
    {
        return array_reduce($this->rows, function ($count, BasketRow $row) {
            return $row->getQuantity() + $count;
        }, 0);
    }


    /**
     * count
     *
     * @return int
     */
    public function getTotal(): int
    {
        return array_reduce($this->rows, function ($count, BasketRow $row) {
            return $row->getQuantity() * $row->getProduct()->getPrice() + $count;
        }, 0);
    }

    /**
     * Get the value of rows
     */
    public function getRows(): array
    {
        return $this->rows;
    }


    public function getRow(Product $product): ?BasketRow
    {
        
        foreach ($this->rows as $row) {
            if ($row->getProductId() === $product->getId()) {
                return $row;
            }
        }
        return null;
    }

    public function empty()
    {
        $this->rows = [];
    }
}
