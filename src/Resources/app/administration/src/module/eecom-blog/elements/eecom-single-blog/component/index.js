import template from './sw-cms-el-eecom-single-blog.html.twig';
import './sw-cms-el-eecom-single-blog.scss';

const { Component, Mixin, Context } = Shopware;
const Criteria = Shopware.Data.Criteria;

Shopware.Component.register('sw-cms-el-eecom-single-blog', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element')
    ],

    created() {
        this.createdComponent();
    },

    data() {
        return {
            blog: null,
            title: 'Blog Title',
            description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus maximus velit, dictum mollis erat finibus quis. Ut dictum ornare dolor, sed mattis tellus gravida vel.',
            mediaUrl: null,
        }
    },

    computed: {
        blogImage() {
            return this.mediaUrl ? this.mediaUrl : `${Shopware.Context.api.assetsPath}/administration/static/img/cms/preview_mountain_small.jpg`;
        },

        repository() {
            return this.repositoryFactory.create('eecom_blog');
        },

        selectedBlogEntry() {
            return this.element.config.blogEntry.value;
        }
    },

    methods: {
        createdComponent() {
            this.initElementConfig('eecom-single-blog');
            this.initElementData('eecom-single-blog');

            if (this.element.config.blogEntry.value) {
                this.getEntityProperties();
            }
        },

        getEntityProperties() {

            if (this.element.config.blogEntry.value) {
                const criteria = new Criteria();
                criteria.addAssociation('media');
                this.repository
                    .get(this.element.config.blogEntry.value, Context.api, criteria)
                    .then((entity) => {
                        this.blog = entity;
                        this.title = this.blog.translated.name;
                        this.description = this.blog.translated.description;
                        if(this.blog.media)
                        {
                            this.mediaUrl = this.blog.media.url;
                        }
                        else
                        {
                            this.mediaUrl = `${Shopware.Context.api.assetsPath}/administration/static/img/cms/preview_mountain_small.jpg`;
                        }

                    });
            } else {
                this.blog = null;
                this.title = 'Blog Title';
                this.description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque faucibus maximus velit, dictum mollis erat finibus quis. Ut dictum ornare dolor, sed mattis tellus gravida vel.';
                this.mediaUrl = null;
            }
        }

    },

    watch: {
        selectedBlogEntry: function () {
            this.getEntityProperties();
        }
    },
});
