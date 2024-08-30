import template from './sw-cms-el-eecom-blog-box.html.twig';


const { Component, Mixin, Filter } = Shopware;

Component.register('sw-cms-el-eecom-blog-box', {
    template,

    inject: ['feature'],

    mixins: [
        Mixin.getByName('cms-element'),
        Mixin.getByName('placeholder'),
    ],

    computed: {
        blog() {
            if (!this.element.data || !this.element.data.blog) {
                return {
                    name: 'Lorem ipsum dolor',
                    description: `Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                    sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
                    sed diam voluptua.`.trim(),
                    media: {
                            url: '/administration/static/img/cms/preview_mountain_small.jpg',
                            alt: 'Lorem Ipsum dolor',
                    },
                };
            }

            return this.element.data.blog;
        },

        displaySkeleton() {
            return !this.element.data || !this.element.data.blog;
        },

        mediaUrl() {
            if (this.blog.media) {
                if (this.blog.media.id) {
                    return this.blog.media.url;
                }

                return this.assetFilter(this.blog.media.url);
            }

            return this.assetFilter('administration/static/img/cms/preview_glasses_large.jpg');
        },

        altTag() {
            if (this.blog.media && this.blog.media.alt) {
                return this.blog.media.alt;
            }

            return null;
        },

        displayModeClass() {
            if (this.element.config.displayMode.value === 'standard') {
                return null;
            }

            return `is--${this.element.config.displayMode.value}`;
        },

        verticalAlignStyle() {
            if (!this.element.config.verticalAlign || !this.element.config.verticalAlign.value) {
                return null;
            }

            return `align-content: ${this.element.config.verticalAlign.value};`;
        },

        assetFilter() {
            return Filter.getByName('asset');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('eecom-blog-box');
            this.initElementData('eecom-blog-box');
        },
    },
});
