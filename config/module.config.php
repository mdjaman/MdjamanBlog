<?php
/**
 * return array
 */
return array(
    'service_manager' => array(
        'invokables' => array(
            'MdjamanBlogAdmin\Filter\Category'          => \MdjamanBlogAdmin\Form\Filter\CategoryFilter::class,
            'MdjamanBlogAdmin\Filter\Tag'               => \MdjamanBlogAdmin\Form\Filter\TagFilter::class,
        ),
        'factories' => array(
            'MdjamanBlog\Options\ModuleOptions'         => \MdjamanBlog\Factory\Options\ModuleOptionsFactory::class,
            'MdjamanBlog\Options\SocialSharingOptions'  => \MdjamanBlog\Factory\Options\SocialSharingOptionsFactory::class,
            'MdjamanBlog\Options\CommentServiceOptions' => \MdjamanBlog\Factory\Options\CommentServiceOptionsFactory::class,
            
            'MdjamanBlog\Repository\Article'            => \MdjamanBlog\Factory\Repository\ArticleRepositoryFactory::class,
            'MdjamanBlog\Repository\Category'           => \MdjamanBlog\Factory\Repository\CategoryRepositoryFactory::class,
            'MdjamanBlog\Repository\Tag'                => \MdjamanBlog\Factory\Repository\TagRepositoryFactory::class,

            'MdjamanBlog\Service\Article'               => \MdjamanBlog\Factory\Service\ArticleServiceFactory::class,
            'MdjamanBlog\Service\Category'              => \MdjamanBlog\Factory\Service\CategoryServiceFactory::class,
            'MdjamanBlog\Service\Tag'                   => \MdjamanBlog\Factory\Service\TagServiceFactory::class,
            
            'MdjamanBlogAdmin\Filter\Article'           => \MdjamanBlogAdmin\Factory\Filter\ArticleFilterFactory::class,

            'MdjamanBlogAdmin\Form\Article'             => \MdjamanBlogAdmin\Factory\Form\ArticleFormFactory::class,
            'MdjamanBlogAdmin\Form\Category'            => \MdjamanBlogAdmin\Factory\Form\CategoryFormFactory::class,
            'MdjamanBlogAdmin\Form\Tag'                 => \MdjamanBlogAdmin\Factory\Form\TagFormFactory::class,
        ),
    ),
    
    'controllers' => array(
        'factories' => array(
            'MdjamanBlog\Controller\Index' => \MdjamanBlog\Factory\Controller\IndexControllerFactory::class,
            'MdjamanBlog\Controller\Article' => \MdjamanBlog\Factory\Controller\ArticleControllerFactory::class,
            'MdjamanBlog\Controller\Category' => \MdjamanBlog\Factory\Controller\CategoryControllerFactory::class,

            'MdjamanBlogAdmin\Controller\Article'  => \MdjamanBlogAdmin\Factory\Controller\ArticleControllerFactory::class,
            'MdjamanBlogAdmin\Controller\Category' => \MdjamanBlogAdmin\Factory\Controller\CategoryControllerFactory::class,
            'MdjamanBlogAdmin\Controller\Tag'      => \MdjamanBlogAdmin\Factory\Controller\TagControllerFactory::class,
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
            'ViewFeedStrategy',
        ),
    ),
    
    'view_helpers' => array(
        'factories'  => array(
            'addThis'                  => \MdjamanBlog\Factory\Helper\AddThisFactory::class,
            'disqus'                   => \MdjamanBlog\Factory\Helper\DisqusFactory::class,
            'featuredSlider'           => \MdjamanBlog\Factory\Helper\FeaturedSliderFactory::class,
            'featuredArticle'          => \MdjamanBlog\Factory\Helper\FeaturedArticleFactory::class,
            'featuredArticleCategory'  => \MdjamanBlog\Factory\Helper\FeaturedArticleCategoryFactory::class,
            'articleRecomendations'    => \MdjamanBlog\Factory\Helper\ArticleRecommendationsFactory::class,
        ),
    ),
    
    'doctrine' => array(
        'driver' => array(
            'mdjaman_blog_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/MdjamanBlog/Entity',
                ),
            ),

            'orm_default' => array(
                'drivers' => array(
                    'MdjamanBlog\Entity' => 'mdjaman_blog_driver',
                ),
            ),
        ),
    ),
    
    'jms_serializer' => array(
        'naming_strategy' => 'identical'
    ),
    
    'router' => [
        'routes' => [
            'blog' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route' => '/news',
                    'defaults' => [
                        '__NAMESPACE__' => 'MdjamanBlog\Controller',
                        'controller' => 'Category',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/[:alias]',
                            'defaults' => [
                                'controller' => 'Article',
                                'action' => 'view',
                            ],
                            'constraints' => [
                                'alias'  => '[a-zA-Z0-9-_.]+',
                            ],
                        ],
                    ],
                    'category' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/category/[:alias]',
                            'defaults' => array(
                                'action' => 'view',
                            ),
                            'constraints' => array(
                                'alias' => '[a-zA-Z0-9-_.]+',
                            ),
                        ),
                    ),
                    'tag' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/tag/[:alias]',
                            'defaults' => array(
                                'controller' => 'Article',
                                'action' => 'tag',
                            ),
                            'constraints' => array(
                                'alias' => '[a-zA-Z0-9-_.]+',
                            ),
                        ),
                    ),
                    'feed' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/feed[/:type]',
                            'defaults' => array(
                                'controller' => 'Article',
                                'action' => 'feed',
                            ),
                            'constraints' => array(
                                'type' => '(rss|atom)',
                            ),
                        ),
                    ),
                ],
            ],
            
            'zfcadmin' => [
                'child_routes' => [
                    'blog' => [
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => [
                            'route' => '/blog',
                            'defaults' => [
                                '__NAMESPACE__' => 'MdjamanBlogAdmin\Controller',
                                'controller' => 'Article',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'article' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/article',
                                    'defaults' => [
                                        'action' => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'view' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/[:id]',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                            'constraints' => [
                                                'id' => '[a-zA-Z-0-9-]*'
                                            ],
                                        ],
                                    ],
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/_new',
                                            'defaults' => array(
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[:id]/_edit',
                                            'defaults' => array(
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[:id]/_delete',
                                            'defaults' => array(
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ],
                            ],
                            'category' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/category',
                                    'defaults' => [
                                        'controller' => 'Category',
                                        'action' => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'view' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/[:id]',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                            'constraints' => [
                                                'id' => '[a-zA-Z-0-9-]*'
                                            ],
                                        ],
                                    ],
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/_new',
                                            'defaults' => array(
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[:id]/_edit',
                                            'defaults' => array(
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[:id]/_delete',
                                            'defaults' => array(
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ],
                            ],
                            'tag' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/tag',
                                    'defaults' => [
                                        'controller' => 'Tag',
                                        'action' => 'index',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'view' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/[:id]',
                                            'defaults' => [
                                                'action' => 'view',
                                            ],
                                            'constraints' => [
                                                'id' => '[a-zA-Z-0-9-]*'
                                            ],
                                        ],
                                    ],
                                    'add' => array(
                                        'type' => 'Literal',
                                        'options' => array(
                                            'route' => '/_new',
                                            'defaults' => array(
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[:id]/_edit',
                                            'defaults' => array(
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => '/[:id]/_delete',
                                            'defaults' => array(
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    
    'navigation' => array(
        'admin' => array(
            'blog' => array(
                'label' => 'Blog',
                'route' => 'zfcadmin/blog/article',
                'icon'  => 'fa-newspaper-o',
                'order' => 1,
                'pages' => array(
                    'article' => array(
                        'label' => 'Articles',
                        'route' => 'zfcadmin/blog/article',
                        'icon'  => 'fa-file-text',
                    ),
                    'category' => array(
                        'label' => 'Rubriques',
                        'route' => 'zfcadmin/blog/category',
                        'icon'  => 'fa-folder',
                    ),
                    'tag' => array(
                        'label' => 'Mots-clés',
                        'route' => 'zfcadmin/blog/tag',
                        'icon'  => 'fa-tag',
                    ),
                ),
            ),
        ),
    ),
);