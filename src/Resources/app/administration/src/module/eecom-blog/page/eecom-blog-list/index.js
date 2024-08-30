import template from './eecom-blog-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('eecom-blog-list', {
    template,

    inject: [
        'repositoryFactory',
        'blogDraftApiService'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
        Mixin.getByName('eecom-blog-draft'),
        Mixin.getByName('eecom-blog-page')
    ],

    data() {
        return {
            blogs: null,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
            naturalSorting: true,
            isLoading: false,
            total: 0
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        blogRepository() {
            return this.repositoryFactory.create('eecom_blog');
        },
        draftVersionId() {
            return Shopware.State.get('eecom-blog-publisher').versionId;
        },
        columns() {
            return [{
                property: 'name',
                dataIndex: 'name',
                label: this.$t('eecom-blog.list.columnName'),
                routerLink: 'eecom.blog.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'active',
                label: this.$t('eecom-blog.list.columnActive'),
                inlineEdit: 'boolean',
                allowResize: true,
                align: 'center'
            },
            {
                property: 'publishedAt',
                label: this.$t('eecom-blog.list.publishedAt'),
                allowResize: true,
                align: 'center'
            },
            {
                property: 'categories',
                label: this.$t('eecom-blog.list.categoryName'),
                allowResize: true,
            }
            ];
        }
    },

    methods: {
        getList() {
            this.isLoading = true;

            const blogCriteria = new Criteria(this.page, this.limit);
            this.naturalSorting = this.sortBy === 'createdAt';
            blogCriteria.addAssociation('categories');
            blogCriteria.addAssociation('drafts');
            blogCriteria.addAssociation('activities');
            blogCriteria.getAssociation('drafts')
                .addFilter(Criteria.equals('draftVersion', this.draftVersionId));
            blogCriteria.setTerm(this.term);
            blogCriteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.blogRepository
                .search(blogCriteria, Shopware.Context.api)
                .then((result) => {
                    this.blogs = result;
                    this.total = result.total;
                    this.isLoading = false;
                }).catch(() => {
                this.isLoading = false;
            });
        },



        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.getList();
        }


    }
});
