<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MdjamanBlog\Controller;

use MdjamanBlog\Exception;
use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanBlog\Service\ArticleServiceInterface;
use MdjamanBlog\Service\CategoryServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CategoryController extends AbstractActionController
{

    /**
     * @var CategoryServiceInterface
     */
    protected $categoryService;

    /**
     * @var ArticleServiceInterface
     */
    protected $articleService;

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;
    
    /**
     * Fetches list of categories
     * @return mixed|ViewModel|JsonModel
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];
        
        $page    = $this->params()->fromQuery('page', 1);
        $limit   = $this->params()->fromQuery('limit', $this->getOptions()->getArchiveListingLimit());
        $offsetParam  = $this->params()->fromQuery('offset');
        $offset  = isset($offsetParam) ? $offsetParam : ($page - 1) * $limit;
        
        $filter = $this->params()->fromQuery('filter');
        $value = $this->params()->fromQuery('value');

        $sort = $this->params()->fromQuery('sort', 'id');
        $dir = $this->params()->fromQuery('dir', 'desc');
 
        $filArray = ['filter', 'value', 'sort', 'dir', 'limit', 'offset'];
        $filters = compact($filArray);
        
        $service = $this->getCategoryService();
        
        $entities = $service->filter($filters);
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($entities, 'json', 'details');
            $resultJson['total'] = $service->getRepository()->countResult([$filter => $value]);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'category' => $entities,
        ]);
    }

    /**
     * Fetch a category
     * @return mixed|JsonModel|ViewModel
     */
    public function viewAction()
    {
        $request = $this->getRequest();
        $viewQuery = $this->params()->fromQuery('viewHtml', 0);
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];
        
        $page    = $this->params()->fromQuery('page', 1);
        $limit   = $this->params()->fromQuery('limit', $this->getOptions()->getArchiveListingLimit());
        $offsetParam  = $this->params()->fromQuery('offset');
        $offset  = isset($offsetParam) ? $offsetParam : ($page - 1) * $limit;
        
        $sort = $this->params()->fromQuery('sort', 'id');
        $dir = $this->params()->fromQuery('dir', 'desc');

        $id = $this->params()->fromRoute('alias', '');
        
        if ($id === '') {
            return $this->forward()->dispatch('MdjamanBlog\Controller\Category', ['action' => 'index']);
        }
        
        $service = $this->getCategoryService();
        
        try {
            $category = $service->findOneBy(['alias' => $id]);
            if (!$category) {
                $message = sprint(_('Catégorie %s introuvable'), $id);
                throw new Exception\CategoryNotFoundException($message);
            }
            
            $articleService = $this->getArticleService();
            $entities = $articleService->getRepository()->findArticleByCategory($category, true, null, $sort, $dir, $limit, $offset);
        } catch (Exception\InvalidArgumentException $ex) {
            $msg = sprintf(
                "%s:%d %s (%d) [%s]\n", $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getCode(), get_class($ex)
            );
            $service->getLogger()->warn($msg);
            $errMessage = $ex->getMessage();

            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $errMessage;
                return new JsonModel($resultJson);
            }
            
            $this->flashMessenger()->addMessage($errMessage);
            return $this->forward()->dispatch('MdjamanBlog\Controller\Category', ['action' => 'index']);
        }
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($entities, 'json', 'details');
            
            if ($viewQuery) {
                $htmlViewPart = new ViewModel();
                $htmlViewPart->setTerminal(true)
                             ->setTemplate('mdjaman-blog/_partials/paginate/article')
                             ->setVariables(array(
                                'article' => $entities,
                             ));

                $viewRenderer = $this->getServiceLocator()->get('ViewRenderer');
                $htmlOutput = $viewRenderer->render($htmlViewPart);
                
                $resultJson['html'] = $htmlOutput;
            }
            
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'category' => $category,
            'article' => $entities,
        ]);
    }

    /**
     * @return CategoryServiceInterface
     */
    public function getCategoryService()
    {
        return $this->categoryService;
    }

    /**
     * @param CategoryServiceInterface $categoryService
     * @return $this
     */
    public function setCategoryService(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
        return $this;
    }

    /**
     * @return ArticleServiceInterface
     */
    public function getArticleService()
    {
        return $this->articleService;
    }

    /**
     * @param ArticleServiceInterface $articleService
     * @return $this
     */
    public function setArticleService(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
        return $this;
    }

    /**
     * set options
     *
     * @param ModuleOptionsInterface $options
     * @return $this
     */
    public function setOptions(ModuleOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * get options
     *
     * @return ModuleOptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }

}
