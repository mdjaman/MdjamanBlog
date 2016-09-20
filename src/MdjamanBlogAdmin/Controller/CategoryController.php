<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MdjamanBlogAdmin\Controller;

use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanBlog\Service\CategoryServiceInterface;
use MdjamanBlogAdmin\Form\CategoryFormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class CategoryController
 * @package MdjamanBlogAdmin\Controller
 */
class CategoryController extends AbstractActionController
{

    /**
     * @var CategoryFormInterface
     */
    protected $categoryForm;

    /**
     * @var CategoryServiceInterface
     */
    protected $categoryService;

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
        $limit   = $this->params()->fromQuery('limit', $this->getOptions()->getAdminListingLimit());
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
            $resultJson['data'] = $service->serialize($entities, 'json', 'list');
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
        $id = $this->params()->fromRoute('id', 0);
        $resultJson = [
            'code' => 0,
            'msg' => 'There was some error. Try again.',
            'data' => null,
        ];
        
        if ($id == 0) {
            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Category', ['action' => 'index']);
        }
        
        $service = $this->getCategoryService();
        
        try {
            $category = $service->findBy(['id' => $id]);
            $document = $category[0];
            if (!$document) {
                $message = sprintf(_('Catégorie %s introuvable'), $id);
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
            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Category', ['action' => 'index']);
        }
        
        if ($request->isXmlHttpRequest()) {
            $resultJson['code'] = 1;
            $resultJson['msg'] = 'success';
            $resultJson['data'] = $service->serialize($document);
            return new JsonModel($resultJson);
        }
        
        return new ViewModel([
            'category' => $document,
        ]);
    }

    /**
     * Add a category
     * @return \Zend\Http\Response|JsonModel|ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $form = $this->getCategoryForm();
        $service = $this->getCategoryService();
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
                    $category = $service->saveCategory($form->getData());
                } catch (\Exception $exc) {
                    if ($request->isXmlHttpRequest()) {
                        return new JsonModel($resultJson);
                    }
                    
                    return new ViewModel(array(
                        'form' => $form,
                    ));
                }

                $message = sprintf('Categorie %s ajoutée avec succès', $category->getName());
                if ($request->isXmlHttpRequest()) {
                    $resultJson['code'] = 1;
                    $resultJson['msg'] = $message;
                    $resultJson['data'] = $service->serialize($category);
                    return new JsonModel($resultJson);
                }

                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute('zfcadmin/mdjaman-blog/category');
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
     * Update a category
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

        $service = $this->getCategoryService();

        try {
            $document = $service->find($id);
            if (!$document) {
                throw new \Exception(sprintf(_('Categorie %s introuvable'), $id), 404);
            }
        } catch (\Exception $exc) {
            $service->getLogger()->warn($exc->getMessage());
            $this->flashMessenger()->addMessage($exc->getMessage());

            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $exc->getMessage();
                return new JsonModel($resultJson);
            }

            return $this->redirect()->toRoute('zfcadmin/blog/category');
        }

        $form = $this->getCategoryForm();
        $form->bind($document);

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                try {
                    $category = $service->saveCategory($form->getData());
                    if (!$category) {
                        throw new \Exception(sprintf(_('Echec mise à jour de la catégorie %s'), $id), 500);
                    }

                    $message = sprintf(_('Catégorie %s mise à jour avec succès'), $category->getName());

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['code'] = 1;
                        $resultJson['msg'] = $message;
                        $resultJson['data'] = $service->serialize($category);
                        return new JsonModel($resultJson);
                    }

                    return $this->redirect()->toRoute('zfcadmin/blog/category/view', [
                        'id' => $category->getId(),
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
                        'category' => $document,
                    ));
                }
            }

            return new JsonModel($resultJson);
        }

        return new ViewModel(array(
            'form' => $form,
            'category' => $document
        ));
    }

    /**
     * Delete a category
     * @return mixed|\Zend\Http\Response|JsonModel|ViewModel
     */
    public function deleteAction()
    {
        $service = $this->getCategoryService();
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
                throw new \Exception(sprintf(_('Catégorie %s introuvable'), $id), 404);
            }
        } catch (\Exception $e) {
            $service->getLogger()->warn($e->getMessage());
            $this->flashMessenger()->addMessage($e->getMessage());

            if ($request->isXmlHttpRequest()) {
                $resultJson['msg'] = $e->getMessage();
                return new JsonModel($resultJson);
            }

            return $this->forward()->dispatch('MdjamanBlogAdmin\Controller\Category', array('action' => 'index'));
        }

        if ($request->isPost()) {
            $del = $request->getPost('delete', 'no');
            if ($del == 'yes') {
                $id = $request->getPost('id');
                try {
                    $service->delete($document, true);
                } catch (\Exception $e) {
                    $msg = sprintf(_('Suppression catégorie %s impossible'), $id);
                    $service->getLogger()->warn($msg);
                    $this->flashMessenger()->addMessage($msg);

                    if ($request->isXmlHttpRequest()) {
                        $resultJson['msg'] = $msg;
                        return new JsonModel($resultJson);
                    }

                    return $this->redirect()->toRoute('zfcadmin/blog/category');
                }

                $message = sprintf(_('Catégorie %s supprimée avec succès'), $document->getName());
                if ($request->isXmlHttpRequest()) {
                    $resultJson['code'] = 1;
                    $resultJson['msg'] = $message;
                    $resultJson['data'] = $service->serialize($document);
                    return new JsonModel($resultJson);
                }
            }
            return $this->redirect()->toRoute('zfcadmin/blog/category');
        }

        return new ViewModel(array(
            'id' => $id,
            'category' => $document,
        ));
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
     * @return CategoryServiceInterface
     */
    public function getCategoryService()
    {
        return $this->categoryService;
    }

    /**
     * @param CategoryFormInterface $categoryForm
     * @return $this
     */
    public function setCategoryForm(CategoryFormInterface $categoryForm)
    {
        $this->categoryForm = $categoryForm;
        return $this;
    }

    /**
     * @return CategoryFormInterface
     */
    public function getCategoryForm()
    {
        return $this->categoryForm;
    }

    /**
     * @param ModuleOptionsInterface $options
     * @return CategoryController
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
