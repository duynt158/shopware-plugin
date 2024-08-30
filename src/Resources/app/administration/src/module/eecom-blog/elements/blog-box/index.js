import './component';
import './config';
import './preview';

const Criteria = Shopware.Data.Criteria;
const criteria = new Criteria();
criteria.addAssociation('media');

Shopware.Service('cmsService').registerCmsElement({
    name: 'eecom-blog-box',
    label: 'sw-cms.elements.eecomBlogBox.label',
    component: 'sw-cms-el-eecom-blog-box',
    previewComponent: 'sw-cms-el-preview-eecom-blog-box',
    configComponent: 'sw-cms-el-config-eecom-blog-box',
    defaultConfig: {
        blog: {
            source: 'static',
            value: null,
            required: true,
            entity: {
                name: 'eecom_blog',
                criteria: criteria,
            },
        },
        boxLayout: {
            source: 'static',
            value: 'standard',
        },
        displayMode: {
            source: 'static',
            value: 'standard',
        },
        verticalAlign: {
            source: 'static',
            value: null,
        },
    },
    defaultData: {
        boxLayout: 'standard',
        product: null,
    },
    collect: function collect(elem) {
        const context = Object.assign(
            {},
            Shopware.Context.api,
            { inheritance: true },
        );

        const criteriaList = {};

        Object.keys(elem.config).forEach((configKey) => {
            if (elem.config[configKey].source === 'mapped') {
                return;
            }

            const entity = elem.config[configKey].entity;

            if (entity && elem.config[configKey].value) {
                const entityKey = entity.name;
                const entityData = {
                    value: [elem.config[configKey].value],
                    key: configKey,
                    searchCriteria: entity.criteria ? entity.criteria : new Criteria(),
                    ...entity,
                };

                entityData.searchCriteria.setIds(entityData.value);
                entityData.context = context;

                criteriaList[`entity-${entityKey}`] = entityData;
            }
        });

        return criteriaList;
    },
});
