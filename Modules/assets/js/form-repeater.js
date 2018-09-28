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