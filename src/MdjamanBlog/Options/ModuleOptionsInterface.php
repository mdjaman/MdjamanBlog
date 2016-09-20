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


/**
 * ModuleOptionsInterface
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
interface ModuleOptionsInterface
{
    /**
     * Getter for recentListingLimit
     *
     * @return int
     */
    public function getRecentListingLimit();

    /**
     * Setter for recentListingLimit
     *
     * @param int $recentListingLimit Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setRecentListingLimit($recentListingLimit);

    /**
     * Getter for categoryListingLimit
     *
     * @return int
     */
    public function getCategoryListingLimit();

    /**
     * Setter for categoryListingLimit
     *
     * @param string $categoryListingLimit Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setCategoryListingLimit($categoryListingLimit);

    /**
     * Getter for archiveListingLimit
     *
     * @return int
     */
    public function getArchiveListingLimit();

    /**
     * Setter for archiveListingLimit
     *
     * @param string $archiveListingLimit Value to se
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setArchiveListingLimit($archiveListingLimit);

    /**
     * Getter for feedListingLimit
     *
     * @return int
     */
    public function getFeedListingLimit();

    /**
     * Setter for feedListingLimit
     *
     * @param string $feedListingLimit Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setFeedListingLimit($feedListingLimit);

    /**
     * Getter for adminListingLimit
     *
     * @return int
     */
    public function getAdminListingLimit();

    /**
     * Setter for adminListingLimit
     *
     * @param string $adminListingLimit Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setAdminListingLimit($adminListingLimit);

    /**
     * Getter for feedGenerator
     *
     * @return array
     */
    public function getFeedGenerator();

    /**
     * Setter for feedGenerator
     *
     * @param string $feedGenerator Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setFeedGenerator($feedGenerator);

    /**
     * Getter for feedSettings
     *
     * @return array
     */
    public function getFeedSettings();

    /**
     * Setter for feedSettings
     *
     * @param string $feedSettings Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setFeedSettings($feedSettings);

    /**
     * Getter for tagEntityClass
     *
     * @return string
     */
    public function getTagEntityClass();

    /**
     * Setter for tagEntityClass
     *
     * @param string $tagEntityClass Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setTagEntityClass($tagEntityClass);

    /**
     * Getter for categoryEntityClass
     *
     * @return string
     */
    public function getCategoryEntityClass();

    /**
     * Setter for categoryEntityClass
     *
     * @param string $categoryEntityClass Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setCategoryEntityClass($categoryEntityClass);

    /**
     * Getter for articleEntityClass
     *
     * @return string
     */
    public function getArticleEntityClass();

    /**
     * Setter for articleEntityClass
     *
     * @param string $articleEntityClass Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setArticleEntityClass($articleEntityClass);

    /**
     * Getter for socialSharingTools
     *
     * @return array
     */
    public function getSocialSharingTools();

    /**
     * Setter for socialSharingTools
     *
     * @param string $socialSharingTools Value to set
     * @return \MdjamanBlog\Options\ModuleOptions
     */
    public function setSocialSharingTools($socialSharingTools);
}