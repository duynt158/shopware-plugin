import template from './eecom-blog-detail-blog-layout.html.twig';
import './eecom-blog-detail-blog-layout.scss';

const { Component, State, Context, Utils } = Shopware;
const { Criteria } = Shopware.Data;
const { mapState, mapGetters } = Component.getComponentHelper();
const { cloneDeep, merge, get } = Utils.object;

Component.register('eecom-blog-detail-blog-layout', {
    template,

    inject: ['repositoryFactory', 'cmsService', 'feature'],

    data() {
        return {
            showLayoutModal: false,
            isConfigLoading: false,
        };
    },

    computed: {
        cmsPageRepository() {
            return this.repositoryFactory.create('cms_page');
        },
        defaultFolderRepository() {
            return this.repositoryFactory.create('media_default_folder');
        },
        cmsPageId() {
            return get(this.blog, 'cmsPageId', null);
        },

        showCmsForm() {
            return (!this.isLoading || !this.isConfigLoading) && !this.currentPage.locked;
        },

        ...mapState('eecomBlogDetail', [
            'blog',
        ]),

        ...mapGetters('eecomBlogDetail', [
            'isLoading',
        ]),

        ...mapState('cmsPageState', [
            'currentPage',
        ]),

        cmsPageCriteria() {
            const criteria = new Criteria();
            criteria.addAssociation('previewMedia');
            criteria.addAssociation('sections');
            criteria.getAssociation('sections').addSorting(Criteria.sort('position'));

            criteria.addAssociation('sections.blocks');
            criteria.getAssociation('sections.blocks')
                .addSorting(Criteria.sort('position', 'ASC'))
                .addAssociation('slots');

            return criteria;
        },
    },

    watch: {
        cmsPageId() {
            State.dispatch('cmsPageState/resetCmsPageState');
            this.handleGetCmsPage();
        },

        blog: {
            deep: true,
            handler(value) {
                if (!value) {
                    return;
                }

                this.updateCmsPageDataMapping();
            },
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            // Keep current layout configuration if page sections exist
            const sections = this.currentPage?.sections ?? [];

            if (sections.length) {
                return;
            }

            this.handleGetCmsPage();
            this.setPageContext();
        },
        setPageContext() {
            this.getDefaultFolderId().then((folderId) => {
                Shopware.State.commit('cmsPageState/setDefaultMediaFolderId', folderId);
            });
        },
        getDefaultFolderId() {

            const criteria = new Criteria(1, 1);
            criteria.addAssociation('folder');
            criteria.addFilter(Criteria.equals('entity', 'eecom_blog'));

            return this.defaultFolderRepository.search(criteria).then((searchResult) => {
                const defaultFolder = searchResult.first();
                if (defaultFolder.folder?.id) {
                    return defaultFolder.folder.id;
                }

                return null;
            });
        },
        onOpenLayoutModal() {

            this.showLayoutModal = true;
        },

        onCloseLayoutModal() {
            this.showLayoutModal = false;
        },

        onOpenInPageBuilder() {
            if (!this.currentPage) {
                this.$router.push({ name: 'sw.cms.create' });
            } else {
                this.$router.push({ name: 'sw.cms.detail', params: { id: this.currentPage.id } });
            }
        },

        onSelectLayout(cmsPageId) {

            if (!this.blog) {
                return;
            }

            this.blog.cmsPageId = cmsPageId;
            this.blog.slotConfig = null;
            State.commit('eecomBlogDetail/setBlog', this.blog);
        },

        handleGetCmsPage() {
            if (!this.cmsPageId) {
                return;
            }

            this.isConfigLoading = true;

            this.cmsPageRepository.get(this.cmsPageId, Context.api, this.cmsPageCriteria).then((cmsPage) => {
                if (this.blog.slotConfig && cmsPage) {
                    cmsPage.sections.forEach((section) => {
                        section.blocks.forEach((block) => {
                            block.slots.forEach((slot) => {
                                if (!this.blog.slotConfig[slot.id]) {
                                    return;
                                }

                                slot.config = slot.config || {};
                                merge(slot.config, cloneDeep(this.blog.slotConfig[slot.id]));
                            });
                        });
                    });
                }

                State.commit('cmsPageState/setCurrentPage', cmsPage);
                this.updateCmsPageDataMapping();
                this.isConfigLoading = false;
            });
        },

        updateCmsPageDataMapping() {

            Shopware.State.commit('cmsPageState/setCurrentMappingEntity', 'eecom_blog');
            Shopware.State.commit(
                'cmsPageState/setCurrentMappingTypes',
                this.cmsService.getEntityMappingTypes('eecom_blog'),
            );

            Shopware.State.commit('cmsPageState/setCurrentDemoEntity', this.blog);
            Shopware.State.commit('cmsPageState/setPageEntityName', 'eecom_blog');
        },

        onResetLayout() {
            this.onSelectLayout(null);
        },
    },
});
