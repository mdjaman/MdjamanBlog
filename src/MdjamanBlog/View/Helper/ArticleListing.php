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

use MdjamanBlog\Service\ArticleServiceInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Description of ArticleListing
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class ArticleListing extends AbstractHelper
{
    /**
     * @var int
     */
    const DEFAULT_ARTICLE_LIMIT = 10;

    /**
     * @var ArticleServiceInterface
     */
    protected $articleService;

    /**
     * ArticleListing constructor.
     * @param ArticleServiceInterface $articleService
     */
    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }
    
    public function __invoke($sort = 'publishDate', $dir = 'desc', $limit = null)
    {
        $limit = $limit ?: self::DEFAULT_ARTICLE_LIMIT;
        $repository = $this->articleService->getRepository();
        return $repository->findArticles(true, $sort, $dir, $limit);
    }
    
}
