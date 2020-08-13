<?php

namespace App\Shop\Model;

use Framework\Datetime\Timestamp;

class Purchase
{

    private $id;

    private $userId;

    private $productId;

    private $price;

    private $vat;

    private $country;

    private $chargeId;

    use Timestamp;

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId(int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getVat(): ?float
    {
        return $this->vat;
    }

    /**
     * @param mixed $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return mixed
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }


    /**
     * @return mixed
     */
    public function getChargeId()
    {
        return $this->chargeId;
    }

    /**
     * @param mixed $stripeId
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;
    }
}
