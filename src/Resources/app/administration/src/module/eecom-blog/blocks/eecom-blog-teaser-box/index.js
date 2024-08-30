import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'eecom-blog-teaser-box',
    label: 'eecom-blog.blocks.blog.teaserBox.label',
    category: 'eecom-blog',
    component: 'sw-cms-block-eecom-blog-teaser-box',
    previewComponent: 'sw-cms-preview-eecom-blog-teaser-box',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        teaserBox: 'eecom-blog-teaser-box',
    },
});
