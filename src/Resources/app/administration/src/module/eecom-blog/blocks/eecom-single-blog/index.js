import './component';
import './preview';

Shopware.Service('cmsService').registerCmsBlock({
    name: 'eecom-single-blog',
    label: 'eecom-blog.blocks.blog.singleBlog.label',
    category: 'eecom-blog',
    component: 'sw-cms-block-eecom-single-blog',
    previewComponent: 'sw-cms-preview-eecom-single-blog',
    defaultConfig: {
        marginBottom: '0px',
        marginTop: '0px',
        marginLeft: '0px',
        marginRight: '0px',
        sizingMode: 'boxed'
    },
    slots: {
        'singleBlog': {
            type: 'eecom-single-blog',
            default: {
                config: {
                    blogEntry: { source: 'static', value: null }
                }
            }
        }
    }
});
