import DomAccess from 'src/helper/dom-access.helper';
import FilterBasePlugin from 'src/plugin/listing/filter-base.plugin';
import deepmerge from 'deepmerge';
import Iterator from 'src/helper/iterator.helper';

export default class EEComBlogCategoryFilter extends FilterBasePlugin {

    static options = deepmerge(FilterBasePlugin.options, {
        eecomBlogCategories: '',
    });

    init() {

        this.tempValue = null;
        this._initButtons();
    }


    /**
     * @private
     */
    _initButtons() {

        this.buttons = DomAccess.querySelectorAll(this.el, '.eecom-blog-category-navigation input[type=radio]');

        if (this.buttons) {
            this._registerButtonEvents();
        }

    }

    /**
     * @private
     */
    _registerButtonEvents() {
        this.buttons.forEach((radio) => {
            radio.addEventListener('change', this.onChangeCategory.bind(this));
        });
    }


    onChangeCategory(event) {

        this.tempValue = event.target.value;

        this.listing.changeListing(true, { p: 1 });
        this.tempValue = null;
        this.colorOption = jQuery.parseJSON(document.querySelector('.eecom-blog-category-navigation').getAttribute('data-eecom-blog-navigation-options'));
        const selected = DomAccess.querySelector(this.el, '.eecom-blog-category-navigation input[type=radio]:checked')


    }

    /**
     * @public
     */
    reset() {
    }

    /**
     * @public
     */
    resetAll() {
    }

    /**
     * @return {Object}
     * @public
     */
    getValues() {
        if (this.tempValue !== null) {
            return {eecomBlogCategories: this.tempValue};
        }
        return {eecomBlogCategories: ''};
    }

    afterContentChange() {
        // this._initButtons();
    }

    /**
     * @return {Array}
     * @public
     */
    getLabels() {
        return [];
    }

    setValuesFromUrl(params) {
        let stateChanged = false;
        this.tempValue = '';
        const properties = params['eecomBlogCategories'];
        Object.keys(params).forEach(key => {
            if (key === 'eecomBlogCategories') {
                this.currentCategory = params[key];


                const radios = DomAccess.querySelectorAll(this.el, '.eecom-blog-category-navigation input[type=radio]', false);
                if (radios) {
                    Iterator.iterate(radios, (radio) => {
                        if (radio.value === this.currentCategory) {
                            radio.checked = true;
                        }
                    });
                }

                stateChanged = true;
            }
        });


    }
}
