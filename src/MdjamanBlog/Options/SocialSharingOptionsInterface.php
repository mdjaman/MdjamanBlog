<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MdjamanBlog\Options;

/**
 * Description of SocialSharingOptionsInterface
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
interface SocialSharingOptionsInterface
{
    
    /**
     * @param string $provider
     */
    public function setProvider($provider);
    
    /**
     * @return string
     */
    public function getProvider();
    
    /**
     * @param string $id
     */
    public function setId($id);
    
    /**
     * @return string
     */
    public function getId();
    
    /**
     * @param array $networks
     */
    public function setNetworks($networks);
    
    /**
     * @return array
     */
    public function getNetworks();
    
    /**
     * @param array $analytics
     */
    public function setAnalytics($analytics);
    
    /**
     * @return array
     */
    public function getAnalytics();
}
