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

namespace MdjamanBlog\Repository;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;

/**
 * ArticleRepository
 */
class ArticleRepository extends EntityRepository
{

    /**
     * @param array $criteria
     * @return string
     */
    public function countResult($criteria)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('COUNT(a.id)');
        
        $x = 1;
        foreach ($criteria as $key => $value) {
            if ($key !== '') {
                $qb->andWhere("a.$key = ?$x");
                $qb->setParameter($x, $value);
                ++$x;
            }
        }
        
        $query = $qb->getQuery();
        
        $cacheKey = __FUNCTION__ . json_encode(func_get_args());
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);
        
        return $query->getSingleScalarResult();
    }

    /**
     * List articles
     * 
     * @param boolean|null $active
     * @param string $orderBy
     * @param string $dir
     * @param int $limit max results to retrieve
     * @param int|null $offset
     * @param boolean $active only published or not
     * @return mixed
     */
    public function findArticles($active = true, $orderBy = 'publishDate', $dir = 'desc', $limit = 20, $offset = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->addSelect(array('c'))
           ->leftJoin('a.category', 'c');

        if ($active === true) {
            $qb->where('a.active = 1');
        }

        $qb->orderBy('a.'. $orderBy, $dir);

        $query = $qb->getQuery();
        if (null !== $limit) {
            $query->setMaxResults($limit);
            if (null !== $offset) {
                $query->setFirstResult($offset);
            }
        }

        $cacheKey = md5(__FUNCTION__ . json_encode(func_get_args()));
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);

        return $query->getResult();
    }
    
    /**
     * Find a article by it's id value
     * 
     * @param int $id
     * @param boolean $active
     * @return mixed
     */
    public function findOneById($id, $active = true)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->leftJoin('n.category', 'c')
            ->where('n.id = ?1')
            ->setParameter(1, $id);
        
        if ($active == true) {
            $qb->andWhere('n.active = 1');
        }

        $query = $qb->getQuery();

        $cacheKey = __FUNCTION__ . json_encode(func_get_args());
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);

        return $query->getResult();
    }

    /**
     * Find a article by it's alias value
     * 
     * @param string $alias
     * @param boolean $active
     * @return mixed
     */
    public function findOneByAlias($alias, $active = true)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->addSelect(array('c', 't'))
            ->leftJoin('n.category', 'c')
            ->leftJoin('n.tags', 't')
            ->where('n.alias = ?1')
            ->setParameter(1, $alias);

        if ($active) {
            $qb->andWhere('n.active = 1');
        }
        
        $query = $qb->getQuery();

        $cacheKey = md5(__FUNCTION__ . json_encode(func_get_args()));
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);

        return $query->getOneOrNullResult();
    }

    /**
     * @param $category
     * @param bool $active
     * @param string $orderBy
     * @param string $dir
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryArticleByCategory($category, $active = true, $orderBy = 'id', $dir = 'desc')
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where('n.category = ?1')
            ->setParameter(1, $category);

        if ($active == true) {
            $qb->andWhere('n.active = 1');
        }

        $qb->orderBy('n.' . $orderBy, $dir);

        return $qb;
    }

    /**
     * 
     * Paginate article of a category
     * @param array $tags
     * @param boolean $active if true only active will be fetch
     * @param string $orderBy
     * @param string $dir
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryArticleByTags($tags, $active = true, $orderBy = 'id', $dir = 'desc')
    {
        $qb = $this->createQueryBuilder('n');
        $qb->addSelect(array('c', 't'))
            ->leftJoin('n.category', 'c')
            ->leftJoin('n.tags', 't');
        $qb->where('t.id IN (:ids)')
            ->setParameter('ids', $tags);

        if ($active == true) {
            $qb->andWhere('n.active = 1');
        }

        $qb->orderBy('n.' . $orderBy, $dir);

        return $qb;
    }

    /**
     * 
     * Paginate article of a category
     * @param int $category
     * @param boolean $active if true only active will be fetch
     * @param string $orderBy the sorted column
     * @param string $dir sorting direction
     * @return \Doctrine\ORM\Query
     */
    public function paginateByCategory($category, $active = true, $orderBy = 'id', $dir = 'desc')
    {
        $qb = $this->queryArticleByCategory($category, $active, $orderBy, $dir);

        $query = $qb->getQuery();

        $cacheKey = __FUNCTION__ . json_encode(func_get_args());
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);
        
        return $query;
    }

    /**
     * 
     * Paginate article by a tag
     * @param string $tag
     * @param boolean $active if true only active will be fetch
     * @return void
     */
    public function paginateByTag($tag, $active = true)
    {
        $qb = $this->createQueryBuilder('n');
        $qb->where($qb->expr()->like('n.tag', '?1'));

        if ($active == true) {
            $qb->andWhere('n.active = 1');
        }

        $query = $qb->getQuery();
        $query->setParameter(1, '%' . $tag . '%');

        $cacheKey = __FUNCTION__ . json_encode(func_get_args());
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);
        
        return $query;
    }

    /**
     * Find articles of a category id
     * 
     * @param mixed $category
     * @param boolean $active
     * @param null|string $exclude
     * @param string $orderBy
     * @param string $dir
     * @param null|int $limit
     * @param null|int $offset
     * @return mixed
     */
    public function findArticleByCategory(
        $category, $active = true, $exclude = null,
        $orderBy = 'id', $dir = 'desc', $limit = 20, $offset = null)
    {
        $qb = $this->queryArticleByCategory($category, $active, $orderBy, $dir);
        if (null !== $exclude) {
            $qb->andWhere('n.id != :exclude');
            $qb->setParameter('exclude', $exclude);
        }
        
        $query = $qb->getQuery();
        
        if (null !== $limit) {
            $query->setMaxResults($limit);
            if (null !== $offset) {
                $query->setFirstResult($offset);
            }
        }

        $cacheKey = __FUNCTION__ . json_encode(func_get_args());
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);
        
        return $query->getResult();
    }

    /**
     * Find articles from tags
     * 
     * @param mixed|array $tags
     * @param boolean $active if true only active will be fetch
     * @return mixed
     */
    public function findArticleByTags($tags, $active = true, $orderBy = 'id', $dir = 'desc',
        $limit = 20, $offset = null)
    {
        $tags = (array) $tags;
        $qb = $this->queryArticleByTags($tags, $active, $orderBy, $dir);
        
        $query = $qb->getQuery();
        
        if (null !== $limit) {
            $query->setMaxResults($limit);
            if (null !== $offset) {
                $query->setFirstResult($offset);
            }
        }

        $cacheKey = md5(__FUNCTION__ . json_encode(func_get_args()));
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);
        
        return $query->getResult();
    }

    /**
     * @param string $year
     * @return mixed
     */
    public function statByYear($year = '2016')
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('MdjamanBlog\Entity\Article', 'n');
        $rsm->addFieldResult('n', 'publish', 'publishDate');
        $rsm->addFieldResult('n', 'total', 'articleid');

        $sql = 'SELECT DATE_FORMAT(publishDate, "%Y-%m-%d %H:%i:%s") publish, COUNT(articleid) total
FROM article
WHERE DATE_FORMAT(publishDate, "%Y") = ?
GROUP BY DATE_FORMAT(publishDate, "%M")
ORDER BY dateupdated';
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $year);

        $query->useResultCache(true, 3600, __FUNCTION__.$year);

        return $query->getArrayResult();
    }
    
    /**
     * Retrieve featured articles
     * @param boolean $active
     * @param string $orderBy
     * @param string $dir
     * @param int $limit
     * @param null|int $offset
     * @return mixed
     */
    public function getFeaturedArticles($active = true, $orderBy = 'publishDate',
        $dir = 'desc', $limit = 20, $offset = null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.feature = 1');
        
        if ($active === true) {
            $qb->andWhere('a.active = 1');
        }

        $qb->orderBy('a.'. $orderBy, $dir);

        $query = $qb->getQuery();
        if (null !== $limit) {
            $query->setMaxResults($limit);
            if (null !== $offset) {
                $query->setFirstResult($offset);
            }
        }

        $cacheKey = __FUNCTION__ . json_encode(func_get_args());
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);

        return $query->getResult();
    }

}
