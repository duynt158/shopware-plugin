import './component';
import './config';

Shopware.Service('cmsService').registerCmsElement({
    name: 'eecom-blog-heading',
    label: 'eecom-blog.blocks.blog.eecomBlogHeading.label',
    component: 'sw-cms-el-eecom-blog-heading',
    configComponent: 'sw-cms-el-config-eecom-blog-heading',
    defaultConfig: {
        content: {
            source: 'static',
            value: '<h2>Lorem ipsum dolor sit amet.</h2>'.trim(),
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
    },
});
