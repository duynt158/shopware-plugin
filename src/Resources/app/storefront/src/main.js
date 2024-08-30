import EEComBlogCategoryFilter from './EEComBlogCategoryFilter/index';
import EEComBlogCategoryActiveItem from './EEComBlogCategoryActiveItem/index';


window.PluginManager.register(
    'EEComBlogCategoryFilter',
    EEComBlogCategoryFilter,
    '[data-eecom-blog-category-filter]'
);

window.PluginManager.register(
    'EEComBlogCategoryActiveItem',
    EEComBlogCategoryActiveItem,
    '[data-eecom-blog-category-item]'
);

if (module.hot) {
    module.hot.accept();
}
