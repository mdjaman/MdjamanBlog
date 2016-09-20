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

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository
 */
class CategoryRepository extends EntityRepository
{

    /**
     * 
     * @param array $criteria
     * @return type
     */
    public function countResult($criteria)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('COUNT(c.id)');
        
        $x = 1;
        foreach ($criteria as $key => $value) {
            if ($key !== '') {
                $qb->andWhere("c.$key = ?$x");
                $qb->setParameter($x, $value);
                ++$x;
            }
        }
        
        $query = $qb->getQuery();
        
        $query->useQueryCache(true)
              ->useResultCache(true, 3600);
        
        return $query->getSingleScalarResult();
    }
    
    /**
     * Find feature categories
     * 
     * @param string $orderBy
     * @param string $dir
     * @param int|null $limit
     * @param int|null $offset
     * @return mixed
     */
    public function findFeatures($orderBy = 'id', $dir = 'asc', $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.feature = 1');
        
        $qb->orderBy('c.'. $orderBy, $dir);

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
     * 
     * @param type $criteria
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function queryByCriteria($criteria)
    {
        $qb = $this->createQueryBuilder('c');
        
        $x = 1;
        foreach ($criteria as $key => $value) {
            if ($key !== '') {
                $qb->andWhere("c.$key = ?$x");
                $qb->setParameter($x, $value);
                ++$x;
            }
        }
        
        return $qb;
    }
    
    public function findCategories($criteria = [], $orderBy = 'id', $dir = 'asc', $limit = null, $offset = null)
    {
        $qb = $this->queryByCriteria($criteria);
        $qb->addSelect('a')
           ->leftJoin('c.articles', 'a')
           ->where('a.active = 1');
        
        $qb->orderBy('c.'. $orderBy, $dir);

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
     * Find a
     * @param string $alias
     * @return mixed
     */
    public function findCategoryByAlias($alias)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->addSelect('a')
           ->leftJoin('c.articles', 'a')
           ->where('c.alias = ?1')
           ->andWhere('a.active = 1');
        
        $qb->setParameter(1, $alias);
        
        $query = $qb->getQuery();

        $cacheKey = __FUNCTION__ . json_encode(func_get_args());
        $query->useQueryCache(true);
        $query->useResultCache(true, 3600, $cacheKey);

        return $query->getSingleResult();
    }

}
