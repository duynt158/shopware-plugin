import template from './eecom-blog-publisher-activity-item.html.twig';
import './eecom-blog-publisher-activity-item.scss';

const OPERATIONS = {
    UPDATE: 'update',
    INSERT: 'insert',
    DELETE: 'delete'
};

const SNIPPET_KEYS = {
    DISCARDED: 'discarded',
    MERGED: 'merged',
    PUBLISHED: 'published',
    DRAFT_CREATED: 'draftCreated',
    LAYOUT_CREATED: 'layoutCreated',
    UPDATED: 'updated'
};

const ICONS = {
    CHECKMARK: 'small-default-checkmark-line-small',
    MINUS: 'small-default-minus-small',
    PLUS: 'small-default-plus-line-small',
    CIRCLE: 'small-default-circle-medium',
    CIRCLE_SMALL: 'small-default-circle-small'
};

const ENTITY_NAMES = {
    EECOM_BLOG: 'eecom_blog'
};

const { Component, Mixin } = Shopware;

Component.register('eecom-blog-publisher-activity-item', {
    template,
    props: ['log'],
    mixins: [
        Mixin.getByName('eecom-blog-activity')
    ],
    data() {
        return {
            isLoading: false,
            showDetails: false
        }
    },
    computed: {
        isMainLayoutEntry() {
            const { isDiscarded, isMerged, isReleasedAsNew, draftVersion } = this.log;

            return isMerged || isReleasedAsNew || (!isDiscarded && !draftVersion);
        },
        userName() {
            const { firstName, lastName, userName } = this.log.user;

            if (firstName || lastName) {
                return `${firstName} ${lastName}`;
            }

            return userName;
        },
        isCurrentPage() {
            const { draftVersion } = this.log;
            const { draftVersionId } = this.$route.params;
            return (draftVersion === draftVersionId) || (!draftVersion && !draftVersionId);
        },
        canNavigate() {
            const { isDiscarded, isMerged, isReleasedAsNew } = this.log;

            if (isMerged || isDiscarded || isReleasedAsNew || this.isCurrentPage) {
                return false;
            }

            return true;
        },
        isCreatedLayout() {
            return this.log.details && this.log.details.some(({ entityName, operation }) => {
                return (entityName === ENTITY_NAMES.EECOM_BLOG && operation === OPERATIONS.INSERT);
            });
        },
        isCreatedDraft() {
            const { draftVersion, details } = this.log;

            return draftVersion && (!details || !details.length);
        }
    },
    methods: {
        getDraftName() {
            return this.log.name;
        },
        getBadgeType() {
            const { isCreatedLayout, isCreatedDraft } = this;
            const { isDiscarded, isMerged, isReleasedAsNew, details } = this.log;

            let snippet = 'publisher.activity.feed.badges.eecom_blog.';
            let count = 0;
            let params = {};

            if (isDiscarded) {
                snippet += SNIPPET_KEYS.DISCARDED;
            } else if (isMerged) {
                snippet += SNIPPET_KEYS.MERGED;
            } else if (isReleasedAsNew) {
                snippet += SNIPPET_KEYS.PUBLISHED;
            } else if (isCreatedLayout) {
                snippet += SNIPPET_KEYS.LAYOUT_CREATED;
            } else if (isCreatedDraft) {
                snippet += SNIPPET_KEYS.DRAFT_CREATED;
            } else {
                snippet += SNIPPET_KEYS.UPDATED;
                count = details ? details.length : 0;
                params = { detailCount: count };
            }

            return this.$tc(snippet, count, params);
        },
        getBadgeIcon() {
            const { isCreatedLayout, isCreatedDraft } = this;
            const { isDiscarded, isMerged, isReleasedAsNew } = this.log;

            if (isMerged || isReleasedAsNew) {
                return ICONS.CHECKMARK;
            } else if (isDiscarded) {
                return ICONS.MINUS;
            } else if (isCreatedDraft || isCreatedLayout) {
                return ICONS.PLUS;
            } else {
                return ICONS.CIRCLE;
            }
        },
        getBadgeMessage() {
            const { isDiscarded, isMerged } = this.log;

            let snippet = 'publisher.activity.feed.badgeMessages.eecom_blog.';

            if (isDiscarded) {
                snippet += SNIPPET_KEYS.DISCARDED;
            } else if (isMerged) {
                snippet += SNIPPET_KEYS.MERGED;
            }

            return this.$tc(snippet, null, { draftName: this.getDraftName() });
        },
        onClickDetails() {
            this.showDetails = !this.showDetails;
        },
        getDetailIcon(operation) {
            switch(operation) {
                case OPERATIONS.DELETE:
                    return ICONS.MINUS;
                case OPERATIONS.UPDATE:
                    return ICONS.CIRCLE_SMALL;
                case OPERATIONS.INSERT:
                default:
                    return ICONS.PLUS;
            }
        },
        getDetailSnippet({ entityName, operation }) {
            return this.$tc(`publisher.activity.feed.detail.${entityName}.${operation}`);
        },
        async onClickLayoutName({ eecomBlogId, draftVersion }) {
            if (!this.canNavigate) {
                return;
            }

            const { href } = this.$router.resolve({
                name: 'eecom.blog.detail',
                params: {
                    id: eecomBlogId,
                    draftVersionId: draftVersion
                }
            });

            window.open(href);
        },
        onDetailMouseEnter(targetId) {
            const target = document.querySelector(`[data-publisher-selector="${targetId}"]`);

            if (!target) {
                return;
            }

            target.parentElement.classList.add('publisher-highlight-changes');
        },
        onDetailMouseLeave(targetId) {
            const target = document.querySelector(`[data-publisher-selector="${targetId}"]`);

            if (!target) {
                return;
            }

            target.parentElement.classList.remove('publisher-highlight-changes');
        }
    }
});
