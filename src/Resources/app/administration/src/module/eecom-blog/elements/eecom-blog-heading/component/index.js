import './sw-cms-el-eecom-blog-heading.scss';

const { Component, Mixin } = Shopware;

Component.extend('sw-cms-el-eecom-blog-heading', 'sw-cms-el-text', {
    mixins: [
        Mixin.getByName('cms-element'),
    ],

    computed: {
        isEEComBlogPage() {
            return this.cmsPageState?.currentPage?.type ?? '' === 'blog_page';
        },
    },

    methods: {
        createdComponent() {

            this.initElementConfig('eecom-blog-heading');
            if (this.isEEComBlogPage && !this.element?.translated?.config?.content) {
                this.element.config.content.source = 'mapped';
                this.element.config.content.value = 'eecom_blog.name';
            }
        },

        updateDemoValue() {
            if (this.element.config.content.source === 'mapped') {
                let label = '';
                let className = 'sw-cms-el-eecom-blog-heading__skeleton';

                if (this.element.config.content.value === 'eecom_blog.name') {
                    className = 'sw-cms-el-eecom-blog-heading__placeholder';
                    label = this.$tc('eecom-blog.elements.eecomBlogHeading.label');
                }

                this.demoValue = `<h1 class="${className}">${label}</h1>`;

                if (this.cmsPageState.currentDemoEntity) {
                    this.demoValue = this.getDemoValue(this.element.config.content.value);
                }
            }
        },
    },
});
