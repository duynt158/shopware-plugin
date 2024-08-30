import template from './eecom-blog-category-clone-modal.html.twig';
import './eecom-blog-category-clone-modal.scss';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('eecom-blog-category-clone-modal', {
    template,

    inject: ['repositoryFactory', 'numberRangeService'],

    props: {
        category: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            cloningVariants: false,
            cloneMaxProgress: 0,
            cloneProgress: 0,
        };
    },

    computed: {
        progressInPercentage() {
            return 100 / this.cloneMaxProgress * this.cloneProgress;
        },

        repository() {
            return this.repositoryFactory.create('eecom_blog_category');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            await this.cloneParent();
        },
        async cloneParent() {
            const behavior = {
                overwrites: {
                    name: `${this.category.name} ${this.$tc('sw-product.general.copy')}`,
                    active: false,
                },
            };

            await this.repository.save(this.category);
            const clone = await this.repository.clone(this.category.id, Shopware.Context.api, behavior);
            this.$emit('clone-finish', { id:  clone.id });

        },

    },
});
