import './page/eecom-blog-category-list';
import './component/eecom-blog-category-clone-modal';
import './view/eecom-blog-category-detail-category-data';
import './view/eecom-blog-category-detail-category-seo';
import './page/eecom-blog-category-detail';
import './component/eecom-blog-category-seo-form';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('eecom-blog-category', {
    type: 'plugin',
    name: 'blog Category',
    title: 'eecom-blog-category.general.mainMenuItemGeneral',
    description: 'eecom-blog-category.general.descriptionTextModule',
    color: '#70fffc',
    icon: 'default-object-shield',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'eecom-blog-category-list',
            path: 'index'
        },
        create: {
            component: 'eecom-blog-category-detail',
            path: 'create',
            meta: {
                parentPath: 'eecom.blog.category.index'
            },
            redirect: {
                name: 'eecom.blog.category.create.categoryData'
            },
            children: {
                categoryData: {
                    component: 'eecom-blog-category-detail-category-data',
                    path: 'category-data',
                    meta: {
                        parentPath: 'eecom.blog.category.index'
                    }
                }
            }
        },
        detail: {
            component: 'eecom-blog-category-detail',
            path: 'detail/:id?',
            props: {
                default: (route) => ({ categoryId: route.params.id })
            },
            meta: {
                parentPath: 'eecom.blog.category.index'
            },
            redirect: {
                name: 'eecom.blog.category.detail.categoryData'
            },
            children: {
                categoryData: {
                    component: 'eecom-blog-category-detail-category-data',
                    path: 'category-data',
                    meta: {
                        parentPath: 'eecom.blog.category.index'
                    }
                },
                categorySeo: {
                    component: 'eecom-blog-category-detail-category-seo',
                    path: 'category-seo',
                    meta: {
                        parentPath: 'eecom.blog.category.index'
                    }
                }
            }
        }



    },

    navigation: [
        {
            id: 'eecom-blog-category',
            path: 'eecom.blog.category.index',
            label: 'eecom-blog.general.mainMenuCategoryItemGeneral',
            color: '#70fffc',
            icon: 'default-object-shield',
            position: 10,
            parent: 'eecom-blog-shop'
        }
        ]
});
