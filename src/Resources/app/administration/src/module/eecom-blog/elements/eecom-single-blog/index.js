import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'eecom-single-blog',
    label: 'eecom-blog.blocks.blog.singleBlog.label',
    component: 'sw-cms-el-eecom-single-blog',
    configComponent: 'sw-cms-el-config-eecom-single-blog',
    previewComponent: 'sw-cms-el-preview-eecom-single-blog',
    defaultConfig: {
        blogEntry: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'eecom_blog',
            }
        },
        showType: {
            source: 'static',
            value: 'standard'
        },
        displayMode: {
            source: 'static',
            value: 'standard',
        },
        verticalAlign: {
            source: 'static',
            value: null,
        }
    }
});
