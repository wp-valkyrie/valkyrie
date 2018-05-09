
/**
 * Describes a basic Core-Form and allows conditional rendering
 */
class Form {

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
        this.wrapper = $('#'+this.id);

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
        this.fields.on('change keyup formInit', ()=>{
            this.handleChange();
        }).first().trigger('formInit');
    }

    /**
     * Checks all conditions and show or hide
     * the Form-Elements according to the check-result
     */
    handleChange(){
        this.resetObjects();
        this.logic.forEach((logic)=>{
            let target = $('[name="'+this.prefix + logic.name+'"]'),
                check = $('[name="'+this.prefix + logic.field+'"]');

            // Search for data-name if we are most likely looking for a wrapping item
            if (target.length === 0){
                target = $('[data-name="'+logic.name+'"');
            }

            if (check.length > 0) {
                // disjunctive normal form for the condition
                if (!logic.not && this.constructor.getValue(check) === logic.value || logic.not && this.constructor.getValue(check) !== logic.value){
                    this.stageObject(target, this.prefix + logic.name, true);
                }
                else{
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
        if(!this.targets[name]){
            this.targets[name] = {
                target: target,
                show: show
            }
        }
        else{
            if (this.targets[name].show){
                this.targets[name].show = show;
            }
        }
    }

    /**
     * Resolves the target states and shows/hides the Element depending on the logic-results
     */
    resolveObjects(){
        const className = 'js-core-target';
        for (let item in this.targets){
            let target = this.targets[item];
            let wrapper = target.target;
            if (!wrapper.hasClass(className)){
                wrapper = target.target.parents('.'+className).first();
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
        if (['checkbox','radio'].indexOf(check.attr('type')) >= 0){
            if (check.prop('checked')){
                return check.val();
            }
            else{
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

