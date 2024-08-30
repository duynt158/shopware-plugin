import template from './eecom-blog-detail.html.twig';
import BlogDraftApiService from '../../../../service/blog.draft.api.service';
import errorConfig from './error-cfg.json';
import eecomBlogDetail from './state';

const { Component, Mixin } = Shopware;
const { Criteria,ChangesetGenerator } = Shopware.Data;
const { hasOwnProperty,cloneDeep } = Shopware.Utils.object;
const { mapPageErrors, mapGetters, mapState } = Shopware.Component.getComponentHelper();
const type = Shopware.Utils.types;

Component.register('eecom-blog-detail', {
    template,

    inject: {
        blogDraftApiService: BlogDraftApiService.name,
        repositoryFactory: 'repositoryFactory',
        mediaService:'mediaService',
        seoUrlService:'seoUrlService'
    },


    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder'),
        Mixin.getByName('eecom-blog-draft')
    ],

    props: {
        blogId: {
            type: String,
            required: false,
            default: null
        }
    },

    data() {
        return {
            processSuccess: false,
            mediaLogoSquare: null,
            mediaLogoLandscape: null,
            cloning: false,
            showModalDraftName: false,
            draftName: '',
            pageOrigin: null,
            showModalParentChanges: false,
            initialLoadDone: false,
            showModalPublish: false,
            showModalUnsaved: false


        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.blogTitle)
        };
    },

    computed: {
        ...mapState('eecomBlogDetail', [
            'loading',
            'blog'
        ]),

        ...mapGetters('eecomBlogDetail', [
            'blogRepository',
            'isLoading'
        ]),

        ...mapPageErrors(errorConfig),
        ...mapState('cmsPageState', [
            'currentPage',
        ]),

        blogRepository() {

            const repository  = this.repositoryFactory.create('eecom_blog');

            repository._get = repository.get;
            repository._save = repository.save;

            repository.save = (blog) => repository._save(blog, this.getVersionContext());
            repository.get = (id, context, criteria) => repository._get(id, this.getVersionContext(), criteria);

            return repository;
        },
        draftVersionId() {
            return Shopware.State.get('eecom-blog-publisher').versionId;
        },
        draft() {
            return Shopware.State.get('eecom-blog-publisher').draft;
        },

        blogCriteria() {
            const criteria = new Criteria();
            const activitySortCriteria = Criteria.sort('createdAt', 'DESC');

            criteria.getAssociation('media');

            criteria.getAssociation('tags');

            criteria.getAssociation('seoUrls')
                .addFilter(Criteria.equals('isCanonical', true));


            criteria.getAssociation('user');
            criteria.getAssociation('visibilities.salesChannel');


            criteria
                .addAssociation('categories')
                .addAssociation('cmsPage')

            criteria.addAssociation('activities');

            criteria.getAssociation('activities')
                .addSorting(activitySortCriteria);

            if (this.isEntityDraft) {

                criteria.addAssociation('drafts');

                criteria.getAssociation('drafts')
                    .addFilter(Criteria.equals('draftVersion', this.draftVersionId));
            }

            return criteria;
        },

        blogTitle() {
            return this.placeholder(this.blog, 'name', this.blogId ? '' : this.$tc('eecom-blog.detail.textHeadline'));
        }
    },

    beforeCreate() {
        Shopware.State.registerModule('eecomBlogDetail', eecomBlogDetail);
    },

    created() {
        this.createdComponent();
    },

    beforeDestroy() {
        Shopware.State.unregisterModule('eecomBlogDetail');
    },

    destroyed() {
        this.destroyedComponent();
    },

    watch: {
        blogId() {
            this.destroyedComponent();
            this.createdComponent();
        },
        blog: {
            deep: true,
            handler() {
                if (!this.initialLoadDone) {
                    this.initialLoadDone = true;
                    this.checkForParentUpdate();
                }
            }
        }
    },

    methods: {
        resolvePublisherData() {

            Shopware.State.commit('eecom-blog-publisher/setDraft', this.blog.drafts[0]);
            Shopware.State.commit('eecom-blog-publisher/setActivity', this.blog.activities);
            Shopware.State.dispatch('eecom-blog-publisher/enrichActivity', this.repositoryFactory.create('user'));
        },
        createdComponent() {

            Shopware.State.dispatch('cmsPageState/resetCmsPageState');
            // when create
            if (!this.blogId) {
                // set language to system language
                if (!Shopware.State.getters['context/isSystemDefaultLanguage']) {
                    Shopware.State.commit('context/resetLanguageToDefault');
                }
            }

            Shopware.State.commit('eecomBlogDetail/setBlog', this.blogRepository.create(Shopware.Context.api));
            Shopware.State.commit('eecomBlogDetail/setApiContext', Shopware.Context.api);
            this.$root.$on('sidebar-toggle-open', this.openMediaSidebar);

            if(this.blogId) {
                return this.getBlog();
            }


        },

        destroyedComponent() {
            this.$root.$off('sidebar-toggle-open');

        },

        getBlog() {
            Shopware.State.commit('eecomBlogDetail/setLoading', true);

            this.blogRepository
                .get(this.$route.params.id, Shopware.Context.api, this.blogCriteria)
                .then((entity) => {
                    Shopware.State.commit('eecomBlogDetail/setBlog', entity);
                    Shopware.State.commit('eecomBlogDetail/setLoading', false);
                    this.resolvePublisherData();
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

            const pageOverrides = this.getCmsPageOverrides();
            if (type.isPlainObject(pageOverrides)) {
                this.blog.slotConfig = cloneDeep(pageOverrides);
            }


            Shopware.State.commit('eecomBlogDetail/setLoading', true);

            this.updateSeoUrls().then(() => {

                return this.blogRepository.save(this.blog, { ...Shopware.Context.api });
            }).then(() => {

                if(this.blogId) {
                    return  this.getBlog();
                }
                else {
                    this.$router.push({ name: 'eecom.blog.detail', params: { id: this.blog.id } });
                }
                Shopware.State.commit('eecomBlogDetail/setLoading', false);
                this.processSuccess = true;
            }).catch(() => {
                Shopware.State.commit('eecomBlogDetail/setLoading', false);

                this.createNotificationError({
                    title: this.$t('eecom-blog.detail.errorTitle'),
                    message: this.$t('eecom-blog.detail.errorMessage')
                });
            });


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
            return Shopware.State.getters['eecomBlogDetail/hasChanges'];
        },
        async onDuplicateSave() {
            this.cloning = true;
        },

        onDuplicateFinish(duplicate) {
            this.cloning = false;
            this.$router.push({ name: 'eecom.blog.detail', params: { id: duplicate.id } });
        },
        onSaveDraft()
        {
            this.draftName = this.createDraftName();
            this.showModalDraftName = true;

        },
        createDraftName() {
            return `${this.blog.name}-${this.getCurrentDate()}`
        },
        getCurrentDate() {
            const date = new Date();
            let month = '' + (date.getMonth() + 1),
                day = '' + date.getDate(),
                year = date.getFullYear();

            if (month.length < 2) {
                month = '0' + month;
            }

            if (day.length < 2) {
                day = '0' + day;
            }

            return [year, month, day].join('-');
        },
        setDraftName(draftName) {
            this.draftName = draftName;
        },
        onModalDraftNameConfirm() {
            this.showModalDraftName = false;
            this.$nextTick(this.saveDraft);
        },
        onSavePublish() {

            if (!this.blog.categories.length) {
                this.saveAndPublish();
            } else {
                this.showModalPublish = true;
            }
        },
        async saveAndPublish() {
            try {
                await this.onClickSave();
                Shopware.State.commit('eecomBlogDetail/setLoading', true);

                await this.blogDraftApiService.merge(this.blog);
                this.getBlog(this.blog.id);
            } catch {
                this.onError('publisher.error.action.saveAndPublish');
            } finally {
                Shopware.State.commit('eecomBlogDetail/setLoading', false);
            }
        },
        async saveDraft() {

            let draftVersionId;

            try {
                Shopware.State.commit('eecomBlogDetail/setLoading', true);

                draftVersionId = await this.blogDraftApiService.saveAsDraft(this.blog, this.draftName);

                await this.openDetailPage(this.blog.id, draftVersionId);

                if (this.entityHasChanged(this.blog, this.pageOrigin)) {
                    await this.onClickSave();
                } else {
                    await this.loadPage(this.blog.id);
                }
            } catch {
                if (typeof draftVersionId !== 'undefined') {
                    // nth
                } else {
                    this.onError('eecom-blog.error.action.draft');
                }
            } finally {
                this.draftName = '';
                Shopware.State.commit('eecomBlogDetail/setLoading', false);
            }
        },
        async loadPage(blogId) {
            try {
                Shopware.State.commit('eecomBlogDetail/setLoading', true);

                const blog = await this.blogRepository.get(blogId, Shopware.Context.api, this.blogCriteria);

                Shopware.State.commit('eecomBlogDetail/setBlog', blog);

                this.resolvePublisherData();
            } catch(exception) {
                this.createNotificationError({
                    title: exception.message,
                    message: exception.response.statusText
                });
            } finally {
                Shopware.State.commit('eecomBlogDetail/setLoading', false);
            }
        },
        onError(errorKey) {
            this.createNotificationError({
                title: this.$tc('eecom-blog.error.title'),
                message: this.$tc(errorKey)
            });
        },
        async checkForParentUpdate() {
            this.showModalParentChanges = await this.entityParentUpdated(this.blog);
        },
        closeDetailPage() {
            this.$router.push({
                name: 'eecom.blog.index'
            });
        },
        onDiscard() {
            this.showModalUnsaved = false;
            this.showModalPublish = false;

            this.$nextTick(this.closeDetailPage);
        },
        onModalSaveDraft() {
            this.showModalUnsaved = false;
            this.showModalPublish = false;

            this.$nextTick(async () => {
                await this.saveAsNewDraftAndClose();
            });
        },
        async saveAsNewDraftAndClose() {
            try {
                Shopware.State.commit('eecomBlogDetail/setLoading', true);
                await this.onClickSave();
                await this.blogDraftApiService.saveAsDraft(this.blog, this.draftName);

                this.closeDetailPage();
            } catch {
                this.onError('publisher.error.action.draft');
            } finally {
                this.draftName = '';
                Shopware.State.commit('eecomBlogDetail/setLoading', false);
            }
        },
        onModalSavePublish() {
            this.showModalPublish = false;

            this.$nextTick(async () => {
                if (this.isEntityDraft) {
                    this.saveAndPublish();
                } else {
                    await this.onClickSave();
                }
            });
        },
        getCmsPageOverrides() {
            if (this.currentPage === null) {
                return null;
            }

            this.deleteSpecifcKeys(this.currentPage.sections);
            const changesetGenerator = new ChangesetGenerator();
            const { changes } = changesetGenerator.generate(this.currentPage);

            const slotOverrides = {};
            if (changes === null) {
                return slotOverrides;
            }

            if (type.isArray(changes.sections)) {
                changes.sections.forEach((section) => {
                    if (type.isArray(section.blocks)) {
                        section.blocks.forEach((block) => {
                            if (type.isArray(block.slots)) {
                                block.slots.forEach((slot) => {
                                    slotOverrides[slot.id] = slot.config;
                                });
                            }
                        });
                    }
                });
            }

            return slotOverrides;
        },
        deleteSpecifcKeys(sections) {
            if (!sections) {
                return;
            }

            sections.forEach((section) => {
                if (!section.blocks) {
                    return;
                }

                section.blocks.forEach((block) => {
                    if (!block.slots) {
                        return;
                    }

                    block.slots.forEach((slot) => {
                        if (!slot.config) {
                            return;
                        }

                        Object.values(slot.config).forEach((configField) => {
                            if (configField.entity) {
                                delete configField.entity;
                            }
                            if (configField.required) {
                                delete configField.required;
                            }
                            if (configField.type) {
                                delete configField.type;
                            }
                        });
                    });
                });
            });
        },
    }
});
