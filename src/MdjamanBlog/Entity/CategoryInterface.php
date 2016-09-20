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

/**
 * CategoryInterface
 */
interface CategoryInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set alias
     *
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias);

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias();

    /**
     * Set descr
     *
     * @param string $descr
     * @return $this
     */
    public function setDescr($descr);

    /**
     * Get descr
     *
     * @return string
     */
    public function getDescr();

    /**
     * Set image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Get image
     *
     * @return string
     */
    public function getImage();

    /**
     * Set feature
     *
     * @param boolean|int $feature
     * @return $this
     */
    public function setFeature($feature = 0);

    /**
     * Get feature
     *
     * @return boolean
     */
    public function getFeature();

    /**
     * Add article
     *
     * @param Article $article
     * @return $this
     */
    public function addArticles(Article $article);

    /**
     * Get article
     *
     * @return Collection $article
     */
    public function getArticles();

    /**
     * @param Collection $article
     */
    public function removeArticles(Collection $article);
}