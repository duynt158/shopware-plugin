import template from './eecom-blog-category-seo-form.html.twig';

const { Component } = Shopware;

Component.register('eecom-blog-category-seo-form', {
    template,

    props: {
        category: {
            type: Object,
            required: true,
        },
    },
});
