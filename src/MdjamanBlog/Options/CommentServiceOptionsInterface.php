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
 * Description of CommentServiceOptionsInterface
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
interface CommentServiceOptionsInterface
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
     * @param string $identifier
     */
    public function setIdentifier($identifier);
    
    /**
     * @return string
     */
    public function getIdentifier();
    
    /**
     * @param string $shortname
     */
    public function setShortname($shortname);
    
    /**
     * @return string
     */
    public function getShortname();
    
}
