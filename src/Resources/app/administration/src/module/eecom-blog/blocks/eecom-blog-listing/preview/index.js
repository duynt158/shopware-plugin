import template from './sw-cms-preview-eecom-blog-listing.html.twig';
import './sw-cms-preview-eecom-blog-listing.scss';

Shopware.Component.register('sw-cms-preview-eecom-blog-listing', {
    template,

    computed: {
        today() {
            return new Date().toLocaleDateString();
        }
    }
});
