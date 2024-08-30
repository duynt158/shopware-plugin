import template from './sw-cms-el-eecom-blog.html.twig';
import './sw-cms-el-eecom-blog.scss';

const { Component, Mixin } = Shopware;

Shopware.Component.register('sw-cms-el-eecom-blog', {
    template,

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog');
        },
    }
});
