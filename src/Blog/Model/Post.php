<?php

namespace App\Blog\Model;

use DateTime;

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
    private $created_at;

    /**
     * @var
     */
    private $updated_at;

    /**
     * @var Category
     */
    private $category;

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
     * Get the value of created_at
     *
     * @return  DateTime
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @param  DateTime  $created_at
     *
     * @return  self
     */
    public function setCreated_at(DateTime $created_at)
    {
        if (is_string($created_at)) {
            $this->created_at = new DateTime($created_at);
        } else {
            $this->created_at = $created_at;
        }
        return $this;
    }

    /**
     * Get the value of updated_at
     *
     * @return  DateTimeInterface
     */
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @param  DateTime  $updated_at
     *
     * @return  self
     */
    public function setUpdated_at(DateTime $updated_at)
    {
        $this->updated_at = $updated_at;

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
}
