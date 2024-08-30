import template from './eecom-blog-publisher-activity-feed.html.twig';
import './eecom-blog-publisher-activity-feed.scss';

const { Component, State } = Shopware;

Component.register('eecom-blog-publisher-activity-feed', {
    template,
    props: ['entity'],
    computed: {
        publisherState() {
            return State.get('eecom-blog-publisher');
        },
        hasActivity() {

            return this.activity.length;
        },
        activity() {
            return this.publisherState.activity;
        },
        pageLocked() {

            return false;
        }
    }
});
