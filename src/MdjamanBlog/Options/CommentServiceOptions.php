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

/**
 * Description of CommentServiceOptions
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class CommentServiceOptions extends AbstractOptions implements CommentServiceOptionsInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;
    
    protected $provider = 'disqus';
    
    protected $identifier;
    
    protected $shortname = 'santefute';
    
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
     * Getter for identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    
    /**
     * Setter for identifier
     *
     * @param string $identifier
     * @return self
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }
    
    public function getShortname()
    {
        return $this->shortname;
    }

    public function setShortname($shortname)
    {
        $this->shortname = $shortname;
        return $this;
    }
    
}
