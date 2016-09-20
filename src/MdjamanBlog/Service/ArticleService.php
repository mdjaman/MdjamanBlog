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

namespace MdjamanBlog\Service;

use Doctrine\Common\Collections\Criteria;
use MdjamanBlog\Entity\ArticleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanBlog\View\Helper\Truncate;
use MdjamanCommon\Service\AbstractService;
use Zend\ServiceManager\ServiceManager;

/**
 * Description of ArticleService
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class ArticleService extends AbstractService implements ArticleServiceInterface
{
    
    const FILTER_GLOSSARY = 'glossary';
    const FILTER_STATUS_ALL = 'all';

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;

    /**
     * ArticleService constructor.
     * @param ServiceManager $serviceManager
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @param ModuleOptionsInterface $options
     */
    public function __construct(ServiceManager $serviceManager, $om, ModuleOptionsInterface $options)
    {
        $this->options = $options;

        $entityClass = $options->getArticleEntityClass();
        parent::__construct(new $entityClass, $om);

        $this->setServiceManager($serviceManager);
    }

    /**
     * @param ArticleInterface|array $data
     * @return ArticleInterface
     */
    public function saveArticle($data)
    {
        $tags = [];
        if (is_array($data) && isset($data['tags'])) {
            $tags = explode(',', $data['tags']);
            unset($data['tags']);
        }

        /* @var $article ArticleInterface */
        $article = $this->save($data, false);

        if (null === $article->getDescription() || '' === $article->getDescription()) {
            $truncate = new Truncate();
            $truncated = $truncate->truncate($article->getContent(), 150, '', false, true);
            $article->setDescription(strip_tags($truncated));
        }

        if (count($tags)) {
            $tagEntityClass = $this->options->getTagEntityClass();
            $tagCollection = new ArrayCollection();
            foreach ($tags as $item) {
                $tag = new $tagEntityClass;
                $tag->setName($item);
                $tagCollection->add($tag);
            }

            $article->addTags($tagCollection);
        }
        
        $this->getObjectManager()->flush();
        
        return $article;
    }
    
    /**
     * Save article and process tags
     * 
     * @param ArticleInterface $article
     * @param mixed|string|array $tags
     * @return ArticleInterface
     */
    public function saveArticleWithTags(ArticleInterface $article, $tags)
    {
        if (!is_array($tags) ) {
            $tags = explode(',', $tags);
        }
        $tagCollection = new ArrayCollection();
        $tagEntityClass = $this->options->getTagEntityClass();
        foreach ($tags as $item) {
            if ($item === '') {
                continue;
            }
            $tag = new $tagEntityClass;
            $tag->setName($item);
            $tagCollection->add($tag);
        }
        
        $article->addTags($tagCollection);

        return $this->save($article, true);
    }
    
    /**
     * Filter
     * @param array $filters
     * @return mixed
     */
    public function filter(array $filters = null)
    {
        $filter = null;
        $value = null;
        $limit = 20;
        $sort = 'created_at';
        $offset = null;

        if (is_array($filters)) {
            extract($filters, EXTR_OVERWRITE);
        }

        $sort = !isset($sort) ? 'created_at' : $sort;

        if (!isset($dir) || !in_array($dir, ['asc', 'desc'])) {
            $dir = 'desc';
        }

        $repository = $this->getRepository();
        switch ($filter) {
            case static::FILTER_GLOSSARY: 
                $document = $repository->findByTitleInitial($value, $sort, $dir, $limit, $offset);
                break;
            case static::FILTER_STATUS_ALL:
                $document = $repository->findArticles(null, $sort, $dir, $limit, $offset);
                break;
            default:
                $document = $repository->findArticles(true, $sort, $dir, $limit, $offset);
                break;
        }
        
        return $document;
    }

    /**
     * @param array $filters
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function filters(array $filters, array $orderBy = null, $limit = null, $offset = null)
    {
        $matches = $this->getMatchingRecords($filters, $orderBy, $limit, $offset);
        return $matches->toArray();
    }

    /**
     * @param $filters
     * @return int
     */
    public function countMatchingRecords($filters)
    {
        $matches = $this->getMatchingRecords($filters);
        return (int)$matches->count();
    }

    /**
     * @param array $filters
     * @return Criteria
     */
    protected function buildCriteria(array $filters)
    {
        $entityClass = $this->options->getArticleEntityClass();
        $entity = $this->hydrate($filters, new $entityClass);

        $expr = Criteria::expr();
        $criteria = Criteria::create();

        foreach ($filters as $key => $value) {
            $method = 'get' . ucfirst($key);
            $criteria->andWhere($expr->eq($key, $entity->{$method}()));
        }

        return $criteria;
    }

    /**
     * @param array $filters
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    protected function getMatchingRecords(array $filters, array $orderBy = null, $limit = null, $offset = null)
    {
        $criteria = $this->buildCriteria($filters);
        return $this->getRepository()->matching($criteria, $orderBy, $limit, $offset);
    }

}
