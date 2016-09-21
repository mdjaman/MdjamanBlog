<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Marcel Djaman
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace MdjamanBlog\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use MdjamanCommon\Entity\BaseEntity;

/**
 * Category
 *
 * @ORM\Table(name="blog_category")
 * @ORM\Entity(repositoryClass="MdjamanBlog\Repository\CategoryRepository")
 */
class Category extends BaseEntity implements CategoryInterface
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @JMS\Groups({"list", "details"})
     */
    protected $name;

    /**
     * @var string $alias
     *
     * @ORM\Column(name="alias", type="string", length=50, nullable=false, unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @JMS\Groups({"list", "details"})
     */
    protected $alias;

    /**
     * @var string $descr
     *
     * @ORM\Column(name="descr", type="string", length=100, nullable=true, unique=false)
     * @JMS\Groups({"list", "details"})
     */
    protected $descr;
    
    /**
     * @var boolean $active
     *
     * @ORM\Column(name="feature", type="boolean", nullable=true, unique=false)
     * @JMS\Groups({"details"})
     */
    protected $feature = 0;

    /**
     * @var string $image
     *
     * @ORM\Column(name="image", type="string", length=50, nullable=true, unique=false)
     * @JMS\Groups({"details"})
     */
    protected $image;

    /**
     * @var $article
     * 
     * @ORM\OneToMany(targetEntity="Article", mappedBy="category", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $articles;
    
    
    public function __construct()
    {
        parent::__construct();
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
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
     * Set descr
     *
     * @param string $descr
     * @return $this
     */
    public function setDescr($descr)
    {
        $this->descr = $descr;
        return $this;
    }

    /**
     * Get descr
     *
     * @return string 
     */
    public function getDescr()
    {
        return $this->descr;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set feature
     *
     * @param boolean|int $feature
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
     * Add article
     *
     * @param ArticleInterface $article
     * @return $this
     */
    public function addArticles(ArticleInterface $article)
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
        }
        return $this;
    }

    /**
     * Get article
     *
     * @return Collection $article
     */
    public function getArticles()
    {
        return $this->articles;
    }
    
    /**
     * @param Collection $article
     */
    public function removeArticles(Collection $article)
    {
        foreach ($article as $item) {
            $this->articles->removeElement($item);
        }
    }

}
