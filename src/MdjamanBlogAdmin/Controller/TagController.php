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

use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanBlog\Service\TagServiceInterface;
use MdjamanBlogAdmin\Form\TagFormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class TagController
 * @package MdjamanBlogAdmin\Controller
 */
class TagController extends AbstractActionController
{

    /**
     * @var TagFormInterface
     */
    protected $tagForm;

    /**
     * @var TagServiceInterface
     */
    protected $tagService;

    /**
     * @var ModuleOptionsInterface
     */
    protected $options;
    
    /**
     * Fetches list of tags
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
 
        $filArray = ['filter', 'value', 'sort', 'dir', 'limit', 'offset'];
        $filters = compact($filArray);
        
        $service = $this->getTagService();
        
        $entities = $service->filter($filters);
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($entities, 'json', 'list');
            $resultJson['total'] = $service->getRepository()->countResult([$filter => $value]);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'tag' => $entities,
        ]);
    }

    /**
     * Fetch a tag
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
            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Tag', ['action' => 'index']);
        }
        
        $service = $this->getTagService();
        
        try {
            $document = $service->find($id);
            if (!$document) {
                $message = sprintf(_('Tag %s introuvable'), $id);
                throw new \Exception($message);
            }
        } catch (\Exception $e) {
            $errMessage = $e->getMessage();
            $service->getLogger()->warn($errMessage);
            
            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $errMessage;
                return new JsonModel($resultJson);
            }
            
            $this->flashMessenger()->addMessage($errMessage);
            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Tag', ['action' => 'index']);
        }
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($document);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'tag' => $document,
        ]);
    }

    /**
     * Add a tag
     * @return \Zend\Http\Response|JsonModel|ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $form = $this->getTagForm();
        $service = $this->getTagService();
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
                    $tag = $service->saveTag($form->getData());
                } catch (\Exception $exc) {
                    if ($request->isXmlHttpRequest()) {
                        return new JsonModel($resultJson);
                    }
                    
                    return new ViewModel(array(
                        'form' => $form,
                    ));
                }

                $message = sprintf(_('Tag %s ajouté avec succès'), $tag->getName());
                if ($request->isXmlHttpRequest()) {
                    $resultJson['code'] = 1;
                    $resultJson['msg'] = $message;
                    $resultJson['data'] = $service->serialize($tag);
                    return new JsonModel($resultJson);
                }

                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute('zfcadmin/blog/tag');
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
     * Update a tag
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

        $service = $this->getTagService();

        try {
            $document = $service->find($id);
            if (!$document) {
                throw new \Exception(sprintf(_('Tag %s introuvable'), $id), 404);
            }
        } catch (\Exception $exc) {
            $service->getLogger()->warn($exc->getMessage());
            $this->flashMessenger()->addMessage($exc->getMessage());

            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $exc->getMessage();
                return new JsonModel($resultJson);
            }

            return $this->redirect()->toRoute('zfcadmin/blog/tag');
        }

        $form = $this->getTagForm();
        $form->bind($document);

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                try {
                    $tag = $service->saveTag($form->getData());
                    if (!$tag) {
                        throw new \Exception(sprintf(_('Echec mise à jour du tag %s'), $id), 500);
                    }

                    $message = sprintf(_('Tag %s mis à jour avec succès'), $tag->getName());

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['code'] = 1;
                        $resultJson['msg'] = $message;
                        $resultJson['data'] = $service->serialize($tag);
                        return new JsonModel($resultJson);
                    }

                    return $this->redirect()->toRoute('zfcadmin/blog/tag/view', [
                        'id' => $tag->getId(),
                    ]);
                } catch (\Exception $exc) {
                    $service->getLogger()->err($exc->getMessage());
                    $this->flashMessenger()->addMessage($exc->getMessage());

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['msg'] = $exc->getMessage();
                        return new JsonModel($resultJson);
                    }

                    return new ViewModel(array(
                        'form' => $form,
                        'tag' => $document,
                    ));
                }
            }

            return new JsonModel($resultJson);
        }

        return new ViewModel(array(
            'form' => $form,
            'tag' => $document
        ));
    }

    public function deleteAction()
    {
        $service = $this->getTagService();
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
                throw new \Exception(sprintf(_('Tag %s introuvable'), $id), 404);
            }
        } catch (\Exception $e) {
            $service->getLogger()->warn($e->getMessage());
            $this->flashMessenger()->addMessage($e->getMessage());

            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $e->getMessage();
                return new JsonModel($resultJson);
            }

            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Tag', array('action' => 'index'));
        }

        if ($request->isPost()) {
            $del = $request->getPost('delete', 'no');
            if ($del == 'yes') {
                $id = $request->getPost('id');
                try {
                    $service->delete($document, true);
                } catch (\Exception $e) {
                    $msg = sprintf(_('Suppression tag %s impossible'), $id);
                    $service->getLogger()->warn($msg);
                    $this->flashMessenger()->addMessage($msg);

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['msg'] = $msg;
                        return new JsonModel($resultJson);
                    }

                    return $this->redirect()->toRoute('zfcadmin/mdjaman-blog/tag');
                }

                $message = sprintf(_('Tag %s supprimé avec succès'), $document->getName());
                if ($request->isXmlHttpRequest()) {
                    $resultJson['code'] = 1;
                    $resultJson['msg'] = $message;
                    $resultJson['data'] = $service->serialize($document);
                    return new JsonModel($resultJson);
                }
            }
            return $this->redirect()->toRoute('zfcadmin/mdjaman-blog/tag');
        }

        return new ViewModel(array(
            'id' => $id,
            'tag' => $document,
        ));
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
     * @return TagServiceInterface
     */
    public function getTagService()
    {
        return $this->tagService;
    }

    /**
     * @param TagFormInterface $tagForm
     * @return $this
     */
    public function setTagForm(TagFormInterface $tagForm)
    {
        $this->tagForm = $tagForm;
        return $this;
    }

    /**
     * @return TagFormInterface
     */
    public function getTagForm()
    {
        return $this->tagForm;
    }

    /**
     * @param ModuleOptionsInterface $options
     * @return TagController
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
