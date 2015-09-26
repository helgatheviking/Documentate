<?php
/**
 * Handles taxonomies in admin
 *
 * @class    Docu_Admin_Taxonomies
 * @version  1.0.0
 * @package  Documentate/Admin
 * @category Class
 * @author   helgatheviking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Docu_Admin_Taxonomies class.
 */
class Docu_Admin_Taxonomies {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Category/term ordering
		add_action( 'create_term', array( $this, 'create_term' ), 5, 3 );
		add_action( 'delete_term', array( $this, 'delete_term' ), 5 );

		// Add form
		add_action( 'docu_cat_add_form_fields', array( $this, 'add_category_fields' ) );
		add_action( 'docu_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );
		add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

		// Add columns
		add_filter( 'manage_edit-docu_cat_columns', array( $this, 'docu_cat_columns' ) );
		add_filter( 'manage_docu_cat_custom_column', array( $this, 'docu_cat_column' ), 10, 3 );

		// Taxonomy page descriptions
		add_action( 'docu_cat_pre_add_form', array( $this, 'docu_cat_description' ) );

		// Maintain hierarchy of terms
		add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );
	}

	/**
	 * Order term when created (put in position 0).
	 *
	 * @param mixed $term_id
	 * @param mixed $tt_id
	 * @param mixed $taxonomy
	 */
	public function create_term( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( 'docu_cat' != $taxonomy ) {
			return;
		}

		update_documentate_term_meta( $term_id, 'order', 0 );
	}

	/**
	 * When a term is deleted, delete its meta.
	 *
	 * @param mixed $term_id
	 */
	public function delete_term( $term_id ) {
		global $wpdb;

		$term_id = absint( $term_id );

		if ( $term_id ) {
			$wpdb->delete( $wpdb->documentate_termmeta, array( 'term_id' => $term_id ), array( '%d' ) );
		}
	}

	/**
	 * Category thumbnail fields.
	 */
	public function add_category_fields() {
		?>
		<div class="form-field">
			<label for="display_type"><?php _e( 'Display type', 'documentate' ); ?></label>
			<select id="display_type" name="display_type" class="postform">
				<option value=""><?php _e( 'Default', 'documentate' ); ?></option>
				<option value="documents"><?php _e( 'Documents', 'documentate' ); ?></option>
				<option value="subcategories"><?php _e( 'Subcategories', 'documentate' ); ?></option>
				<option value="both"><?php _e( 'Both', 'documentate' ); ?></option>
			</select>
		</div>
		<div class="form-field">
			<label><?php _e( 'Thumbnail', 'documentate' ); ?></label>
			<div id="docu_cat_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( documentate_placeholder_img_src() ); ?>" width="60px" height="60px" /></div>
			<div style="line-height: 60px;">
				<input type="hidden" id="docu_cat_thumbnail_id" name="docu_cat_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'documentate' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'documentate' ); ?></button>
			</div>
			<script type="text/javascript">

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
console.log(wp.media);
					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( "Choose an image", "documentate" ); ?>',
						button: {
							text: '<?php _e( "Use image", "documentate" ); ?>'
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
					jQuery( '#docu_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( documentate_placeholder_img_src() ); ?>' );
					jQuery( '#docu_cat_thumbnail_id' ).val( '' );
					jQuery( '.remove_image_button' ).hide();
					return false;
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public function edit_category_fields( $term ) {

		$display_type = get_documentate_term_meta( $term->term_id, 'display_type', true );
		$thumbnail_id = absint( get_documentate_term_meta( $term->term_id, 'thumbnail_id', true ) );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = documentate_placeholder_img_src();
		}
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Display type', 'documentate' ); ?></label></th>
			<td>
				<select id="display_type" name="display_type" class="postform">
					<option value="" <?php selected( '', $display_type ); ?>><?php _e( 'Default', 'documentate' ); ?></option>
					<option value="documents" <?php selected( 'documents', $display_type ); ?>><?php _e( 'Documents', 'documentate' ); ?></option>
					<option value="subcategories" <?php selected( 'subcategories', $display_type ); ?>><?php _e( 'Subcategories', 'documentate' ); ?></option>
					<option value="both" <?php selected( 'both', $display_type ); ?>><?php _e( 'Both', 'documentate' ); ?></option>
				</select>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'documentate' ); ?></label></th>
			<td>
				<div id="docu_cat_thumbnail" style="float: left; margin-right: 10px;"><img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="docu_cat_thumbnail_id" name="docu_cat_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'documentate' ); ?></button>
					<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'documentate' ); ?></button>
				</div>
				<script type="text/javascript">

					// Only show the "remove image" button when needed
					if ( '0' === jQuery( '#docu_cat_thumbnail_id' ).val() ) {
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
							title: '<?php _e( "Choose an image", "documentate" ); ?>',
							button: {
								text: '<?php _e( "Use image", "documentate" ); ?>'
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
						jQuery( '#docu_cat_thumbnail' ).find( 'img' ).attr( 'src', '<?php echo esc_js( documentate_placeholder_img_src() ); ?>' );
						jQuery( '#docu_cat_thumbnail_id' ).val( '' );
						jQuery( '.remove_image_button' ).hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * save_category_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 */
	public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( isset( $_POST['display_type'] ) && 'docu_cat' === $taxonomy ) {
			update_documentate_term_meta( $term_id, 'display_type', esc_attr( $_POST['display_type'] ) );
		}
		if ( isset( $_POST['docu_cat_thumbnail_id'] ) && 'docu_cat' === $taxonomy ) {
			update_documentate_term_meta( $term_id, 'thumbnail_id', absint( $_POST['docu_cat_thumbnail_id'] ) );
		}
	}

	/**
	 * Description for docu_cat page to aid users.
	 */
	public function docu_cat_description() {
		echo wpautop( __( 'Document categories can be managed here. To change the order of categories on the front-end you can drag and drop to sort them. To see more categories listed click the "screen options" link at the top of the page.', 'documentate' ) );
	}


	/**
	 * Thumbnail column added to category admin.
	 *
	 * @param mixed $columns
	 * @return array
	 */
	public function docu_cat_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['thumb'] = __( 'Image', 'documentate' );

		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function docu_cat_column( $columns, $column, $id ) {

		if ( 'thumb' == $column ) {

			$thumbnail_id = absint( get_documentate_term_meta( $id, 'thumbnail_id', true ) );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = documentate_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'documentate' ) . '" class="wp-post-image" height="48" width="48" />';

		}

		return $columns;
	}

	/**
	 * Maintain term hierarchy when editing a document.
	 *
	 * @param  array $args
	 * @return array
	 */
	public function disable_checked_ontop( $args ) {

		if ( 'docu_cat' == $args['taxonomy'] ) {
			$args['checked_ontop'] = false;
		}

		return $args;
	}

}

new Docu_Admin_Taxonomies();
