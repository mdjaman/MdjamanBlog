<?php
/**
 * This file is part of the SanteFute project.
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MdjamanBlogAdmin\Form\Filter;

use Doctrine\Common\Persistence\ObjectRepository;
use DoctrineModule\Validator\ObjectExists;
use Zend\InputFilter\InputFilter;
use Zend\Validator\InArray;

/**
 * Description of ArticleFilter
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class ArticleFilter extends InputFilter
{

    /**
     * ArticleFilter constructor.
     * @param ObjectRepository $categoryRepository
     */
    public function __construct(ObjectRepository $categoryRepository)
    {
        $this->add(array(
            'name'       => 'title',
            'required'   => true,
            'filters'	 => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 3,
                        'max' => 100,
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name'       => 'content',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'StringTrim'),
            ),
        ));
        $this->add(array(
            'name'       => 'publishDate',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->add(array(
            'name'       => 'src',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->add(array(
            'name'       => 'category',
            'required'   => true,
            'filters'	 => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                new ObjectExists(array(
                    'object_repository' => $categoryRepository,
                    'fields' => array('id')
                )),
            ),
        ));
        $this->add(array(
            'name'       => 'img',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
        $this->add(array(
            'name'       => 'cmtopen',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'Int'),
            ),
            'validators' => array(
                new InArray(array(
                    'haystack' => array(0, 1)
                )),
            )
        ));
        $this->add(array(
            'name'       => 'active',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'Int'),
            ),
            'validators' => array(
                new InArray(array(
                    'haystack' => array(0, 1)
                )),
            )
        ));
        $this->add(array(
            'name'       => 'feature',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'Int'),
            ),
            'validators' => array(
                new InArray(array(
                    'haystack' => array(0, 1)
                )),
            )
        ));
    }
}
