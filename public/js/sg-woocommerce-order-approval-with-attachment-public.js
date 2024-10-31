(function($) {
    'use strict';
   
    

    jQuery(document).ready(function() {
        jQuery('#sg_order_attachments').on('change', get_images);
        jQuery('#sg_order_attachments').on('dragenter', (e) => {
            jQuery(e.target).parent().addClass('is-active');
        });
        jQuery('#sg_order_attachments').on('drop', (e) => {
            jQuery(e.target).parent().removeClass('is-active');
        });
        jQuery(document).on('click', 'p.close-btn', sg_attachment_remove);
    });

    function get_images(event) {
        let all_files = event.target.files;
        for (var i = 0; i < all_files.length; i++) {
            // create image container
            if (sg_attach_vars.sg_allowed_files.includes(all_files[i].type)) {

                var preview_container = jQuery('<div></div>').attr({
                    class: 'sg-thumb-container thumb-img-item'
                }).prependTo(jQuery('.sg-attachment-img-previewer'));
                // create image for preview
                jQuery('<img>').attr({
                    class: 'img-preview-thumb',
                    src: URL.createObjectURL(all_files[i])
                }).prependTo(preview_container);

                // create image close button
                jQuery('<p>+</p>').attr({
                    class: 'close-btn',
                    id: 'sg_attachment_remove_img_' + i
                }).prependTo(preview_container);
                jQuery('<input>').attr({
                    type: 'hidden',
                    class: 'image-save',
                    name: "sg_order_attachments_selected[]",
                    id: "sg_attachment_" + i,
                    value: all_files[i].name
                }).prependTo(preview_container);
            } else {
                alert('Unsupported type of file');
                return;
            }
        }
        let images = [];
        for (const er of jQuery('.sg-attachment-img-previewer input')) {
            images.push(er.value);
        }
        store_images(images);
    }

    function sg_attachment_remove(event) {
        jQuery(event.target).parent().remove();
        let images = [];
        for (const er of jQuery('.sg-attachment-img-previewer input')) {
            images.push(er.value);
        }
        store_images(images);
    }

    function store_images(selected_imgs) {
        let form_data = new FormData();
        form_data.append("action", 'sg_attachment_upload');
        form_data.append("nonce_data", $('#sg_nonce').data('nonce'));
        for (const img of selected_imgs) {
            form_data.append("selected_imgs[]", img);

        }
        for (const files of document.getElementById('sg_order_attachments').files) {
            form_data.append("sg_attachments[]", files);
        }

        jQuery.ajax({
            type: "post",
            url: document.getElementById('sg_attachment_ajax_caller').value,
            data: form_data,
            dataType: 'json', // what to expect back from the PHP script
            cache: false,
            contentType: false,
            processData: false,

            success: function(response) {

            }
        });
    }
})(jQuery);