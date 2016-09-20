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

use MdjamanBlog\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use MdjamanBlog\Options\ModuleOptionsInterface;
use MdjamanCommon\Form\BaseForm;

/**
 * Description of TagForm
 *
 * @author Marcel Djaman <marceldjaman@gmail.com>
 */
class TagForm extends BaseForm implements TagFormInterface
{

    /**
     * TagForm constructor.
     * @param ObjectManager $om
     * @param ModuleOptionsInterface $options
     */
    public function __construct(ObjectManager $om, ModuleOptionsInterface $options)
    {
        $entityClass = $options->getTagEntityClass();
        parent::__construct('tag');
        $this->setHydrator(new DoctrineHydrator($om))
             ->setObject(new $entityClass);

        $this->add([
            'name' => 'name',
            'attributes' => [
                'required' => 'required'
            ],
            'options' => [
                'label' => 'Nom',
            ]
        ]);
    }
}
