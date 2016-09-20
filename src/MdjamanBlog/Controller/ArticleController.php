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
use MdjamanBlog\Service\TagServiceInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Model\FeedModel;

/**
 * Class ArticleController
 * @package MdjamanBlog\Controller
 */
class ArticleController extends AbstractActionController
{

    /**
     * @var ArticleServiceInterface
     */
    protected $articleService;

    /**
     * @var CategoryServiceInterface
     */
    protected $categoryService;

    /**
     * @var TagServiceInterface
     */
    protected $tagService;

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;

    /**
     * Fetches list of articles
     * @return mixed|ViewModel|JsonModel
     */
    public function indexAction()
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
        
        $filter = $this->params()->fromQuery('filter');
        $value = $this->params()->fromQuery('value');

        $sort = $this->params()->fromQuery('sort', 'publishDate');
        $dir = $this->params()->fromQuery('dir', 'desc');
 
        $filArray = ['filter', 'value', 'sort', 'dir', 'limit', 'offset'];
        $filters = compact($filArray);
        
        $service = $this->getArticleService();
        
        $entities = $service->filter($filters);
        
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
            $resultJson['total'] = $service->getRepository()->countResult([$filter => $value]);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'article' => $entities,
        ]);
    }

    /**
     * Fetch an article
     * @return mixed|JsonModel|ViewModel
     */
    public function viewAction()
    {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('alias', '');
        $fromAdmin = $this->params()->fromQuery('fromAdmin', 0);
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];
        
        if ($id === '') {
            return $this->forward()->dispatch('MdjamanBlog\Controller\Article', ['action' => 'index']);
        }
        
        $service = $this->getArticleService();
        
        try {
            $active = ($fromAdmin === 0) ? true : false;
            $entity = $service->getRepository()->findOneByAlias($id, $active);
            if (!$entity) {
                $message = sprintf(_('Article %s introuvable'), $id);
                throw new Exception\ArticleNotFoundException($message);
            }
        } catch (Exception\ArticleNotFoundException $ex) {
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
            return $this->forward()->dispatch('MdjamanBlog\Controller\Article', ['action' => 'index']);
        }
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($entity, 'json', 'details');
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'article' => $entity,
        ]);
    }

    /**
     * Fetch articles from category
     * @return mixed|JsonModel|ViewModel
     * @throws Exception\CategoryNotFoundException
     */
    public function categoryAction()
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
            return $this->forward()->dispatch('MdjamanBlog\Controller\Article', ['action' => 'index']);
        }
        
        $service = $this->getArticleService();
        
        try {
            $categoryService = $this->getCategoryService();
            $category = $categoryService->findOneBy(['alias' => $id]);
            
            if (!$category) {
                $message = sprint(_('Catégorie %s introuvable'), $id);
                throw new Exception\CategoryNotFoundException($message);
            }
            
            $entities = $service->getRepository()->findArticleByCategory($category->getId(), true, null, $sort, $dir, $limit, $offset);
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
            return $this->forward()->dispatch('MdjamanBlog\Controller\Article', ['action' => 'index']);
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
            //$resultJson['total'] = $service->getRepository()->countResult(['category' => $category->getId()]);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'category' => $category,
            'article' => $entities,
        ]);
    }

    /**
     * @return mixed|JsonModel|ViewModel
     * @throws Exception\TagNotFoundException
     */
    public function tagAction()
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
            return $this->forward()->dispatch('MdjamanBlog\Controller\Article', ['action' => 'index']);
        }
        
        $service = $this->getArticleService();
        
        try {
            $tagService = $this->getTagService();
            $tag = $tagService->findOneBy(['alias' => $id]);
            
            if (!$tag) {
                $message = sprintf(_('Tag %s introuvable'), $id);
                throw new Exception\TagNotFoundException($message);
            }
            
            $entities = $service->getRepository()->findArticleByTags($tag->getId(), true, $sort, $dir, $limit, $offset);
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
            return $this->forward()->dispatch('MdjamanBlog\Controller\Article', ['action' => 'index']);
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
            //$resultJson['total'] = $service->getRepository()->countResult(['tags' => $tag->getId()]);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'tag' => $tag,
            'article' => $entities,
        ]);
    }
    
    public function feedAction()
    {
        $limit    = $this->getOptions()->getFeedListingLimit();
        $repository = $this->getArticleService()->getRepository();
        $articles = $repository->findArticles(true, 'publishDate', $dir = 'desc', $limit);
        $model = new FeedModel;
        $model->setOption('feed_type', $this->params('type', 'rss'));
        // Convert articles listing into feed
        $feedSettings       = $this->getOptions()->getFeedSettings();
        $model->title       = $feedSettings['title'];
        $model->description = $feedSettings['description'];
        $model->link        = $this->url()->fromRoute('mdjaman-blog', array(), array('force_canonical' => true));
        $model->feed_link   = array(
            'link' => $this->url()->fromRoute('mdjaman-blog/feed', array(), array('force_canonical' => true)),
            'type' => $this->params('type', 'rss'),
        );
        if (null !== ($generator = $this->getOptions()->getFeedGenerator())) {
            $model->generator = $generator;
        }
        $entries   = array();
        $modified  = new \DateTime('@0');
        foreach ($articles as $article) {
            $entry = array(
                'title'        => $article->getTitle(),
                'description'  => $article->getContent(),
                'date_created' => $article->getPublishDate(),
                'link'         => $this->url()->fromRoute(
                    'mdjaman-blog/view',
                    array('alias' => $article->getAlias()),
                    array('force_canonical' => true)
                ),
        //        author' => array(
        //             'name'  => 'WebMaster SantéFuté',
        //             'email' => 'jurian@juriansluiman.nl', // optional
        //             'uri'   => 'http://juriansluiman.nl', // optional
        //         ),
            );
            if ($article->getPublishDate() > $modified) {
                $modified = $article->getPublishDate();
            }
            $entries[] = $entry;
        }
        $model->entries       = $entries;
        $model->date_modified = $modified;
        return $model;
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
     * @return TagServiceInterface
     */
    public function getTagService()
    {
        return $this->tagService;
    }

    /**
     * @param TagServiceInterface $tagService
     * @return $this
     */
    public function setTagService(TagServiceInterface $tagService)
    {
        $this->tagService = $tagService;
        return $this;
    }

    /**
     * set options
     *
     * @param ModuleOptionsInterface $options
     * @return ArticleController
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
