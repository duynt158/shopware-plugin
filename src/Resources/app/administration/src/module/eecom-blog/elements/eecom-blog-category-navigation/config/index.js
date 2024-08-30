import template from './sw-cms-el-config-eecom-blog-category-navigation.html.twig';
import './sw-cms-el-config-eecom-blog-category-navigation.scss'

const { Component,Context, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;
Component.register('sw-cms-el-config-eecom-blog-category-navigation', {
    template,
    inject: ['repositoryFactory', 'feature'],
    mixins: [
        Mixin.getByName('cms-element'),
    ],
    data() {
        return {
            mediaModalIsOpen: false,
            initialFolderId: null,
        };
    },
    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        uploadTag() {
            return `cms-element-media-config-${this.element.id}`;
        },
        mediaFolderRepository() {
            return this.repositoryFactory.create('media_folder');
        },
        mediaDefaultFolderRepository() {
            return this.repositoryFactory.create('media_default_folder');
        },
        previewSource() {
            if (this.element.data && this.element.data.media && this.element.data.media.id) {
                return this.element.data.media;
            }

            return this.element.config.media.value;
        },
        mediaDefaultFolderCriteria() {
            const criteria = new Criteria(1, 1);

            criteria.addAssociation('folder.children');
            criteria.addFilter(Criteria.equals('entity', 'eecom_blog'));

            return criteria;
        },
        mediaFolderCriteria() {
            const criteria = new Criteria(1, 1);

            criteria.addFilter(Criteria.equals('name', 'blog'));


            return criteria;
        },

    },

    created() {
        this.createdComponent();
    },
    methods: {
        async createdComponent() {
            this.initElementConfig('eecom-blog-category-navigation');
            this.getMediaDefaultFolderId().then((mediaFolderId) => {
                this.mediaDefaultFolderId = mediaFolderId;
            });

        },
        getMediaDefaultFolderId() {
            return this.mediaDefaultFolderRepository.search(this.mediaDefaultFolderCriteria, Context.api)
                .then((mediaDefaultFolder) => {

                    const defaultFolder = mediaDefaultFolder.first();

                    if (defaultFolder.folder?.id) {
                        return defaultFolder.folder.id;
                    }

                    return null;
                });
        },
        async onImageUpload({ targetId }) {
            const mediaEntity = await this.mediaRepository.get(targetId);

            this.element.config.media.value = mediaEntity.id;

            this.updateElementData(mediaEntity);

            this.$emit('element-update', this.element);
        },
        onImageRemove() {
            this.element.config.media.value = null;

            this.updateElementData();

            this.$emit('element-update', this.element);
        },
        onCloseModal() {
            this.mediaModalIsOpen = false;
        },
        onSelectionChanges(mediaEntity) {
            const media = mediaEntity[0];
            this.element.config.media.value = media.id;

            this.updateElementData(media);

            this.$emit('element-update', this.element);
        },
        updateElementData(media = null) {
            const mediaId = media === null ? null : media.id;

            if (!this.element.data) {
                this.$set(this.element, 'data', { mediaId });
                this.$set(this.element, 'data', { media });
            } else {
                this.$set(this.element.data, 'mediaId', mediaId);
                this.$set(this.element.data, 'media', media);
            }
        },

        onOpenMediaModal() {
            this.mediaModalIsOpen = true;
        },

        onChangeMinHeight(value) {
            this.element.config.minHeight.value = value === null ? '' : value;

            this.$emit('element-update', this.element);
        },
        onChangeDisplayMode(value) {
            if (value === 'cover') {
                this.element.config.verticalAlign.value = null;
            }

            this.$emit('element-update', this.element);
        },

    }
});
