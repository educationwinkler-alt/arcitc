/**
 * Category
 */

jQuery(function ($) {

    const body = $("body");

    /**
     * Upload
     */
    body.on("click", ".js-term__image--upload", function (event) {
        event.preventDefault();

        const button = $(this)
        const imageIDField = $(".js-term__image--id");
        const imageID = imageIDField.val();
        const imagePreview = $(".js-term__image--preview");
        const imageRemove = $(".js-term__image--remove");

        const customUploader = wp.media({
            title: parameter.modal_window_title,
            button: {
                text: parameter.modal_window_button
            },
            library: {
                type: "image"
            },
            multiple: false
        }).on("select", function () {
            const attachment = customUploader.state().get("selection").first().toJSON();

            button.html(parameter.image_change);

            imageIDField.val(attachment.id);
            imagePreview.html('<img src="' + attachment.url + '" alt="">');
            imageRemove.css("display", "inline-block");
        });

        // already selected images
        customUploader.on("open", function () {

            let attachment;

            if (imageID) {
                const selection = customUploader.state().get("selection")
                attachment = wp.media.attachment(imageID);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            }

        });

        customUploader.open()

    });

    /**
     * Remove
     */
    body.on("click", ".js-term__image--remove", function (event) {
        event.preventDefault();

        const imageIDField = $(".js-term__image--id");
        const imageUpload = $(".js-term__image--upload");
        const imagePreview = $(".js-term__image--preview");
        const imageRemove = $(".js-term__image--remove");

        imageIDField.val("");
        imageUpload.html(parameter.image_add);
        imagePreview.html("");
        imageRemove.css("display", "none");
    });
});