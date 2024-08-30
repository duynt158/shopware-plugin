import template from './eecom-blog-seo-form.html.twig';

const { Component } = Shopware;

Component.register('eecom-blog-seo-form', {
    template,

    props: {
        blog: {
            type: Object,
            required: true,
        },
    },
});
