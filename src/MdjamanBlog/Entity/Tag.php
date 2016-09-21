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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use MdjamanCommon\Entity\BaseEntity;

/**
 * Tag
 *
 * @ORM\Table(name="blog_tag")
 * @ORM\Entity(repositoryClass="MdjamanBlog\Repository\TagRepository")
 */
class Tag extends BaseEntity implements TagInterface
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
     * @ORM\Column(name="name", type="string", length=50, nullable=true, unique=false)
     * @JMS\Groups({"list", "details"})
     */
    protected $name;

    /**
     * @var string $alias
     * 
     * @ORM\Column(name="alias", type="string", length=50, nullable=true, unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @JMS\Groups({"list", "details"})
     */
    protected $alias;

    /**
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="tags", cascade={"all"})
     * @JMS\MaxDepth(1)
     * @JMS\Groups({"details"})
     */
    protected $articles;


    public function __construct()
    {
        parent::__construct();
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    /**
     * destroy identity
     */
    public function __clone()
    {
        $this->id = null;
        $this->alias = null;
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }
    
    /**
     * @param Collection $articles
     */
    public function removeArticles(Collection $articles)
    {
        foreach ($articles as $item) {
            $this->articles->removeElement($item);
        }
    }

}
