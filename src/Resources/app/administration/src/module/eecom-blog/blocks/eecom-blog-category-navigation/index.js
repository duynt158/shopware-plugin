import './component';
import './preview';

const { Application } = Shopware;

Application.getContainer('service').cmsService.registerCmsBlock({
    name: 'eecom-blog-category-navigation',
    label: 'eecom-blog.blocks.blog.eecomBlogCategoryNavigation.label',
    category: 'eecom-blog',
    component: 'sw-cms-block-eecom-blog-category-navigation',
    previewComponent: 'sw-cms-block-preview-eecom-blog-category-navigation',
    defaultConfig: {
        marginBottom: '20px',
        marginTop: '20px',
        marginLeft: '20px',
        marginRight: '20px',
        sizingMode: 'boxed',
    },
    slots: {
        blogCategoryContent: 'eecom-blog-category-navigation',
    },
});
