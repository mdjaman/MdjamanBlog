<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MdjamanBlog;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature;

/**
 * Description of Module
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface
{
    
    /**
     * {@InheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__           => __DIR__ . '/src/MdjamanBlog',
                    __NAMESPACE__ . 'Admin' => __DIR__ . '/src/MdjamanBlogAdmin',
                ),
            ),
        );
    }

    /**
     * {@InheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

}
