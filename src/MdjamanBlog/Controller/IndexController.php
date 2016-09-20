<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MdjamanBlog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of IndexController
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class IndexController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }
}
