import './component/eecom-blog-clone-modal';
import './component/eecom-blog-category-form';
import './component/eecom-blog-visibility-detail';
import './component/eecom-blog-visibility-select';
import './component/eecom-blog-layout-assignment';
import './component/eecom-blog-seo-form';
import './component/eecom-blog-box-preview';
import './view/eecom-blog-detail-blog-data';
import './view/eecom-blog-detail-blog-layout';
import './view/eecom-blog-detail-blog-seo';
import './page/eecom-blog-list';
import './page/eecom-blog-detail';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';


/**
 * CMS Blocks
 */
import './blocks/eecom-blog-listing';
import './blocks/eecom-blog-teaser-box';
import './blocks/eecom-single-blog';
import './blocks/eecom-blog-category-navigation';
import './blocks/eecom-blog-heading';
/**
 * CMS Elements
 */

import './elements/blog';
import './elements/eecom-blog-teaser-box';
import './elements/blog-box';
import './elements/eecom-single-blog';
import './elements/eecom-blog-category-navigation';
import './elements/eecom-blog-heading';

const { Module } = Shopware;

Module.register('eecom-blog', {
    type: 'plugin',
    name: 'blog',
    title: 'eecom-blog.general.mainMenuItemGeneral',
    description: 'eecom-blog.general.descriptionTextModule',
    color: '#70fffc',
    icon: 'default-object-shield',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'eecom-blog-list',
            path: 'index'
        },
        create: {
            component: 'eecom-blog-detail',
            path: 'create',
            meta: {
                parentPath: 'eecom.blog.index'
            },
            redirect: {
                name: 'eecom.blog.create.blogData'
            },
            children: {
                blogData: {
                    component: 'eecom-blog-detail-blog-data',
                    path: 'blog-data',
                    meta: {
                        parentPath: 'eecom.blog.index'
                    }
                }
            }
        },
        detail: {
            component: 'eecom-blog-detail',
            path: 'detail/:id?',
            props: {
                default: (route) => ({ blogId: route.params.id })
            },
            meta: {
                parentPath: 'eecom.blog.index'
            },
           redirect: {
                name: 'eecom.blog.detail.blogData'
            },
            children: {
                blogData: {
                    component: 'eecom-blog-detail-blog-data',
                    path: 'blog-data/:draftVersionId?',
                    meta: {
                        parentPath: 'eecom.blog.index'
                    }
                },
               blogLayout: {
                    component: 'eecom-blog-detail-blog-layout',
                    path: 'blog-layout',
                    meta: {
                        parentPath: 'eecom.blog.index'
                    }
                },
                blogSeo: {
                    component: 'eecom-blog-detail-blog-seo',
                    path: 'blog-seo',
                    meta: {
                        parentPath: 'eecom.blog.index'
                    }
                }
            }
        }

    },

    navigation: [{
        id: 'eecom-blog-shop',
        label: 'eecom-blog.general.mainMenuItemNavigation',
        color: '#70fffc',
        icon: 'default-object-shield-full',
        position: 71,
        parent: 'sw-catalogue'
    }, {
        id: 'eecom-blog',
        path: 'eecom.blog.index',
        label: 'eecom-blog.general.mainMenuItemGeneral',
        color: '#70fffc',
        icon: 'default-object-shield',
        position: 10,
        parent: 'eecom-blog-shop'
    }
    ]
});
