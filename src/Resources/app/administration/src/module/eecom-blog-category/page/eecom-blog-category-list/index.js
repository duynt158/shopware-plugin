import template from './eecom-blog-category-list.html.twig';

const {Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;
const utils = Shopware.Utils;

Component.register('eecom-blog-category-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('listing'),
        Mixin.getByName('position')
    ],

    data() {
        return {
            blogCategories: null,
            sortBy: 'position',
            sortDirection: 'ASC',
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
        blogCategoriesRepository() {
            return this.repositoryFactory.create('eecom_blog_category');
        },

        columns() {
            return [
                {
                    property: 'name',
                    dataIndex: 'name',
                    label: this.$t('eecom-blog-category.list.columnName'),
                    routerLink: 'eecom.blog.category.detail',
                    inlineEdit: 'string',
                    allowResize: true,
                    primary: true
                }, {
                    property: 'active',
                    label: this.$t('eecom-blog-category.list.columnActive'),
                    inlineEdit: 'boolean',
                    allowResize: true,
                    align: 'center'
                },
                {
                    property: 'position',
                    label: this.$t('eecom-blog-category.list.columnOrder'),
                }
            ];
        }
    },

    methods: {
        getList() {
            this.isLoading = true;

            const blogCategoryCriteria = new Criteria(this.page, this.limit);
            this.naturalSorting = this.sortBy === 'createdAt';

            blogCategoryCriteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            this.blogCategoriesRepository
                .search(blogCategoryCriteria, Shopware.Context.api)
                .then((result) => {

                    this.blogCategories = result;
                    this.total = result.total;
                    this.isLoading = false;
                }).catch(() => {
                this.isLoading = false;
            });
        },

        onPositionChanged: utils.debounce(function syncClogCategories(blogCategories) {
            this.blogCategories = blogCategories;

            this.blogCategoriesRepository.sync(blogCategories)
                .then(this.getList)
                .catch(() => {
                    this.getList();
                    this.createNotificationError({
                        message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                    });
                });
        }, 800),

        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.getList();
        },
    }
});
