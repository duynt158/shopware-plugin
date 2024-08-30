import Plugin from 'src/plugin-system/plugin.class';

export default class EEComBlogNavigationHover extends Plugin {


    /**
     * Initializes the plugin.
     *
     * @constructor
     * @returns {void}
     */
    init() {
        this._registerEvents();
    }

    _registerEvents() {
        this.el.addEventListener('click', e => {
            document.querySelectorAll('.eecom-blog-category-navigation-entry').forEach(el => el.classList.remove('is--active'));
            this.el.classList.add("is--active");
        })
    }


}
