import template from './eecom-blog-category-detail-category-data.html.twig';
import './eecom-blog-category-detail-category-data.scss';
const { Component, Mixin } = Shopware;
const { mapPropertyErrors, mapGetters, mapState } = Shopware.Component.getComponentHelper();

Component.register('eecom-blog-category-detail-category-data', {
    template,

    mixins: [
        Mixin.getByName('placeholder')
    ],

    inject: [
        'repositoryFactory'
    ],
    props: {
        categoryId: {
            type: String,
            required: false,
            default: null
        }
    },
    data() {
        return {
            league: null,
            columnCount: 5,
            columnWidth: 90
        };
    },
    computed: {
        ...mapState('eecomBlogCategoryDetail', [
            'loading',
            'category'
        ]),

        ...mapGetters('eecomBlogCategoryDetail', [
            'isLoading'
        ]),

        ...mapPropertyErrors('category', [
            'name'
        ]),
        isTitleRequired() {
            return Shopware.State.getters['context/isSystemDefaultLanguage'];
        },
    }

});
