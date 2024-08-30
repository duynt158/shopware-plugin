import template from './sw-cms-el-config-eecom-single-blog.html.twig';

const { Component, Mixin } = Shopware;
const { EntityCollection, Criteria } = Shopware.Data;

Component.register('sw-cms-el-config-eecom-single-blog', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    data() {
        return {
            blogEntry: null,
            selectedEntry: null
        }
    },
    computed: {
        blogEntryRepository() {
            return this.repositoryFactory.create('eecom_blog');
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('eecom-single-blog');
        }
    }
});
