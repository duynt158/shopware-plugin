import template from './eecom-blog-layout-assignment.html.twig';


const { Component } = Shopware;

Component.register('eecom-blog-layout-assignment', {
    template,


    props: {
        cmsPage: {
            type: Object,
            required: false,
            default: null,
        },
    },

    methods: {
        openLayoutModal() {
            this.$emit('modal-layout-open');
        },

        openInPageBuilder() {
            this.$emit('button-edit-click');
        },

        onLayoutReset() {
            this.$emit('button-delete-click');
        },
    },
});
