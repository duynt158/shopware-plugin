import template from './eecom-blog-category-detail.html.twig';
import errorConfig from './error-cfg.json';
import eecomBlogCategoryDetail from './state';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { hasOwnProperty } = Shopware.Utils.object;
const { mapPageErrors, mapGetters, mapState } = Shopware.Component.getComponentHelper();


Component.register('eecom-blog-category-detail', {
    template,

    inject: [
        'repositoryFactory',
        'seoUrlService'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    props: {
        categoryId: {
            type: String,
            required: false,
            default: null
        }
    },

    data() {
        return {
            processSuccess: false,
            cloning: false
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.CategoryTitle)
        };
    },

    computed: {
        ...mapState('eecomBlogCategoryDetail', [
            'loading',
            'category'
        ]),

        ...mapGetters('eecomBlogCategoryDetail', [
           'blogCategoriesRepository',
            'isLoading'
        ]),

        ...mapPageErrors(errorConfig),


        blogCategoriesRepository() {
            return this.repositoryFactory.create('eecom_blog_category');
        },

        blogCategoryCriteria() {
            const criteria = new Criteria();
            criteria.getAssociation('seoUrls')
                .addFilter(Criteria.equals('isCanonical', true));

            return criteria;
        },

        CategoryTitle() {
            return this.placeholder(this.category, 'name', this.categoryId ? '' : this.$tc('eecom-blog-category.detail.textHeadline'));
        }
    },

    beforeCreate() {
        Shopware.State.registerModule('eecomBlogCategoryDetail', eecomBlogCategoryDetail);
    },

    created() {
        this.createdComponent();
    },

    beforeDestroy() {
        Shopware.State.unregisterModule('eecomBlogCategoryDetail');
    },

    destroyed() {
        this.destroyedComponent();
    },

    watch: {
        categoryId() {
            this.destroyedComponent();
            this.createdComponent();
        }
    },

    methods: {
        createdComponent() {
            // when create
            if (!this.categoryId) {
                // set language to system language
                if (!Shopware.State.getters['context/isSystemDefaultLanguage']) {
                    Shopware.State.commit('context/resetLanguageToDefault');
                }
            }

            Shopware.State.commit('eecomBlogCategoryDetail/setBlogCategory', this.blogCategoriesRepository.create(Shopware.Context.api));
            Shopware.State.commit('eecomBlogCategoryDetail/setApiContext', Shopware.Context.api);
            this.$root.$on('sidebar-toggle-open', this.openMediaSidebar);

            if(this.categoryId) {
                return this.getBlogCategory();
            }



        },

        destroyedComponent() {
            this.$root.$off('sidebar-toggle-open');
        },

         getBlogCategory() {
            Shopware.State.commit('eecomBlogCategoryDetail/setLoading', true);

            this.blogCategoriesRepository
                .get(this.$route.params.id, Shopware.Context.api, this.blogCategoryCriteria)
                .then((entity) => {

                    Shopware.State.commit('eecomBlogCategoryDetail/setBlogCategory', entity);
                    Shopware.State.commit('eecomBlogCategoryDetail/setLoading', false);
                });
        },

        openMediaSidebar() {
            // Check if we have a reference to the component before calling a method
            if (!hasOwnProperty(this.$refs, 'mediaSidebarItem')
                || !this.$refs.mediaSidebarItem) {
                return;
            }
            this.$refs.mediaSidebarItem.openContent();
        },

        onClickSave() {
            Shopware.State.commit('eecomBlogCategoryDetail/setLoading', true);

            this.updateSeoUrls().then(() => {
                return this.blogCategoriesRepository.save(this.category, { ...Shopware.Context.api });
            }).then(() => {
                if(this.categoryId) {
                   return  this.getBlogCategory();
                }
                else {
                    this.$router.push({ name: 'eecom.blog.category.detail', params: { id: this.category.id } });
                }
            }).catch(() => {
                Shopware.State.commit('eecomBlogCategoryDetail/setLoading', false);

                this.createNotificationError({
                    title: this.$t('eecom-blog-category.detail.errorTitle'),
                    message: this.$t('eecom-blog-category.errorMessage')
                });
            });



        },

        saveFinish() {
            this.processSuccess = false;
        },


        onChangeLanguage(languageId) {
            Shopware.State.commit('context/setApiLanguageId', languageId);
            this.createdComponent();
        },

        saveOnLanguageChange() {
            return this.onClickSave();
        },

        abortOnLanguageChange() {
            return Shopware.State.getters['eecomBlogCategoryDetail/hasChanges'];
        },
        updateSeoUrls() {
            if (!Shopware.State.list().includes('swSeoUrl')) {
                return Promise.resolve();
            }

            const seoUrls = Shopware.State.getters['swSeoUrl/getNewOrModifiedUrls']();

            return Promise.all(seoUrls.map((seoUrl) => {
                if (seoUrl.seoPathInfo) {
                    seoUrl.isModified = true;
                    return this.seoUrlService.updateCanonicalUrl(seoUrl, seoUrl.languageId);
                }

                return Promise.resolve();
            }));
        },
        async onDuplicateSave() {
            this.cloning = true;
        },

        onDuplicateFinish(duplicate) {
            this.cloning = false;
            this.$router.push({ name: 'eecom.blog.category.detail', params: { id: duplicate.id } });
        }

    }
});
