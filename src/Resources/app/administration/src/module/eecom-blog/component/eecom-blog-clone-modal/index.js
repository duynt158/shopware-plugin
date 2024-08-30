import template from './eecom-blog-clone-modal.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('eecom-blog-clone-modal', {
    template,

    inject: ['repositoryFactory', 'numberRangeService'],

    props: {
        blog: {
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
            return this.repositoryFactory.create('eecom_blog');
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
                    name: `${this.blog.name} ${this.$tc('sw-product.general.copy')}`,
                    active: false,
                },
            };

            await this.repository.save(this.blog);
            const clone = await this.repository.clone(this.blog.id, Shopware.Context.api, behavior);
            this.$emit('clone-finish', { id:  clone.id });

        }

    },
});
