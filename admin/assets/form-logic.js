/**
 * Describes a basic Core-Form and allows conditional rendering
 */
class Form{

    /**
     * Form constructor
     * @param {string} id - ID of the main Wrapper, which contains all Elements
     * @param {string} prefix - The Field prefix
     * @param {Condition[]} conditions - List of Condition-objects
     */
    constructor(id, prefix, conditions){

        /**
         * The ID of the Form-Wrapper
         * @type {string}
         */
        this.id = id;

        /**
         * The field prefix
         * @type {string}
         */
        this.prefix = prefix;

        /**
         * The Wrapper around the Form
         * @type {*|jQuery|HTMLElement}
         */
        this.wrapper = $('#' + this.id);

        /**
         * List of Condition-objects
         * @type {Condition[]}
         */
        this.logic = conditions;

        /**
         * List of all Form-Elements within the Form-Element
         * @type {*|jQuery|HTMLElement}
         */
        this.fields = this.wrapper.find('[name]');
    }

    /**
     * Hooks the conditional rendering to all Elements
     * and triggers one initial check
     */
    init(){
        this.fields.on('change keyup formInit', () => {
            this.handleChange();
        }).first().trigger('formInit');
    }

    /**
     * Checks all conditions and show or hide
     * the Form-Elements according to the check-result
     */
    handleChange(){
        this.resetObjects();
        this.logic.forEach((logic) => {
            let target = $('[name="' + this.prefix + logic.name + '"]'),
                check = $('[name="' + this.prefix + logic.field + '"]');

            // Search for data-name if we are most likely looking for a wrapping item
            if (target.length === 0) {
                target = $('[data-name="' + logic.name + '"');
            }

            if (check.length > 0) {
                // disjunctive normal form for the condition
                if (!logic.not && this.constructor.getValue(check) === logic.value || logic.not && this.constructor.getValue(check) !== logic.value) {
                    this.stageObject(target, this.prefix + logic.name, true);
                }
                else {
                    this.stageObject(target, this.prefix + logic.name, false);
                }
            }
        });
        this.resolveObjects();
    }

    /**
     * Resets the forms Target-States
     */
    resetObjects(){
        this.targets = {};
    }

    /**
     * Sets the target state in the forms Target-State
     * @param target  the given target to stage
     * @param name the identifier key of the given target
     * @param show if the target should be hidden or shown
     */
    stageObject(target, name, show){
        if (!this.targets[name]) {
            this.targets[name] = {
                target: target,
                show: show
            }
        }
        else {
            if (this.targets[name].show) {
                this.targets[name].show = show;
            }
        }
    }

    /**
     * Resolves the target states and shows/hides the Element depending on the logic-results
     */
    resolveObjects(){
        const className = 'js-core-target';
        for (let item in this.targets) {
            let target = this.targets[item];
            let wrapper = target.target;
            if (!wrapper.hasClass(className)) {
                wrapper = target.target.parents('.' + className).first();
            }
            wrapper.toggle(target.show);
        }
    }

    /**
     * Helps to get the value from a checkbox or radio input
     * @param check
     * @returns {*|jQuery|HTMLElement} the Element to get the Value from
     */
    static getValue(check){
        if (['checkbox', 'radio'].indexOf(check.attr('type')) >= 0) {
            if (check.prop('checked')) {
                return check.val();
            }
            else {
                return '';
            }
        }
        return check.val();
    }
}

/**
 * Describes a Condition used for conditional rendering in the Form-Class
 */
class Condition{

    /**
     * Condition constructor
     * @param {string} field the field to check
     * @param {string} value the value the field needs to have, for the condition to be be met
     * @param {bool} not if the condition should be inverted
     * @param {string} name the origin field name
     */
    constructor(field, value, not, name){
        this.field = field;
        this.value = value;
        this.not = not;
        this.name = name;
    }
}


jQuery(function ($){

    const repeaterAddSelector = '.core-repeater > .core-repeater__menu .core-repeater__button';
    const repeatRemoveSelector = '.core-repeater > .core-repeater__container > .core-repeater__item > .core-repeater__button';
    const doc = $(document);

    /**
     * Rewrites a Repeat-Item to a new array position (number)
     * @param {jQuery} item the item to rewrite
     * @param {number} i  the new position in the array
     */
    function alignNames(item, i){
        const pattern = item.attr('data-repeat-pattern');
        const digitExp = new RegExp('d\\+', 'g');
        const replace = pattern.replace(digitExp, i).replace(/\\/g, '');
        const data = $.merge(item, item.find('*'));

        // Update the data-repeat-id
        item.attr('data-repeat-id', i);

        // Filter all attributes from the item and all its children
        data.each(function (){
            const item = $(this);
            let att;
            let currentAtt;
            for (let i = 0; i < this.attributes.length; i++) {
                att = this.attributes[i].name;
                currentAtt = item.attr(att);
                item.attr(att, currentAtt.replace(new RegExp(pattern, 'g'), replace));
            }
        });
    }

    /**
     * Allows drag-and drop sorting and rewrites all array positions
     * to fit the new sort-order
     */
    $('.core-repeater__container').sortable().on('sortstop', function (){
        let i = 0;
        $(this).children('.core-repeater__item').each(function (){
            alignNames($(this), i);
            i++;
        });
    });

    /**
     * Adds a new element to the repeater by coping
     * and preparing the template html string
     */
    doc.on('click', repeaterAddSelector, function (event){
        event.preventDefault();
        const menu = $(this).parent('.core-repeater__menu');
        const repeater = menu.parent('.core-repeater');
        const container = menu.siblings('.core-repeater__container');
        const id = repeater.attr('data-repeat-id');
        let tpl = repeater.find('.core-repeater__template').val();

        // Build Template Object
        tpl = tpl.replace(new RegExp(id, 'g'), container.children('.core-repeater__item').length);
        tpl = $(tpl);

        // Append Template to the repeater
        tpl.hide().appendTo(container).slideDown(300);
    });

    /**
     * Removes an element from the repeater and fixes
     * the array position of all following items
     */
    doc.on('click', repeatRemoveSelector, function (event){
        event.preventDefault();
        const className = '.core-repeater__item';
        const item = $(this).parent(className);
        let next = item.next(className);
        while (next.length > 0) {
            alignNames(next, parseInt(next.attr('data-repeat-id')) - 1);
            next = next.next(className);
        }
        item.slideUp(300, function (){
            item.remove();
        });
    });
});