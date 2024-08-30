import template from './sw-cms-el-config-eecom-blog-box.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('sw-cms-el-config-eecom-blog-box', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        blogRepository() {
            return this.repositoryFactory.create('eecom_blog');
        },

        blogSelectContext() {
            const context = Object.assign({}, Shopware.Context.api);
            context.inheritance = true;

            return context;
        },

        blogCriteria() {
            return new Criteria();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('eecom-blog-box');
        },

        onBlogChange(blogId) {
            if (!blogId) {
                this.element.config.blog.value = null;
                this.$set(this.element.data, 'blogId', null);
                this.$set(this.element.data, 'blog', null);
            } else {
                const criteria = new Criteria();
                criteria.addAssociation('media');

                this.blogRepository.get(blogId, this.blogSelectContext, criteria).then((blog) => {
                    this.element.config.blog.value = blogId;
                    this.$set(this.element.data, 'blogId', blogId);
                    this.$set(this.element.data, 'blog', blog);
                });
            }

            this.$emit('element-update', this.element);
        },
    },
});
