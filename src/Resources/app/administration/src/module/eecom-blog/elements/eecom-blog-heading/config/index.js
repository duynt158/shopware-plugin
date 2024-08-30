const { Component } = Shopware;

Component.extend('sw-cms-el-config-eecom-blog-heading', 'sw-cms-el-config-text', {

    computed: {
        isEEComBlogPage() {
            return this.cmsPageState?.currentPage?.type ?? '' === 'blog_page';
        },
    },

    methods: {
        createdComponent() {
            this.initElementConfig('eecom-blog-heading');

            if (!this.isEEComBlogPage || this.element?.translated?.config?.content) {
                return;
            }

            if (this.element.config.content.source && this.element.config.content.value) {
                return;
            }

            this.element.config.content.source = 'mapped';
            this.element.config.content.value = 'eecom_blog.name';
        },
    },
});
