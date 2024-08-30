import template from './eecom-blog-detail-blog-data.html.twig';
import './eecom-blog-detail-blog-data.scss';

const { Component,Context, Mixin } = Shopware;
const {Criteria} = Shopware.Data;
const { mapPropertyErrors, mapGetters, mapState } = Shopware.Component.getComponentHelper();

Component.register('eecom-blog-detail-blog-data', {
    template,

    mixins: [
        Mixin.getByName('placeholder')
    ],

    inject: [
        'repositoryFactory'
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
            mediaDefaultFolderId: null,
            columnCount: 5,
            columnWidth: 90,
            uploadTag: 'eecom-blog-teaser-upload-tag',
            displayTeaserItem: null,
        };
    },

    computed: {
        isTitleRequired() {
            return Shopware.State.getters['context/isSystemDefaultLanguage'];
        },



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

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        gridAutoRows() {
            return `grid-auto-rows: ${this.columnWidth}`;
        },
        mediaDefaultFolderRepository() {
            return this.repositoryFactory.create('media_default_folder');
        },
        mediaFolderRepository() {
            return this.repositoryFactory.create('media_folder');
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
        createdComponent() {
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

        onMediaUploadButtonOpenSidebar() {
            this.$root.$emit('sidebar-toggle-open');
        },


        setMediaItem({ targetId }) {
            // on replace
            this.mediaRepository.get(targetId, Shopware.Context.api).then((response) => {
                this.blog.media = response;
            });

            this.blog.teaserId = targetId;
        },

        onDropMedia(mediaEntity) {
            this.setMediaItem({ targetId: mediaEntity.id });
        },
        onUnlinkImage() {
            this.blog.teaserId = null;
            this.blog.media = null;
        }

    }
});
