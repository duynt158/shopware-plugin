import './state/eecom-blog-publisher.state';
import './mixin/eecom-blog-draft.mixin';
import './mixin/eecom-blog-activity.mixin';
import './mixin/eecom-blog-page.mixin';
import './component/eecom-blog-publisher-activity-item';
import './component/eecom-blog-publisher-activity-feed';
import './component/eecom-blog-publisher-activity-stack';
const DRAFT_VERSION_PARAMETER = 'draftVersionId';
const VERSIONABLE_ROUTES = ['eecom.blog.detail'];

Shopware.Module.register('eecom-blog-publisher', {
    routeMiddleware: (next, currentRoute) => {
        if (!VERSIONABLE_ROUTES.includes(currentRoute.name)) {
            next(currentRoute);
            return;
        }

        currentRoute.path = `${currentRoute.path}/:${DRAFT_VERSION_PARAMETER}?`;

    }
});
