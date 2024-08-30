const { Component, Mixin, Utils } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();
const { ShopwareError } = Shopware.Classes;
const { debounce } = Shopware.Utils;
const { cloneDeep, getObjectDiff } = Shopware.Utils.object;
const { warn } = Shopware.Utils.debug;
const Criteria = Shopware.Data.Criteria;
const debounceTimeout = 800;
Component.override('sw-cms-detail', {
    computed: {
        cmsTypeMappingEntities() {
            const eecomBlogMapping = {
                entity: 'eecom_blog',
                mode: 'single',
            };
            const cmsTypeMappingEntities = this.$super('cmsTypeMappingEntities');

            this.$set(cmsTypeMappingEntities, 'blog_page', eecomBlogMapping)
            return cmsTypeMappingEntities;

        },
        cmsPageTypeSettings() {

            const eecomBlogPageTypeSettings = {
                entity: 'eecom_blog',
                mode: 'single',
            }
            const cmsPageTypeSettings = this.$super('cmsPageTypeSettings');
            if(this.page.type === 'blog_page')
            {
                return eecomBlogPageTypeSettings;
            }
           return cmsPageTypeSettings;

        }
    },
    methods: {
        async createdComponent() {
            Shopware.State.commit('adminMenu/collapseSidebar');

            const isSystemDefaultLanguage = Shopware.State.getters['context/isSystemDefaultLanguage'];
            this.$store.commit('cmsPageState/setIsSystemDefaultLanguage', isSystemDefaultLanguage);

            this.resetCmsPageState();

            if (this.$route.params.id) {
                this.pageId = this.$route.params.id;
                this.isLoading = true;
                const defaultStorefrontId = '8A243080F92E4C719546314B577CF82B';

                const criteria = new Criteria();
                criteria.addFilter(
                    Criteria.equals('typeId', defaultStorefrontId),
                );

                await this.salesChannelRepository.search(criteria).then(async (response) => {
                    this.salesChannels = response;

                    if (this.salesChannels.length > 0) {
                        this.currentSalesChannelKey = this.salesChannels[0].id;
                        await this.loadPage(this.pageId);
                    }
                });
            }

           await this.setPageContext();
        },
        setPageContext() {
            this.getDefaultFolderId().then((folderId) => {
                Shopware.State.commit('cmsPageState/setDefaultMediaFolderId', folderId);
            });
        },

        async loadPage(pageId) {
            this.isLoading = true;

            await this.pageRepository.get(pageId, Shopware.Context.api, this.loadPageCriteria).then(async (page) => {
                this.page = { sections: [] };
                this.page = page;

                await this.cmsDataResolverService.resolve(this.page).then(() => {
                    this.updateSectionAndBlockPositions();
                    Shopware.State.commit('cmsPageState/setCurrentPage', this.page);

                    this.updateDataMapping();
                    this.pageOrigin = cloneDeep(this.page);

                    if (this.selectedBlock) {
                        const blockId = this.selectedBlock.id;
                        const blockSectionId = this.selectedBlock.sectionId;
                        this.page.sections.forEach((section) => {
                            if (section.id === blockSectionId) {
                                section.blocks.forEach((block) => {
                                    if (block.id === blockId) {
                                        this.setSelectedBlock(blockSectionId, block);
                                    }
                                });
                            }
                        });
                    }

                    this.isLoading = false;
                }).catch((exception) => {
                    this.isLoading = false;
                    this.createNotificationError({
                        title: exception.message,
                        message: exception.response,
                    });

                    warn(this._name, exception.message, exception.response);
                });
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: exception.message,
                    message: exception.response.statusText,
                });

                warn(this._name, exception.message, exception.response);
            });
        },


        getDefaultFolderId() {
            if(this.page.type === 'blog_page')
            {
                this.cmsPageState.pageEntityName = 'eecom_blog';
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
            }
            else
            {
                return this.$super('getDefaultFolderId');
            }


        },

    }

});
