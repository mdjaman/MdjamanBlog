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

use MdjamanBlog\Entity\Tag;
use MdjamanBlog\Entity\TagInterface;
use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanCommon\Service\AbstractService;
use Zend\ServiceManager\ServiceManager;

/**
 * TagService
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class TagService extends AbstractService implements TagServiceInterface
{

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;

    /**
     * TagService constructor.
     * @param ServiceManager $serviceManager
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @param ModuleOptionsInterface $options
     */
    public function __construct(ServiceManager $serviceManager, $om, ModuleOptionsInterface $options)
    {
        $this->options = $options;

        $entityClass = $options->getTagEntityClass();
        parent::__construct(new $entityClass, $om);

        $this->setServiceManager($serviceManager);
    }

    /**
     * @param TagInterface|array $data
     * @return TagInterface
     */
    public function saveTag($data)
    {
        return $this->save($data);
    }
    
    /**
     * Filter
     * @param array $filters
     * @return multitype:
     */
    public function filter(array $filters = null)
    {
        $filter = null;
        $value = null;
        $criteria = [];
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

        $orderBy = [$sort => $dir];

        switch ($filter) {
            default:
                if (is_null($filter) || $filter == '') {
                    $criteria = [];
                } else {
                    $criteria = [$filter => $value];
                }
                
                $document = $this->findBy($criteria, $orderBy, $limit, $offset);
                break;
        }
        
        return $document;
    }
    
}
