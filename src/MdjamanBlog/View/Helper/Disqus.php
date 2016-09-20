<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MdjamanBlog\View\Helper;

use MdjamanBlog\Options\CommentServiceOptionsInterface;
use Zend\View\Helper\AbstractHelper;
use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Description of Disqus
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class Disqus extends AbstractHelper
{
    
    static $loadComInstances = 0;
    static $comCountInstances = 0;
    
    /**
     * @var array
     */
    private static $plugins = array(
        'pluginUrlBuilder'
    );

    /**
     * @var array
     */
    private $params = array();
    
    protected $schema = 'http://';

    /**
     * Proxies all calls
     *
     * @param  string $method
     * @param  array  $args
     * @throws RuntimeException
     * @return string
     */
    public function __call($method, $args)
    {
        if (($params = array_shift($args)) !== null) {
            if (is_array($params)) {
                foreach (self::$plugins as $plugin) {
                    $params = $this->$plugin($params);
                }

                $args[0] = $params;
            }
        } else {
            $params = array();
        }

        $params = array_shift($args) ?: array();

        try {
            return $this->$method($params, $params);
        } catch (Exception $ex) {
            throw new RuntimeException($ex->getMessage());
        }
    }

    /**
     * Load comments from thread
     * 
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public function loadThreadComments($params)
    {
        if (!isset($params['id']) || !isset($params['url'])) {
            throw new InvalidArgumentException('Options identifier and url are required!');
        }
        
        $options = $this->getOptions();
        $params = $this->pluginUrlBuilder($params);
        $url = $this->view->serverUrl() . $params['url'];
        $identifier = md5($params['id']);
        $shortname = $options->getShortname();
        
        $html = '';
        self::$loadComInstances += 1;
        if (self::$loadComInstances === 1) {
            $html = <<<HTML
<div id="disqus_thread"></div>
<script>
    var disqus_config = function () {
        this.page.url = '{$url}';
        this.page.identifier = '{$identifier}';
    };
    
    (function() {
        var d = document, s = d.createElement('script');
        
        s.src = '//{$shortname}.disqus.com/embed.js';
        
        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
HTML;
        }
        
        return $html;
    }
    
    /**
     * Load comment count from thread link
     * 
     * @param array $params
     * @return string html link
     * @throws InvalidArgumentException
     */
    public function commentsCount($params)
    {
        if (!isset($params['id']) || !isset($params['url'])) {
            throw new InvalidArgumentException('Expected option "id" and "url"!');
        }
        
        $params = $this->pluginUrlBuilder($params);
        $sitename = $this->getOptions()->getShortname();
        $pageId = $this->generatePageIdentifier($params['id']);
        
        self::$comCountInstances += 1;
        if (self::$comCountInstances == 1) {
            $url = '//' . $sitename . '.disqus.com/count.js';
            $this->getView()->inlineScript()
                    ->setAllowArbitraryAttributes(true)
                    ->appendFile($url, null, ['id' => 'dsq-count-scr', 'async' => '']);
        }
        
        return sprintf('<a href="http://%s#disqus_thread" data-disqus-identifier="%s">%s</a>', 
                $_SERVER['SERVER_NAME'] . $params['url'],
                $pageId,
                isset($params['text']) ? $params['text'] : ''
        );
    }

    /**
     * @param array $params
     * @return void
     */
    protected function pluginUrlBuilder(array $params)
    {
        if (isset($params['url']) && is_array($params['url'])) {
            $urlOptions = array_merge(
                array('name' => null, 'params' => array(), 'options' => array(), 'reuseMatchedParams' => false),
                $params['url']
            );
            $params['url'] = $this->getView()->url($urlOptions['name'], $urlOptions['params'], $urlOptions['options'], $urlOptions['reuseMatchedParams']);
        }

        return $params;
    }
    
    /**
     * Generate page item identifier
     * @param string $id
     * @return string
     */
    protected function generatePageIdentifier($id)
    {
        return md5($this->getOptions()->getIdentifier() . '_' . $id);
    }

    /**
     * @param CommentServiceOptionsInterface $options
     */
    public function setOptions(CommentServiceOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * @return CommentServiceOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof CommentServiceOptionsInterface) {
            throw new InvalidArgumentException('Missing CommentServiceOptions class in setOptions!');
        }
        return $this->options;
    }

}
