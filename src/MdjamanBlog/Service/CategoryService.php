<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MdjamanBlog\Service;

use MdjamanBlog\Entity\CategoryInterface;
use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanCommon\Service\AbstractService;
use Zend\ServiceManager\ServiceManager;

/**
 * Description of Category
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class CategoryService extends AbstractService implements CategoryServiceInterface
{

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;

    /**
     * CategoryService constructor.
     * @param ServiceManager $serviceManager
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     * @param ModuleOptionsInterface $options
     */
    public function __construct(ServiceManager $serviceManager, $om, ModuleOptionsInterface $options)
    {
        $this->options = $options;

        $entityClass = $options->getCategoryEntityClass();
        parent::__construct(new $entityClass, $om);

        $this->setServiceManager($serviceManager);
    }

    /**
     * @param CategoryInterface|array $data
     * @return CategoryInterface
     */
    public function saveCategory($data)
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
