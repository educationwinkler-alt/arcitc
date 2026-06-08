/**
 * Category
 */

jQuery(function ($) {

    const body = $("body");

    /**
     * Upload
     */
    body.on("click", ".js-term__media--upload, .js-term__image--upload", function (event) {
        event.preventDefault();

        const button = $(this);
        const field = button.closest("[data-term-media-field], [data-term-image-field]");
        const mediaType = field.data("termMediaType") === "video" ? "video" : "image";
        const mediaIDField = field.find(".js-term__image--id");
        const mediaID = mediaIDField.val();
        const mediaPreview = field.find(".js-term__image--preview");
        const mediaRemove = field.find(".js-term__media--remove, .js-term__image--remove");

        const customUploader = wp.media({
            title: mediaType === "video" ? parameter.video_modal_title : parameter.modal_window_title,
            button: {
                text: mediaType === "video" ? parameter.video_modal_button : parameter.modal_window_button
            },
            library: {
                type: mediaType
            },
            multiple: false
        }).on("select", function () {
            const attachment = customUploader.state().get("selection").first().toJSON();

            button.html(mediaType === "video" ? parameter.video_change : parameter.image_change);

            mediaIDField.val(attachment.id);
            if (mediaType === "video") {
                mediaPreview.html('<video src="' + attachment.url + '" controls muted preload="metadata"></video>');
            } else {
                mediaPreview.html('<img src="' + attachment.url + '" alt="">');
            }
            mediaRemove.css("display", "inline-block");
        });

        // already selected images
        customUploader.on("open", function () {

            let attachment;

            if (mediaID) {
                const selection = customUploader.state().get("selection");
                attachment = wp.media.attachment(mediaID);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            }

        });

        customUploader.open();

    });

    /**
     * Remove
     */
    body.on("click", ".js-term__media--remove, .js-term__image--remove", function (event) {
        event.preventDefault();

        const button = $(this);
        const field = button.closest("[data-term-media-field], [data-term-image-field]");
        const mediaType = field.data("termMediaType") === "video" ? "video" : "image";
        const mediaIDField = field.find(".js-term__image--id");
        const mediaUpload = field.find(".js-term__media--upload, .js-term__image--upload");
        const mediaPreview = field.find(".js-term__image--preview");
        const mediaRemove = field.find(".js-term__media--remove, .js-term__image--remove");

        mediaIDField.val("");
        mediaUpload.html(mediaType === "video" ? parameter.video_add : parameter.image_add);
        mediaPreview.html("");
        mediaRemove.css("display", "none");
    });
});
