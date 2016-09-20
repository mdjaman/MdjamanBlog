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

use Zend\InputFilter\InputFilter;

/**
 * Description of CategoryFilter
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class CategoryFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'       => 'name',
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
            'name'       => 'descr',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
        ));
        $this->add(array(
            'name'       => 'image',
            'required'   => false,
            'filters'	 => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            ),
        ));
    }
}
