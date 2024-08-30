import template from './eecom-blog-category-form.html.twig';


const { Component, Context, Mixin } = Shopware;
const { EntityCollection, Criteria } = Shopware.Data;
const { isEmpty } = Shopware.Utils.types;
const { mapPropertyErrors, mapState, mapGetters } = Shopware.Component.getComponentHelper();

Component.register('eecom-blog-category-form', {
    template,

    inject: ['repositoryFactory', 'systemConfigApiService', 'feature'],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        allowEdit: {
            type: Boolean,
            required: false,
            default: true,
        },
    },

    data() {
        return {
            displayVisibilityDetail: false,
            multiSelectVisible: true,
            salesChannel: null,
        };
    },

    computed: {
        ...mapState('eecomBlogDetail', [
            'loading',
            'blog'
        ]),

        ...mapGetters('eecomBlogDetail', [
            'isLoading'
        ]),
        ...mapPropertyErrors('blog', [
            'name'
        ]),

        hasSelectedVisibilities() {
            if (this.blog && this.blog.visibilities) {
                return this.blog.visibilities.length > 0;
            }
            return false;
        },

        blogVisibilityRepository() {
            return this.repositoryFactory.create(this.blog.visibilities.entity);
        },

        salesChannelRepository() {
            return this.repositoryFactory.create('sales_channel');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.salesChannel = new EntityCollection(
                '/sales-channel',
                'sales_channel',
                Shopware.Context.api,
                new Criteria(),
            );

            if (this.feature.isActive('FEATURE_NEXT_12437')) {
                this.fetchSalesChannelSystemConfig();
            }
        },

        displayAdvancedVisibility() {
            this.displayVisibilityDetail = true;
        },

        closeAdvancedVisibility() {
            this.displayVisibilityDetail = false;
        },

        visibilitiesRemoveInheritanceFunction(newValue) {
            newValue.forEach(({ salesChannelId, salesChannel, visibility }) => {
                const visibilities = this.blogVisibilityRepository.create(Context.api);

                Object.assign(visibilities, {
                    eecomBlogId: this.blog.id,
                    salesChannelId,
                    salesChannel,
                    visibility,
                });

                this.blog.visibilities.push(visibilities);
            });

            this.$refs.blogVisibilitiesInheritance.forceInheritanceRemove = true;

            return this.blog.visibilities;
        },

        fetchSalesChannelSystemConfig() {
            if (!this.blog.isNew()) {
                return Promise.reject();
            }

            return this.systemConfigApiService.getValues('core.defaultSalesChannel')
                .then(configData => {
                    if (isEmpty(configData)) {
                        return Promise.resolve();
                    }

                    const defaultSalesChannelIds = configData?.['core.defaultSalesChannel.salesChannel'];
                    const defaultVisibilities = configData?.['core.defaultSalesChannel.visibility'];
                    this.blog.active = !!configData?.['core.defaultSalesChannel.active'];

                    return this.fetchSalesChannelByIds(defaultSalesChannelIds).then(res => {
                        if (!res.length) {
                            return Promise.resolve();
                        }

                        res.forEach(el => {
                            const visibilities = this.createBlogVisibilityEntity(defaultVisibilities, el);
                            this.blog.visibilities.push(visibilities);
                        });

                        return Promise.resolve();
                    });
                })
                .catch(() => {
                    this.createNotificationError({
                        message: this.$tc('sw-product.visibility.errorMessage'),
                    });
                });
        },

        fetchSalesChannelByIds(ids) {
            const criteria = new Criteria();

            criteria.addFilter(Criteria.equalsAny('id', ids));

            return this.salesChannelRepository.search(criteria);
        },

        createBlogVisibilityEntity(visibility, salesChannel) {
            const visibilities = this.blogVisibilityRepository.create(Context.api);

            Object.assign(visibilities, {
                visibility: visibility[salesChannel.id],
                eecomBlogId: this.blog.id,
                salesChannelId: salesChannel.id,
                salesChannel: salesChannel,
            });

            return visibilities;
        },
    },
});
