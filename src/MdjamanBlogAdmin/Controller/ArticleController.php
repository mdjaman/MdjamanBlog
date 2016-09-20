<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Marcel Djaman
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace MdjamanBlogAdmin\Controller;

use MdjamanBlog\Exception;
use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanBlog\Service\ArticleServiceInterface;
use MdjamanBlogAdmin\Form\ArticleFormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class ArticleController
 * @package MdjamanBlogAdmin\Controller
 */
class ArticleController extends AbstractActionController
{

    /**
     * @var ArticleFormInterface
     */
    protected $articleForm;

    /**
     * @var ArticleServiceInterface
     */
    protected $articleService;

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
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];
        
        $page    = $this->params()->fromQuery('page', 1);
        $limit   = $this->params()->fromQuery('limit', $this->getOptions()->getAdminListingLimit());
        $offsetParam  = $this->params()->fromQuery('offset');
        $offset  = isset($offsetParam) ? $offsetParam : ($page - 1) * $limit;
        
        $filter = $this->params()->fromQuery('filter');
        $value = $this->params()->fromQuery('value');

        $sort = $this->params()->fromQuery('sort', 'id');
        $dir = $this->params()->fromQuery('dir', 'desc');
 
        $service = $this->getArticleService();

        $criteria = [];
        if (null !== $filter && null !== $value) {
            $criteria = [$filter => $value];
        }
        $entities = $service->findBy($criteria, [$sort => $dir], $limit, $offset);
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($entities, 'json', 'details');
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
        $id = $this->params()->fromRoute('id', 0);
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];
        
        if ($id == 0) {
            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Article', ['action' => 'index']);
        }
        
        $service = $this->getArticleService();
        
        try {
            $document = $service->findOneBy(['id' => $id]);
            if (!$document) {
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
            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Article', ['action' => 'index']);
        }
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($document);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'article' => $document,
        ]);
    }

    /**
     * Create an article
     * @return \Zend\Http\Response|JsonModel|ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $form = $this->getArticleForm();
        $service = $this->getArticleService();
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->bind($service->createEntity());
            
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    $article = $service->saveArticleWithTags($form->getData(), $data['hidden-tags']);
                } catch (\Exception $exc) {
                    if ($request->isXmlHttpRequest()) {
                        return new JsonModel($resultJson);
                    }
                    
                    return new ViewModel(array(
                        'form' => $form,
                    ));
                }

                $message = sprintf(_('Article %s ajouté avec succès'), $article->getTitle());
                if ($request->isXmlHttpRequest()) {
                    $resultJson['code'] = 1;
                    $resultJson['msg'] = $message;
                    $resultJson['data'] = $service->serialize($article);
                    return new JsonModel($resultJson);
                }

                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute('zfcadmin/blog/article');
            }

            if ($request->isXmlHttpRequest()) {
                return new JsonModel($resultJson);
            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    /**
     * Update an article
     * @return \Zend\Http\Response|JsonModel|ViewModel
     */
    public function editAction()
    {
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');

        $service = $this->getArticleService();

        try {
            $document = $service->find($id);
            if (!$document) {
                throw new Exception\ArticleNotFoundException(sprintf(_('Article %s introuvable'), $id), 404);
            }
        } catch (Exception\ArticleNotFoundException $ex) {
            $msg = sprintf(
                "%s:%d %s (%d) [%s]\n", $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getCode(), get_class($ex)
            );
            $service->getLogger()->warn($msg);
            $errMsg = $ex->getMessage();
            $this->flashMessenger()->addMessage($errMsg);

            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $ex->getMessage();
                return new JsonModel($resultJson);
            }

            return $this->redirect()->toRoute('zfcadmin/blog/article');
        }

        $form = $this->getArticleForm();
        $form->bind($document);

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                try {
                    $article = $service->saveArticle($form->getData());
                    if (!$article) {
                        throw new Exception\InvalidArgumentException(sprintf(_('Echec mise à jour de l\'article %s'), $id), 500);
                    }

                    $message = sprintf(_('Article %s mis à jour avec succès'), $article->getTitle());

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['code'] = 1;
                        $resultJson['msg'] = $message;
                        $resultJson['data'] = $service->serialize($article);
                        return new JsonModel($resultJson);
                    }

                    return $this->redirect()->toRoute('zfcadmin/blog/article/view', [
                        'id' => $article->getId(),
                    ]);
                } catch (Exception\InvalidArgumentException $ex) {
                    $msg = sprintf(
                        "%s:%d %s (%d) [%s]\n", $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getCode(), get_class($ex)
                    );
                    $service->getLogger()->err($msg);
                    $errMsg = $ex->getMessage();
                    $this->flashMessenger()->addMessage($errMsg);

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['msg'] = $errMsg;
                        return new JsonModel($resultJson);
                    }

                    return new ViewModel(array(
                        'form' => $form,
                        'article' => $document,
                    ));
                }
            }

            return new JsonModel($resultJson);
        }

        return new ViewModel(array(
            'form' => $form,
            'article' => $document
        ));
    }

    /**
     * Delete an article
     * @return mixed|\Zend\Http\Response|JsonModel|ViewModel
     */
    public function deleteAction()
    {
        $service = $this->getArticleService();
        $id = $this->params()->fromRoute('id', 0);
        $request = $this->getRequest();
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];

        try {
            $document = $service->find($id);
            if (!$document) {
                throw new Exception\ArticleNotFoundException(sprintf(_('Article %s introuvable'), $id), 404);
            }
        } catch (Exception\ArticleNotFoundException $ex) {
            $msg = sprintf(
                "%s:%d %s (%d) [%s]\n", $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getCode(), get_class($ex)
            );
            $service->getLogger()->warn($msg);

            $errMsg = $ex->getMessage();
            $this->flashMessenger()->addMessage($errMsg);

            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $errMsg;
                return new JsonModel($resultJson);
            }

            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Article', array('action' => 'index'));
        }

        if ($request->isPost()) {
            $del = $request->getPost('delete', 'no');
            if ($del == 'yes') {
                $id = $request->getPost('id');
                try {
                    $service->delete($document, true);
                } catch (\Exception $e) {
                    $msg = sprintf(_('Suppression article %s impossible'), $id);
                    $service->getLogger()->warn($msg);
                    $this->flashMessenger()->addMessage($msg);

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['msg'] = $msg;
                        return new JsonModel($resultJson);
                    }

                    return $this->redirect()->toRoute('zfcadmin/blog/article');
                }

                $message = sprintf(_('Article %s supprimé avec succès'), $document->getTitle());
                if ($request->isXmlHttpRequest()) {
                    $resultJson['code'] = 1;
                    $resultJson['msg'] = $message;
                    $resultJson['data'] = $service->serialize($document);
                    return new JsonModel($resultJson);
                }
            }
            return $this->redirect()->toRoute('zfcadmin/blog/article');
        }

        return new ViewModel(array(
            'id' => $id,
            'article' => $document,
        ));
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
     * @return ArticleServiceInterface
     */
    public function getArticleService()
    {
        return $this->articleService;
    }

    /**
     * @param ArticleFormInterface $articleForm
     * @return $this
     */
    public function setArticleForm(ArticleFormInterface $articleForm)
    {
        $this->articleForm = $articleForm;
        return $this;
    }

    /**
     * @return ArticleFormInterface
     */
    public function getArticleForm()
    {
        return $this->articleForm;
    }

    /**
     * @param ModuleOptionsInterface $options
     * @return ArticleController
     */
    public function setOptions(ModuleOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return ModuleOptionsInterface
     */
    public function getOptions()
    {
        return $this->options;
    }

}
