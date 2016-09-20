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

namespace MdjamanBlogAdmin\Form;

use MdjamanBlog\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanCommon\Form\BaseForm;

/**
 * Description of ArticleForm
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class ArticleForm extends BaseForm implements ArticleFormInterface
{

    /**
     * ArticleForm constructor.
     * @param ObjectManager $om
     * @param ModuleOptionsInterface $moduleOptions
     */
    public function __construct(ObjectManager $om, ModuleOptionsInterface $moduleOptions)
    {
        $entityClass = $moduleOptions->getArticleEntityClass();
        parent::__construct('article', true);
        $this->setHydrator(new DoctrineObject($om))
             ->setObject(new $entityClass);

        $this->add([
            'name' => 'title',
            'attributes' => [
                'required' => 'required',
                'placeholder' => _('Titre'),
                'id' => 'title'
            ],
            'options' => [
                'label' => _('Titre'),
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2'),
                'twb-layout' => 'horizontal',
            ]
        ]);
        $this->add([
            'name' => 'content',
            'attributes' => [
                'type' => 'textarea',
                'placeholder' => _('Texte'),
                'id' => 'content',
                'class' => 'ckeditor',
            ],
            'options' => [
                'label' => _('Texte'),
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2'),
                'twb-layout' => 'horizontal',
            ]
        ]);
        $this->add([
            'name' => 'publishDate',
            'attributes' => [
                'type' => 'text',
                'placeholder' => _('JJ-MM-AAAA'),
                'id' => 'publishDate',
                'class' => 'datepicker',
            ],
            'options' => [
                'label' => _('Date publication'),
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2'),
                'twb-layout' => 'horizontal',
            ]
        ]);
        $this->add([
            'name' => 'src',
            'attributes' => [
                'placeholder' => _('Source'),
                'id' => 'src',
            ],
            'options' => [
                'label' => _('Source'),
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2'),
                'twb-layout' => 'horizontal',
            ]
        ]);
        $this->add([
            'name' => 'category',
            'type' => ObjectSelect::class,
            'attributes' => [
                'required' => false,
                'id' => 'category',
                'class' => 'chzn-select',
            ],
            'options' => [
                'label' => 'Catégorie',
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2'),
                'empty_option' => _('-- Choix catégorie --'),
                'object_manager' => $om,
                'target_class' => Category::class,
                'property' => 'name',
                'is_method' => true,
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => [],
                        'orderBy' => ['name' => 'ASC'],
                    ],
                ],
                'twb-layout' => 'horizontal',
            ],
        ]);
        $this->add([
            'name' => 'tags',
            'attributes' => [
                'placeholder' => _('Mots-clés'),
                'id' => 'tags',
                'class' => 'tm-input',
            ],
            'options' => [
                'label' => _('Mots-clés'),
                'help-block' => _('Utilisez les touches TAB ou VIRGULE comme séparateurs'),
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2'),
                'twb-layout' => 'horizontal',
            ]
        ]);
        $this->add([
            'name' => 'img',
            'attributes' => [
                'id' => 'img',
            ],
            'options' => [
                'label' => 'Image',
                'column-size' => 'sm-10',
                'label_attributes' => array('class' => 'col-sm-2'),
                'twb-layout' => 'horizontal',
            ]
        ]);
        $this->add([
            'name' => 'cmtopen',
            'type' => 'radio',
            'attributes' => [
                'value' => 1,
            ],
            'options' => [
                'value_options' => [
                    1 => _('Autoriser les commentaires'),
                    0 => _('Interdire les commentaires'),
                ],
                'twb-layout' => 'horizontal',
                'column-size' => 'sm-10 col-sm-offset-2',
            ]
        ]);
        $this->add([
            'name' => 'active',
            'type' => 'checkbox',
            'attributes' => [
                'value' => 1,
            ],
            'options' => [
                'label' => _('Activer'),
                'column-size' => 'sm-10 col-sm-offset-2',
                'twb-layout' => 'horizontal',
            ],
        ]);
        $this->add([
            'name' => 'feature',
            'type' => 'checkbox',
            'attributes' => [
                'value' => 0,
            ],
            'options' => [
                'label' => _('Mettre en avant'),
                'column-size' => 'sm-10 col-sm-offset-2',
                'twb-layout' => 'horizontal',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'button',
            'attributes' => array('type' => 'submit'),
            'options' => array(
                'ignore' => true,
                'label' => _('Enregistrer'),
                'column-size' => 'sm-10 col-sm-offset-2',
                'twb-layout' => 'horizontal',
            )
        ]);
    }
}
