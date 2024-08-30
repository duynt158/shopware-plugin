import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'eecom-blog-listing',
    label: 'eecom-blog.blocks.blog.listing.label',
    category: 'eecom-blog',
    component: 'sw-cms-block-eecom-blog-listing',
    previewComponent: 'sw-cms-preview-eecom-blog-listing',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed'
    },
    slots: {
        listing: 'blog'
    }
});
