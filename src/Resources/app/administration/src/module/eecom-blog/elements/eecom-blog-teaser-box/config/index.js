import template from './sw-cms-el-config-eecom-blog-teaser-box.html.twig';
import './sw-cms-el-config-eecom-blog-teaser-box.scss';

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register('sw-cms-el-config-eecom-blog-teaser-box', {
    template,

    inject: ['repositoryFactory', 'feature'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            blogCollection: null,

            // Temporary values to store the previous selection in case the user changes the assignment type.
            tempBlogIds: []

        };
    },

    computed: {
        blogRepository() {
            return this.repositoryFactory.create('eecom_blog');
        },



        blogs() {
            if (this.element?.data?.blogs && this.element.data.blogs.length > 0) {
                return this.element.data.blogs;
            }

            return null;
        },

        blogMediaFilter() {
            const criteria = new Criteria(1, 25);
            criteria.addAssociation('media');


            return criteria;
        },

        blogMultiSelectContext() {
            return Object.assign({}, Shopware.Context.api);
        },

        eecomBlogAssignmentTypes() {
            return [{
                label: this.$tc('eecom-blog.elements.teaserBox.config.blogAssignmentTypeOptions.manual'),
                value: 'static',
            }, {
                label: this.$tc('eecom-blog.elements.teaserBox.config.blogAssignmentTypeOptions.auto'),
                value: 'mapped',
            }];
        }

    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('eecom-blog-teaser-box');

            this.blogCollection = new EntityCollection('/eecom-blog', 'eecom_blog', Shopware.Context.api);


            if (this.element.config.blogs.value.length <= 0) {
                return;
            }


            if (this.element.config.blogs.source === 'mapped') {


            } else {
                // We have to fetch the assigned entities again
                // ToDo: Fix with NEXT-4830
                const criteria = new Criteria(1, 100);
                criteria.addAssociation('media');

                criteria.setIds(this.element.config.blogs.value);

                this.blogRepository
                    .search(criteria, Object.assign({}, Shopware.Context.api, { inheritance: true }))
                    .then(result => {
                        this.blogCollection = result;
                    });
            }
        },
        onChangeAssignmentType(type) {

            if (type === 'mapped') {

                this.tempBlogIds = this.element.config.blogs.value;
                this.element.config.blogs.value = [];



            } else {
                this.element.config.blogs.value = this.tempBlogIds;
            }
        },
         onBlogsChange() {
            this.element.config.blogs.value = this.blogCollection.getIds();

            if (!this.element?.data) {
                return;
            }

            this.$set(this.element.data, 'blogs', this.blogCollection);
        },

        isSelected(itemId) {
            return this.blogCollection.has(itemId);
        },
    },
});
