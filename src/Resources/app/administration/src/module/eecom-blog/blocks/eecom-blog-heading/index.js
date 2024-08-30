import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'eecom-blog-heading',
    label: 'eecom-blog.blocks.blog.eecomBlogHeading.label',
    category: 'eecom-blog',
    component: 'sw-cms-block-eecom-blog-heading',
    previewComponent: 'sw-cms-preview-eecom-blog-heading',
    defaultConfig: {
        marginTop: '20px',
        marginLeft: '20px',
        marginBottom: '0',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        left: 'eecom-blog-heading'
    },
});
