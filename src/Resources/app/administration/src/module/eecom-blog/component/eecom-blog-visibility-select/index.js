import template from './eecom-blog-visibility-select.html.twig';

const { Component } = Shopware;
const { EntityCollection } = Shopware.Data;
const { mapState } = Shopware.Component.getComponentHelper();

Component.extend('eecom-blog-visibility-select', 'sw-entity-multi-select', {
    template,

    computed: {
        ...mapState('eecomBlogDetail', [
            'blog',
        ]),
        repository() {
            return this.repositoryFactory.create('sales_channel');
        },
        associationRepository() {
            return this.repositoryFactory.create('eecom_blog_visibility');
        },
    },

    methods: {
        isSelected(item) {
            return this.currentCollection.some(entity => {
                return entity.salesChannelId === item.id;
            });
        },

        addItem(item) {

            // Remove when already selected
            if (this.isSelected(item)) {
                const associationEntity = this.currentCollection.find(entity => {
                    return entity.salesChannelId === item.id;
                });
                this.remove(associationEntity);
                return;
            }

            // Create new entity
            const newSalesChannelAssociation = this.associationRepository.create(this.entityCollection.context);
            newSalesChannelAssociation.eecomBlogId = this.blog.id;
            newSalesChannelAssociation.salesChannelId = item.id;
            newSalesChannelAssociation.visibility = 30;
            newSalesChannelAssociation.salesChannel = item;

            this.$emit('item-add', item);

            const changedCollection = EntityCollection.fromCollection(this.currentCollection);
            changedCollection.add(newSalesChannelAssociation);

            this.emitChanges(changedCollection);
            this.onSelectExpanded();
        },
    },
});
