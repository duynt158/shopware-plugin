const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-cms-list', {
    data() {
        return {
            assignablePageTypes: ['categories', 'products', 'blogs'],
        };
    },

    computed: {
        sortPageTypes() {
            const sortPageTypes = this.$super('sortPageTypes');
            sortPageTypes.push({ value: 'blog_page', name: this.$tc('eecom-blog.page-type.type') });



            return sortPageTypes;

        },
        listCriteria() {
            const listCriteria = this.$super('listCriteria');
            listCriteria.addAssociation('blogs');

            return listCriteria;
        }
    }

});
