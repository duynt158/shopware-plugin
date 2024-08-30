import './component';
import './config';
import './preview';

const Criteria = Shopware.Data.Criteria;
const criteria = new Criteria();
criteria.addAssociation('media');

Shopware.Service('cmsService').registerCmsElement({
    name: 'eecom-blog-teaser-box',
    label: 'eecom-blog.blocks.blog.teaserBox.label',
    component: 'sw-cms-el-eecom-blog-teaser-box',
    configComponent: 'sw-cms-el-config-eecom-blog-teaser-box',
    previewComponent: 'sw-cms-el-preview-eecom-blog-teaser-box',
    defaultConfig: {
        blogs: {
            source: 'static',
            value: [],
            entity: {
                name: 'eecom_blog',
                criteria: criteria,
            },
        },
        title: {
            source: 'static',
            value: '',
        },
        displayMode: {
            source: 'static',
            value: 'standard',
        },
        boxLayout: {
            source: 'static',
            value: 'standard',
        },
        navigation: {
            source: 'static',
            value: true,
        },
        border: {
            source: 'static',
            value: false,
        },
        elMinWidth: {
            source: 'static',
            value: '300px',
        },
        blogStreamSorting: {
            source: 'static',
            value: 'name:ASC',
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
        blogLimit: {
            source: 'static',
            value: 10,
        },
    }

});
