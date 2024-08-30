import template from './eecom-blog-category-detail-category-seo.html.twig';

const { Component } = Shopware;
const { mapGetters, mapState } = Shopware.Component.getComponentHelper();

Component.register('eecom-blog-category-detail-category-seo', {
    template,
    inject: ['feature'],
    computed: {
        ...mapState('eecomBlogCategoryDetail', [
            'category'
        ]),

        ...mapGetters('eecomBlogCategoryDetail', [
            'isLoading'
        ])
    },
});
