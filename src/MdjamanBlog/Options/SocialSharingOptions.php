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

use Zend\Stdlib\AbstractOptions;
use MdjamanBlog\Exception\InvalidArgumentException;

/**
 * Description of SocialSharingOptions
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class SocialSharingOptions extends AbstractOptions implements SocialSharingOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;
    
    protected $provider = 'addthis';
    
    protected $id;
    
    protected $networks = [
        'facebook',
        'twitter',
        'google_plus',
        'linkedin',
    ];
    
    protected $analytics = [
        'service' => 'google',
        'social' => true,
        'code' => 'UA-31105807-1',
    ];

    /**
     * Getter for provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }
    
    /**
     * Setter for provider
     *
     * @param string $provider
     * @return self
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }
    
    /**
     * Getter for id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Setter for id
     *
     * @param string $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * Getter for networks
     *
     * @return mixed
     */
    public function getNetworks()
    {
        return $this->networks;
    }
    
    /**
     * Setter for networks
     *
     * @param array $networks
     * @return self
     */
    public function setNetworks($networks = [])
    {
        $this->networks = $networks;
        return $this;
    }
    
    /**
     * Setter for analytics
     *
     * @param mixed $analytics Value to set
     * @return self
     */
    public function setAnalytics($analytics)
    {
        if (!is_array($analytics)) {
            throw new InvalidArgumentException(sprintf(
                'Analytics settings must be an array, %s given',
                gettype($analytics)
            ));
        }
        if (!isset($analytics['service'])
         || !isset($analytics['code']))
        {
            throw new InvalidArgumentException(sprintf(
                'Analytics settings must contain keys "service" and "code", only "%s" given',
                implode(',', array_keys($analytics))
            ));
        }
        $this->analytics = $analytics;
        return $this;
    }

    public function getAnalytics()
    {
        return $this->analytics;
    }

}
