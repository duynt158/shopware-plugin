import template from './sw-cms-el-config-eecom-blog.html.twig';
import './sw-cms-el-config-eecom-blog.scss';

const { Component, Mixin } = Shopware;
const { EntityCollection, Criteria } = Shopware.Data;

Component.register('sw-cms-el-config-eecom-blog', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element')
    ],


    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.initElementConfig('blog');

        }
    }
});
