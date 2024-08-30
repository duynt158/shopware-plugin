import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'eecom-blog-category-navigation',
    label: 'eecom-blog.blocks.blog.eecomBlogCategoryNavigation.label',
    component: 'sw-cms-el-eecom-blog-category-navigation',
    configComponent: 'sw-cms-el-config-eecom-blog-category-navigation',
    previewComponent: 'sw-cms-el-preview-eecom-blog-category-navigation',
    defaultConfig: {
        elMinHeight: {
            source: 'static',
            value: '90px',
        },
        textColor:{
            source: 'static',
            value: '#FFFFFF',
        },
        bgPrimaryColor:{
            source: 'static',
            value: '#575759',
        },
        bgColorGradiant:{
            source: 'static',
            value: '#3E3E40',
        },
        hoverPrimaryColor:{
            source: 'static',
            value: '#861623',
        },
        hoverColorGradiant:{
            source: 'static',
            value: '#BC102A',
        },
        media: {
            source: 'static',
            value: null,
            required: true,
        },
        displayMode: {
            source: 'static',
            value: 'standard',
        },
        elMinLogoHeight: {
            source: 'static',
            value: '90px',
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
        logoUrl: {
            source: 'static',
            value: null,
        }
    }
});

