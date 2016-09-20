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

use MdjamanBlog\Service\ArticleService;
use Zend\Cache\Storage\StorageInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Description of FeaturedSlider
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class FeaturedSlider extends AbstractHelper
{
    
    public static $instance = 0;
    
    protected $articleService;
    
    protected $cache;
    
    protected $cacheKey = 'featuredNewsSlider';
    
    public function __construct(ArticleService $articleService, StorageInterface $cache)
    {
        $this->articleService = $articleService;
        $this->cache = $cache;
    }
    
    public function __invoke($sort = 'publishDate', $dir = 'desc', $limit = 5)
    {
        $view = $this->getView();
        self::$instance += 1;
        if (self::$instance === 1) {
            $view->inlineScript()
                ->appendFile($view->basePath('js/jssor.slider.mini.js'))
                ->appendFile($view->basePath('js/jssor.js'));
        }
        
        if ($this->cache->hasItem($this->cacheKey)) {
            return $this->cache->getItem($this->cacheKey);
        }
        
        $articles = $this->getFeaturedArticles($sort, $dir, $limit);
        $html = <<<HTML
<div id="jssor_1" class="jscontainer">
    <div data-u="loading" class="jsloaoder-container">
        <div class="jsdivfirstdic"></div>
        <div class="jsImgLoader"></div>
    </div>
HTML;
        if (count($articles)) {
            $html .= PHP_EOL . '<div data-u="slides" class="slidercontainer">' . PHP_EOL;
            foreach ($articles as $item) {
                $picture = ($item->getImg()) ? $item->getImg() : '/img/default.png';
                $title = $view->truncate($item->getTitle(), 50);
                $description = $view->truncate($item->getDescription(), 100);
                $html .= <<<HTML
<div data-p="112.50" style="display: none;">
    <img data-u="image" src="{$picture}">
    <div class="slidetitle"><a href="{$view->url('mdjaman-blog/view', ['alias' => $item->getAlias()])}">{$title}</a></div>
    <div class="slidecontent"><a href="{$view->url('mdjaman-blog/view', ['alias' => $item->getAlias()])}">{$description}</a></div>
    <div data-u="thumb">
        <img class="i" src="{$view->thumb($picture, '_mini')}">
        <div class="t">{$title}</div>
    </div>
</div>
HTML;
            }
            $html .= '</div>' . PHP_EOL;
        }
        
        $html .= <<<HTML
<!-- Thumbnail Navigator -->
<div data-u="thumbnavigator" class="jssort11 Thumbnail-nav" data-autocenter="2">
    <!-- Thumbnail Item Skin Begin -->
    <div data-u="slides" style="cursor: default;">
        <div data-u="prototype" class="p">
            <div data-u="thumbnailtemplate" class="tp"></div>
        </div>
    </div>
    <!-- Thumbnail Item Skin End -->
</div>
<!-- Arrow Navigator -->
<span data-u="arrowleft" class="jssora02l arrow-left-pos"  data-autocenter="2"></span>
<span data-u="arrowright" class="jssora02r arrow-right-pos" data-autocenter="2"></span>
HTML;
        $html .= '</div>' . PHP_EOL;
        
        $this->cache->addItem($this->cacheKey, $html);
        return $html;
    }
    
    /**
     * @param int $limit
     * @return mixed
     */
    public function getFeaturedArticles($sort = 'publishDate', $dir = 'desc', $limit = 5)
    {
        $repository = $this->articleService->getRepository();
        return $repository->getFeaturedArticles(true, $sort, $dir, $limit);
    }
    
}
