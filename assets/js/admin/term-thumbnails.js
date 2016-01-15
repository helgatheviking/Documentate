jQuery(document).ready(function($) {
	
	// Only show the "remove image" button when needed
	if ( ! jQuery( '#docu_cat_thumbnail_id' ).val() ) {
	    jQuery( '.remove_image_button' ).hide();
	}

	// Uploading files
	var file_frame;

	jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

	    event.preventDefault();

	    // If the media frame already exists, reopen it.
	    if ( file_frame ) {
	        file_frame.open();
	        return;
	    }

	    // Create the media frame.
	    file_frame = wp.media.frames.downloadable_file = wp.media({
	        title: DOCUMENTATE_MEDIA_PARAMS.title,
	        button: {
	            text: DOCUMENTATE_MEDIA_PARAMS.button
	        },
	        multiple: false
	    });

	    // When an image is selected, run a callback.
	    file_frame.on( 'select', function() {
	        var attachment = file_frame.state().get( 'selection' ).first().toJSON();

	        jQuery( '#docu_cat_thumbnail_id' ).val( attachment.id );
	        jQuery( '#docu_cat_thumbnail' ).find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
	        jQuery( '.remove_image_button' ).show();
	    });

	    // Finally, open the modal.
	    file_frame.open();
	});

	jQuery( document ).on( 'click', '.remove_image_button', function() {
	    jQuery( '#docu_cat_thumbnail' ).find( 'img' ).attr( 'src', DOCUMENTATE_MEDIA_PARAMS.placeholder );
	    jQuery( '#docu_cat_thumbnail_id' ).val( '' );
	    jQuery( '.remove_image_button' ).hide();
	    return false;
	});

});