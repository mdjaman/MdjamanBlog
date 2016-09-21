<?php

namespace MdjamanBlog\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use MdjamanCommon\Entity\BaseEntity;

/**
 * Article
 *
 * @ORM\Table(name="blog_article")
 * @ORM\Entity(repositoryClass="MdjamanBlog\Repository\ArticleRepository")
 */
class Article extends BaseEntity implements ArticleInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Groups({"list", "details"})
     */
    protected $id;

    /**
     * @var string $title
     * @ORM\Column(name="title", type="string", length=150, nullable=false)
     * @JMS\Groups({"list", "details"})
     */
    protected $title;

    /**
     * @var string $alias
     * @ORM\Column(name="alias", type="string", length=150, nullable=false, unique=true)
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @JMS\Groups({"list", "details"})
     */
    protected $alias;

    /**
     * @var text $content
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     * @JMS\Groups({"details"})
     */
    protected $content;

    /**
     * @var text $content
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"details"})
     */
    protected $description;

    /**
     * @var datetime $publishDate
     *
     * @ORM\Column(name="publishDate", type="datetime", nullable=false)
     * @JMS\Groups({"details"})
     */
    protected $publishDate;

    /**
     * @var string $src
     *
     * @ORM\Column(name="src", type="string", length=255, nullable=true)
     * @JMS\Groups({"details"})
     */
    protected $src;

    /**
     * @var string $img
     *
     * @ORM\Column(name="img", type="text", nullable=true)
     * @JMS\Groups({"list", "details"})
     */
    protected $img;

    /**
     * @var integer $hits
     *
     * @ORM\Column(name="hits", type="integer", nullable=true, unique=false)
     * @JMS\Groups({"details"})
     */
    protected $hits = 0;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean", nullable=true, unique=false)
     * @JMS\Groups({"details"})
     */
    protected $active;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="feature", type="boolean", nullable=true, unique=false)
     * @JMS\Groups({"details"})
     */
    protected $feature = 0;

    /**
     * @var boolean $cmtopen
     *
     * @ORM\Column(name="cmtopen", type="boolean", nullable=true)
     * @JMS\Groups({"list", "details"})
     */
    protected $cmtopen;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     * })
     * @JMS\MaxDepth(1)
     * @JMS\Groups({"details"})
     */
    protected $category;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="articles")
     * @ORM\JoinTable(name="blog_article_tag",
     *   joinColumns={
     *      @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *      @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     *   }
     * )
     * @JMS\MaxDepth(3)
     * @JMS\Groups({"details"})
     */
    protected $tags;


    public function __construct()
    {
        parent::__construct();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set publishDate
     *
     * @param \DateTime $publishDate
     * @return $this
     */
    public function setPublishDate(\DateTime $publishDate)
    {
        $this->publishDate = $publishDate;
        return $this;
    }

    /**
     * Get publishDate
     *
     * @return datetime
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * Set src
     *
     * @param string $src
     * @return $this
     */
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    /**
     * Get src
     *
     * @return string
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return $this
     */
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set hits
     *
     * @param integer $hits
     * @return $this
     */
    public function setHits($hits = 0)
    {
        $this->hits = $hits;
        return $this;
    }

    /**
     * Get hits
     *
     * @return integer
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Set active
     *
     * @param mixed|boolean $active
     * @return $this
     */
    public function setActive($active = 1)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set feature
     *
     * @param boolean $feature
     * @return $this
     */
    public function setFeature($feature = 0)
    {
        $this->feature = $feature;
        return $this;
    }

    /**
     * Get feature
     *
     * @return boolean
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * Set cmtopen
     *
     * @param boolean $cmtopen
     * @return $this
     */
    public function setCmtopen($cmtopen)
    {
        $this->cmtopen = $cmtopen;
        return $this;
    }

    /**
     * Get cmtopen
     *
     * @return boolean
     */
    public function getCmtopen()
    {
        return $this->cmtopen;
    }

    /**
     * Set category
     *
     * @param Category $category
     * @return $this
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * 
     * @param Collection $tags
     * @return Article
     */
    public function addTags(Collection $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * 
     * @param Collection $tags
     */
    public function removeTags(Collection $tags)
    {
        foreach ($tags as $item) {
            $this->tags->removeElement($item);
        }
    }

    /**
     * Get tags
     *
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

}
