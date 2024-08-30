import template from './sw-cms-block-eecom-blog-heading.html.twig';
import './sw-cms-block-eecom-blog-heading.scss';

const { Component, State } = Shopware;

Component.register('sw-cms-block-eecom-blog-heading', {
    template,

    computed: {
        currentDeviceView() {
            return State.get('cmsPageState').currentCmsDeviceView;
        },

        currentDeviceViewClass() {
            if (this.currentDeviceView) {
                return `is--${this.currentDeviceView}`;
            }

            return null;
        },
    },
});
