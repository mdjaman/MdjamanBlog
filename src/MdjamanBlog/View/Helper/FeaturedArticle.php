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
 * Description of FeaturedArticle
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class FeaturedArticle extends AbstractHelper
{
    protected $articleService;

    protected $cache;
    
    protected $cacheKey = 'featuredArticle';
    

    public function __construct(ArticleService $articleService, StorageInterface $cache)
    {
        $this->articleService = $articleService;
        $this->cache = $cache;
    }
    
    public function __invoke($sort = 'publishDate', $dir = 'desc', $limit = 8)
    {
        $cacheKey = $this->cacheKey . md5(json_encode(func_get_args()));
        $this->setCacheKey($cacheKey);
        if ($this->cache->hasItem($this->cacheKey)) {
            return $this->cache->getItem($this->cacheKey);
        }
        
        $view = $this->getView();
        $articleRepository = $this->articleService->getRepository();
        $articles = $articleRepository->findArticles(true, $sort, $dir, $limit);
        //$articles = $articleRepository->getFeaturedArticles(true, $sort, $dir, $limit);
        
        $html = $view->partial('mdjaman-blog/_partials/featured_articles.phtml', ['articles' => $articles]);
        
        $this->cache->addItem($this->cacheKey, $html);
        return $html;
    }
    
    /**
     * Set the cache key
     * @param string $cacheKey
     * @return FeaturedArticle
     */
    protected function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }
    
}
