import template from './sw-cms-eecom-blog-box-preview.html.twig';
import './sw-cms-eecom-blog-box-preview.scss';

const { Component } = Shopware;

Component.register('sw-cms-eecom-blog-box-preview', {
    template,

    props: {
        hasText: {
            type: Boolean,
            default: true,
            required: false,
        },
    },
});
