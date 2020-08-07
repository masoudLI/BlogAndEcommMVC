<?php

namespace App\Blog\Model;

use DateTime;
use DateTimeInterface;

class Post
{

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private ?string $name = null;

    /**
     * @var string
     */
    private ?string $slug = null;

    /**
     * @var string
     */
    private ?string $content = null;

    /**
     * @var
     */
    private DateTime $createdAt;

    /**
     * @var
     */
    private DateTime $updatedAt;

    /**
     * @var Category
     */
    private $category;
    

    private $image;


    private bool $published;

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  int  $id
     *
     * @return  self
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of slug
     *
     * @return  string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @param  string  $slug
     *
     * @return  self
     */
    public function setSlug(?string $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of content
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  string  $content
     *
     * @return  self
     */
    public function setContent(?string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return  DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  DateTime  $createdAt
     *
     * @return  self
     */
    public function setCreatedAt($datetime): self
    {
        if (is_string($datetime)) {
            $this->createdAt = new DateTime($datetime);
        } else {
            $this->createdAt = $datetime;
        }
        return $this;
    }

    /**
     * Get the value of updated_at
     *
     * @return  DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updated_at
     *
     * @param  DateTime  $updated_at
     *
     * @return  self
     */
    public function setUpdatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->updatedAt = new DateTime($datetime);
        } else {
            $this->updatedAt = $datetime;
        }
        return $this;
    }

    /**
     * Get the value of category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @return  self
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     *
     * @return  self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->getImage());
        return '/uploads/posts/' . $filename . '_small.' . $extension;
    }


    public function getImageUrl()
    {
        return "/uploads/posts/" . $this->getImage();
    }

    /**
     * Get the value of published
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set the value of published
     *
     * @return  self
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }
}
