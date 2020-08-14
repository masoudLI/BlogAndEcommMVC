<?php

namespace App\Shop\Model;

use Framework\Datetime\Timestamp;

class Product
{

    private int $id;


    private ?string $title = null;


    private ?string $slug = null;


    private ?string $description = null;


    private $price;


    private ?string $image = null;


    private ?bool $published = null;


    use Timestamp;

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice($price): self
    {
        if (is_string($price)) {
            $price = (int)$price;
        }
        $this->price = $price;
        return $this;
    }

    /**
     * Get the value of image
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */
    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of published
     */
    public function getPublished(): ?bool
    {
        return $this->published;
    }

    /**
     * Set the value of published
     *
     * @return  self
     */
    public function setPublished(?bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->getImage());
        return '/uploads/products/' . $filename . '_small.' . $extension;
    }


    public function getImageUrl()
    {
        return "/uploads/products/" . $this->getImage();
    }

    public function getPdf()
    {
        return "{$this->id}.pdf";
    }
}
