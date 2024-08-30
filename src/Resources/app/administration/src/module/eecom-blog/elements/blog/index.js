import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog',
    label: 'eecom-blog.blocks.blog.listing.label',
    component: 'sw-cms-el-eecom-blog',
    configComponent: 'sw-cms-el-config-eecom-blog',
    previewComponent: 'sw-cms-el-preview-eecom-blog',
    defaultConfig: {
        paginationCount: {
            source: 'static',
            value: 5
        },
        showType: {
            source: 'static',
            value: 'standard'
        }

    }
});
