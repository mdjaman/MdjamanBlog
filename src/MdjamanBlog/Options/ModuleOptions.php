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

namespace MdjamanBlog\Options;

use Zend\Stdlib\AbstractOptions;
use MdjamanBlog\Exception\InvalidArgumentException;

/**
 * Description of ModuleOptions
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class ModuleOptions extends AbstractOptions implements ModuleOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;
    
    /**
     * @var int
     */
    protected $recentListingLimit = 20;
    
    /**
     * @var int
     */
    protected $categoryListingLimit = 20;
    
    /**
     * @var int
     */
    protected $archiveListingLimit = 20;
    
    /**
     * @var  int
     */
    protected $feedListingLimit = 20;
    
    /**
     * @var  int
     */
    protected $adminListingLimit = 20;
    
    /**
     * @var array
     */
    protected $feedGenerator;
    
    /**
     * @var array
     */
    protected $feedSettings;
    
    /**
     * @var string
     */
    protected $tagEntityClass = 'MdjamanBlog\Entity\Tag';
    
    /**
     * @var string
     */
    protected $categoryEntityClass = 'MdjamanBlog\Entity\Category';
    
    /**
     * @var string
     */
    protected $articleEntityClass = 'MdjamanBlog\Entity\Article';
    
    /**
     * @var array 
     */
    protected $socialSharingTools;


    /**
     * Getter for recentListingLimit
     *
     * @return int
     */
    public function getRecentListingLimit()
    {
        return $this->recentListingLimit;
    }
    
    /**
     * Setter for recentListingLimit
     *
     * @param int $recentListingLimit Value to set
     * @return self
     */
    public function setRecentListingLimit($recentListingLimit)
    {
        $this->recentListingLimit = (int) $recentListingLimit;
        return $this;
    }
    
    /**
     * Getter for categoryListingLimit
     *
     * @return int
     */
    public function getCategoryListingLimit()
    {
        return $this->categoryListingLimit;
    }
    
    /**
     * Setter for categoryListingLimit
     *
     * @param string $categoryListingLimit Value to set
     * @return self
     */
    public function setCategoryListingLimit($categoryListingLimit)
    {
        $this->categoryListingLimit = $categoryListingLimit;
        return $this;
    }
    
    /**
     * Getter for archiveListingLimit
     *
     * @return int
     */
    public function getArchiveListingLimit()
    {
        return $this->archiveListingLimit;
    }
    
    /**
     * Setter for archiveListingLimit
     *
     * @param string $archiveListingLimit Value to se
     * @return self
     */
    public function setArchiveListingLimit($archiveListingLimit)
    {
        $this->archiveListingLimit = $archiveListingLimit;
        return $this;
    }
    
    /**
     * Getter for feedListingLimit
     *
     * @return int
     */
    public function getFeedListingLimit()
    {
        return $this->feedListingLimit;
    }
    
    /**
     * Setter for feedListingLimit
     *
     * @param string $feedListingLimit Value to set
     * @return self
     */
    public function setFeedListingLimit($feedListingLimit)
    {
        $this->feedListingLimit = $feedListingLimit;
        return $this;
    }
    
    /**
     * Getter for adminListingLimit
     *
     * @return int
     */
    public function getAdminListingLimit()
    {
        return $this->adminListingLimit;
    }
    
    /**
     * Setter for adminListingLimit
     *
     * @param string $adminListingLimit Value to set
     * @return self
     */
    public function setAdminListingLimit($adminListingLimit)
    {
        $this->adminListingLimit = $adminListingLimit;
        return $this;
    }
    
    /**
     * Getter for feedGenerator
     *
     * @return array
     */
    public function getFeedGenerator()
    {
        return $this->feedGenerator;
    }
    
    /**
     * Setter for feedGenerator
     *
     * @param string $feedGenerator Value to set
     * @return self
     */
    public function setFeedGenerator($feedGenerator)
    {
        if (!is_array($feedGenerator)) {
            throw new InvalidArgumentException(sprintf(
                'Feed generator must be an array, %s given',
                gettype($feedGenerator)
            ));
        }
        if (!isset($feedGenerator['name'])
         || !isset($feedGenerator['version'])
         || !isset($feedGenerator['uri']))
        {
            throw new InvalidArgumentException(sprintf(
                'Feed generator must contain keys "name", "version" and "uri", only "%s" given',
                implode(',', array_keys($feedGenerator))
            ));
        }
        $this->feedGenerator = $feedGenerator;
        return $this;
    }
    
    /**
     * Getter for feedSettings
     *
     * @return array
     */
    public function getFeedSettings()
    {
        return $this->feedSettings;
    }
    
    /**
     * Setter for feedSettings
     *
     * @param string $feedSettings Value to set
     * @return self
     */
    public function setFeedSettings($feedSettings)
    {
        if (!is_array($feedSettings)) {
            throw new InvalidArgumentException(sprintf(
                'Feed settings must be an array, %s given',
                gettype($feedSettings)
            ));
        }
        if (!isset($feedSettings['title'])
         || !isset($feedSettings['description']))
        {
            throw new InvalidArgumentException(sprintf(
                'Feed settings must contain keys "title" and "description", only "%s" given',
                implode(',', array_keys($feedSettings))
            ));
        }
        $this->feedSettings = $feedSettings;
        return $this;
    }
    
    /**
     * Getter for tagEntityClass
     *
     * @return string
     */
    public function getTagEntityClass()
    {
        return $this->tagEntityClass;
    }
    
    /**
     * Setter for tagEntityClass
     *
     * @param string $tagEntityClass Value to set
     * @return self
     */
    public function setTagEntityClass($tagEntityClass)
    {
        $this->tagEntityClass = $tagEntityClass;
        return $this;
    }
    
    /**
     * Getter for categoryEntityClass
     *
     * @return string
     */
    public function getCategoryEntityClass()
    {
        return $this->categoryEntityClass;
    }
    
    /**
     * Setter for categoryEntityClass
     *
     * @param string $categoryEntityClass Value to set
     * @return self
     */
    public function setCategoryEntityClass($categoryEntityClass)
    {
        $this->categoryEntityClass = $categoryEntityClass;
        return $this;
    }
    
    /**
     * Getter for articleEntityClass
     *
     * @return string
     */
    public function getArticleEntityClass()
    {
        return $this->articleEntityClass;
    }
    
    /**
     * Setter for articleEntityClass
     *
     * @param string $articleEntityClass Value to set
     * @return self
     */
    public function setArticleEntityClass($articleEntityClass)
    {
        $this->articleEntityClass = $articleEntityClass;
        return $this;
    }

    /**
     * Getter for socialSharingTools
     *
     * @return array
     */
    public function getSocialSharingTools()
    {
        return $this->socialSharingTools;
    }
    
    /**
     * Setter for socialSharingTools
     *
     * @param string $socialSharingTools Value to set
     * @return self
     */
    public function setSocialSharingTools($socialSharingTools)
    {
        if (!is_array($socialSharingTools)) {
            throw new InvalidArgumentException(sprintf(
                'SocialSharingTools settings must be an array, %s given',
                gettype($socialSharingTools)
            ));
        }
        if (!isset($socialSharingTools['provider'])
         || !isset($socialSharingTools['networks']))
        {
            throw new InvalidArgumentException(sprintf(
                'SocialSharingTools settings must contain keys "provider" and "networks", only "%s" given',
                implode(',', array_keys($socialSharingTools))
            ));
        }
        $this->socialSharingTools = $socialSharingTools;
        return $this;
    }
    
}
