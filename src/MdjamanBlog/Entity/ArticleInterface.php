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

/**
 * Description of ArticleInterface
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
interface ArticleInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set alias
     *
     * @param string $alias
     */
    public function setAlias($alias);

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias();

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content);

    /**
     * Get content
     *
     * @return text
     */
    public function getContent();

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription();

    /**
     * Set publishDate
     *
     * @param \DateTime $publishDate
     */
    public function setPublishDate(\DateTime $publishDate);

    /**
     * Get publishDate
     *
     * @return datetime
     */
    public function getPublishDate();

    /**
     * Set src
     *
     * @param string $src
     */
    public function setSrc($src);

    /**
     * Get src
     *
     * @return string
     */
    public function getSrc();

    /**
     * Set img
     *
     * @param string $img
     */
    public function setImg($img);

    /**
     * Get img
     *
     * @return string
     */
    public function getImg();

    /**
     * Set hits
     *
     * @param integer $hits
     */
    public function setHits($hits);

    /**
     * Get hits
     *
     * @return integer
     */
    public function getHits();

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active);

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive();

    /**
     * Set feature
     *
     * @param boolean $feature
     */
    public function setFeature($feature);

    /**
     * Get feature
     *
     * @return boolean
     */
    public function getFeature();

    /**
     * Set cmtopen
     *
     * @param boolean $cmtopen
     */
    public function setCmtopen($cmtopen);

    /**
     * Get cmtopen
     *
     * @return boolean
     */
    public function getCmtopen();

    /**
     * Set category
     *
     * @param CategoryInterface $category
     */
    public function setCategory(CategoryInterface $category);

    /**
     * Get category
     *
     * @return CategoryInterface
     */
    public function getCategory();
    
    /**
     * Get tags
     *
     * @return Collection
     */
    public function getTags();
}
