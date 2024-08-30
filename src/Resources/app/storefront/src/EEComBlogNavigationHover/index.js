import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';

export default class EEComBlogNavigationHover extends Plugin {
    /**
     * Plugin options
     *
     */
    static options = {

        /**
         * The old hoverPrimaryColor hash that needs to get updated.
         * If this property is set we are dealing with a configuration edit.
         *
         * @type {string}
         */
        liSelector: '.eecom-blog-category-navigation-entry',

        /**
         * Holds the string to identify a option of the image upload type
         * @type {string}
         */
        hoverColorGradiant: '[data-eecom-blog-navigation-options]'


    };

    /**
     * Initializes the plugin.
     *
     * @constructor
     * @returns {void}
     */
    init() {
        this.configurator = this.el;
        const liSelector = DomAccess.querySelectorAll(this.configurator, this.options.liSelector);
        this.colorOption = jQuery.parseJSON(document.querySelector('.eecom-blog-category-navigation').getAttribute('data-eecom-blog-navigation-options'));
        liSelector.forEach((liSelector) => {
            liSelector.addEventListener('mouseover', this.onLiHover.bind(this));
            liSelector.addEventListener('mouseout', this.onLiOut.bind(this));

        });
    }
    onLiHover(event) {
        const clickTarget = event.target;
        clickTarget.setAttribute("style", "background-color:"+this.colorOption.hoverPrimaryColor+";color: "+this.colorOption.textColor+"");
    }
    onLiOut(event) {
        const clickTarget = event.target;
        clickTarget.setAttribute("style", "background-color:''; color:"+this.colorOption.textColor+" ");

    }




}
