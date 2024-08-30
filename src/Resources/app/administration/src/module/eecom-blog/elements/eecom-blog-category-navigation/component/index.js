import template from './sw-cms-el-eecom-blog-category-navigation.html.twig';
import './sw-cms-el-eecom-blog-category-navigation.scss';

Shopware.Component.register('sw-cms-el-eecom-blog-category-navigation', {
    template,

    mixins: [
        Shopware.Mixin.getByName('cms-element'),
        Shopware.Mixin.getByName('placeholder'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('eecom-blog-category-navigation');
        },
    },
});
