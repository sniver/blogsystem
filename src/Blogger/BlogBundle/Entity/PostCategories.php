<?php

// src/Blogger/BlogBundle/Entity/Posts.php
namespace Blogger\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
     *@ORM\Entity
     *@ORM\Table(name="post_categories")
     */
class PostCategories
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	
	/**
     * @ORM\Column(type="integer")
     */
    protected $Post_id;
	
	
	/**
     * @ORM\Column(type="integer")
     */
    protected $Category_id;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Post_id
     *
     * @param integer $postId
     * @return PostCategories
     */
    public function setPostId($postId)
    {
        $this->Post_id = $postId;

        return $this;
    }

    /**
     * Get Post_id
     *
     * @return integer 
     */
    public function getPostId()
    {
        return $this->Post_id;
    }

    /**
     * Set Category_id
     *
     * @param integer $categoryId
     * @return PostCategories
     */
    public function setCategoryId($categoryId)
    {
        $this->Category_id = $categoryId;

        return $this;
    }

    /**
     * Get Category_id
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->Category_id;
    }
}
