/**
 * Handles the core-link panel
 * Opens the wpLink Panel and renders the field
 */
jQuery(function ($){

    const doc = $(document);

    /**
     * Hooks the wpLink panel to the buttons
     */
    doc.on('click', '.core-link__button', function (event){
        event.preventDefault();
        const wrapper = $(this).parent('.core-link__container');
        const area = wrapper.find('.core-link__area');
        area.attr('id', Math.floor((Math.random() * Math.pow(10, 16))));
        area.val('');
        wpLink.open(area.attr('id'));
    });

    /**
     * Clears the link-data by click on the delete button
     */
    doc.on('click', '.core-link__clear', function (event){
        event.preventDefault();
        const current = $(this);
        const fields = current.parent('.core-link__display').siblings('.core-link__fields').find('input');
        current.siblings().remove();
        fields.val('');

    });

    /**
     * Enters the link-data into the group-fields
     * and showing the current link in a nice format
     */
    doc.on('change', '.core-link__area', function (event){
        const current = $(this);
        const link = $(current.val());
        const display = current.siblings('.core-link__display');
        const fields = current.siblings('.core-link__fields');

        const data = {
            link: link.attr('href'),
            rel: link.attr('rel'),
            target: (link.attr('target')) ? link.attr('target') : '_self',
            title: (link.text()) ? link.text() : link.attr('href')
        };

        for (let key in data) {
            fields.find('[data-link-part="' + key + '"]').val(data[key]);
        }

        display.find('.core-link__link').remove();
        display.prepend('<div class="core-link__link">' + '<strong>' + data.title + '</strong> <small>' + data.target + '</small><br/> ' + data.link + '</div>');
    });
});