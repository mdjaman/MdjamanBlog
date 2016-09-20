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
use MdjamanBlog\Service\CategoryService;
use Zend\View\Helper\AbstractHelper;

/**
 * Description of FeaturedArticleCategory
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class FeaturedArticleCategory extends AbstractHelper
{
    protected $categoryService;
    
    protected $articleService;


    public function __construct(CategoryService $categoryService, ArticleService $articleService)
    {
        $this->categoryService = $categoryService;
        $this->articleService = $articleService;
    }
    
    public function __invoke($sort = 'id', $dir = 'desc', $limit = 5)
    {
        $repository = $this->categoryService->getRepository();
        $features = $repository->findFeatures($sort, $dir, $limit);
        $view = $this->getView();
        
        $articleRepository = $this->articleService->getRepository();
        foreach ($features as $category) {
            $articles = $articleRepository->findArticleByCategory($category, true, null, 'id', 'desc', 3);
            echo $view->partial('mdjaman-blog/_partials/featured_category.phtml', [
                'articles' => $articles, 
                'category' => $category
            ]);
        }
        
        return;
    }
    
}
