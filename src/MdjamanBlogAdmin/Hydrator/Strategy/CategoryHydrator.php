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

namespace MdjamanBlogAdmin\Hydrator\Strategy;

use MdjamanBlog\Entity\CategoryInterface;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * Class CategoryStrategy
 * @package MdjamanBlogAdmin\Hydrator\Strategy
 */
class CategoryStrategy implements StrategyInterface
{

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var string
     */
    protected $className;

    /**
     * @param ObjectManager $om
     * @param string $className
     */
    public function __construct(ObjectManager $om, $className)
    {
        $this->om = $om;
        $this->className = $className;
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param mixed $value The original value.
     * @return mixed Returns the value that should be extracted.
     */
    public function extract($value)
    {
        if ($value === null || !$value instanceof CategoryInterface) {
            return $value;
        }

        return $value->getId();
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param mixed $value The original value.
     * @return mixed Returns the value that should be hydrated.
     */
    public function hydrate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $object = $this->om->getReference($this->className, $value);
        return $object;
    }
}