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

use MdjamanBlog\Options\SocialSharingOptionsInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Description of AddThis
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class AddThis extends AbstractHelper
{

    static $instances = 0;
    
    protected $options;
    
    protected $facebook = '<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>';
    protected $twitter = '<a class="addthis_button_tweet"></a>';
    protected $google_plus = '<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>';
    protected $linkedin = '<a class="addthis_button_linkedin_counter"></a>';

    /**
     *  Return AddThis social sharing toolkit
     * 
     *  @param $url uri of page to share
     *  @param $title title of page
     *  @param $anews render social addthis at the end of the news
     *  @return string
     */
    public function __invoke($params = [])
    {
        if (!isset($params['domain']) || !isset($params['url']) || !isset($params['title'])) {
            throw new \InvalidArgumentException('Options domain, url and title are required!');
        }
        
        $link = sprintf('http://%s/', 
                $params['domain'],
                $params['url']
        );
        $html = <<<EOT
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style" addthis:url="{$link}" addthis:title="{$params['title']}">
EOT;
        foreach ($this->getOptions()->getNetworks() as $network) {
            if (isset($this->$network)) {
                $html .= $this->$network . PHP_EOL;
            }
        }
        $html .= "</div>\n <!-- AddThis Button END -->\n";

        self::$instances += 1;
        if (self::$instances === 1) {
            if ($this->getOptions()->getAnalytics()) {
                $analytics = $this->getOptions()->getAnalytics();
                if (isset($analytics['code'])) {
                    $isSocial = isset($analytics['social']) ? $analytics['social'] : false;
                    $tracking = <<<SCRIPT
var addthis_config = {
    data_ga_property: '{$analytics['code']}',
    data_ga_social: '{$isSocial}'
};
SCRIPT;
                $this->pluginHeadScript()->appendScript($tracking);
                }
            }

            $src = "//s7.addthis.com/js/300/addthis_widget.js#pubid=" . $this->getOptions()->getId();
            $this->pluginHeadScript()->appendFile($src);
        }
        
        return $html;
    }

    /**
     * @return \Zend\View\Helper\HeadScript
     */
    protected function pluginHeadScript()
    {
        return $this->getView()->headScript();
    }

    /**
     * @param SocialSharingOptionsInterface $options
     */
    public function setOptions(SocialSharingOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * @return SocialSharingOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof SocialSharingOptionsInterface) {
            throw new \InvalidArgumentException('Missing SocialSharingOptions class in setOptions!');
        }
        return $this->options;
    }

}
