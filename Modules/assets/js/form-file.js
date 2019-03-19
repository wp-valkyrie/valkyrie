/**
 * Handles the core-file Upload Button
 * Opens the wp-media Panel and allows  traditional wp uploading
 */
jQuery(function ($){

    let body = $('body');

    /**
     * Handles the ADD Upload file
     * opens the wp-media Panel
     */
    body.on('click', '.core-file__button', function (event){
        event.preventDefault();

        let button = $(this);

        // Attach media file to post if field requires it
        const fileField = button.parents('.core-file');
        if (parseInt(fileField.data('attach')) === 1){
            const id = jQuery('#post_ID').val();
            if (id){
                wp.media.model.settings.post.id = id;
            }
        }
        /**
         * Media Options
         * @see https://codex.wordpress.org/Javascript_Reference/wp.media
         */
        let loader = wp.media({
            title: button.attr('data-title'),
            button: {
                text: button.attr('data-button')
            },
            library: {
                type: button.attr('data-types').trim().split(' ')
            },
            multiple: button.attr('data-multiple') === 'true'
        }).on('select', function (){
            const wrapper = button.parent();
            let attachment = loader.state().get('selection').first().toJSON();
            button.removeClass('button').html(renderType(attachment));
            console.log(attachment);
            wrapper.find('.core-file__title').text(attachment.filename);
            wrapper.find('.core-file__input').val(attachment.id);
            wrapper.find('.core-file__remove').show();
        }).open();
    });

    /**
     * Handles the remove Link
     * resets the Upload-Field to initial state
     */
    body.on('click', '.core-file__remove', function (event){
        event.preventDefault();
        let remove = $(this),
            button = remove.parent().siblings('.core-file__button');

        remove.hide();
        remove.siblings('.core-file__title').text('');
        remove.parent().siblings('.core-file__input').val('');
        button.addClass('button').html(atob(button.attr('data-text')));
    });

    /**
     * Renders the Attachment image depending on its type
     * @param {object} attachment The attachment object
     * @returns {string} The <img tag for the given attachment
     */
    function renderType(attachment){
        if (attachment.type === 'image') {
            if (attachment.sizes) {
                if (attachment.sizes.thumbnail) {
                    return '<img src="' + attachment.sizes.thumbnail.url + '" />';
                }
                return '<img src="' + attachment.sizes.full.url + '" />';
            }
            return '<img src="' + attachment.icon + '" />';
        } else {
            return '<img src="' + attachment.icon + '" />';
        }
    }
});