import template from './eecom-blog-detail-blog-seo.html.twig';

const { Component } = Shopware;
const { mapGetters, mapState } = Shopware.Component.getComponentHelper();

Component.register('eecom-blog-detail-blog-seo', {
    template,
    inject: ['feature'],
    computed: {
        ...mapState('eecomBlogDetail', [
            'blog'
        ]),

        ...mapGetters('eecomBlogDetail', [
            'isLoading'
        ])
    },
});
