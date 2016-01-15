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

		// enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add form
		add_action( 'docu_cat_add_form_fields', array( $this, 'add_category_form_fields' ) );
		add_action( 'docu_cat_edit_form_fields', array( $this, 'edit_category_form_fields' ), 10 );

		add_action( 'created_docu_cat', array( $this, 'save_category_fields' ), 10, 2 );
		add_action( 'edited_docu_cat', array( $this, 'edit_category_fields' ), 10, 2 );

		// Add columns
		add_filter( 'manage_edit-docu_cat_columns', array( $this, 'docu_cat_columns' ) );
		add_filter( 'manage_docu_cat_custom_column', array( $this, 'docu_cat_column' ), 10, 3 );

		// Taxonomy page descriptions
		add_action( 'docu_cat_pre_add_form', array( $this, 'docu_cat_description' ) );

		// Maintain hierarchy of terms
		add_filter( 'wp_terms_checklist_args', array( $this, 'disable_checked_ontop' ) );
	}


	/**
	 * Enqueue admin scripts
	 *
	 * @param	str $hook
	 * @return	array
	 */
	public function enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Edit document category pages
		if ( 'edit-tags.php' == $hook && in_array( $screen->id, array( 'edit-docu_cat' ) ) ) {
			wp_enqueue_media();

			$docu_media_params = array( 
				'title' => __( "Choose an image", "documentate" ),
				'button' => __( "Use image", "documentate" ),
				'placeholder' => documentate_placeholder_img_src() );

			wp_enqueue_script( 'documentate_term_thunbnails', Docu()->plugin_url() . '/assets/js/admin/term-thumbnails' . $suffix . '.js', array( 'jquery', 'media-editor' ), Docu()->version, true );
			wp_localize_script( 'documentate_term_thunbnails', 'DOCUMENTATE_MEDIA_PARAMS', $docu_media_params );
		}

		// Term ordering - only when sorting by term_order
		if ( ! empty( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], apply_filters( 'documentate_sortable_taxonomies', array( 'docu_cat' ) ) ) && ! isset( $_GET['orderby'] ) ) {

			wp_enqueue_script( 'documentate_term_ordering', Docu()->plugin_url() . '/assets/js/admin/term-ordering' . $suffix . '.js', array( 'jquery-ui-sortable' ), Docu()->version, true );

			$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';

			$docu_term_order_params = array(
				'taxonomy' => $taxonomy
			);

			wp_localize_script( 'documentate_term_ordering', 'DOCUMENTATE_TERM_ORDER_PARAMS', $docu_term_order_params );
		}

	}


	/**
	 * Category thumbnail fields.
	 */
	public function add_category_form_fields() {
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
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit category thumbnail field.
	 *
	 * @param mixed $term Term (category) being edited
	 */
	public function edit_category_form_fields( $term ) {

		$display_type = get_term_meta( $term->term_id, 'display_type', true );
		$thumbnail_id = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );

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
	public function save_category_fields( $term_id, $tt_id = '' ) {

		update_term_meta( $term_id, 'order', 0 );

	    if ( isset( $_POST['display_type'] ) && '' !== $_POST['display_type'] ) {
	        $type = in_array( $_POST['display_type'], array( 'documents', 'subcategory', 'both' ) ) ? $_POST['display_type'] : '';
	        add_term_meta( $term_id, 'display_type', $type );
	    }
	    if ( isset( $_POST['docu_cat_thumbnail_id'] ) && absint( $_POST['docu_cat_thumbnail_id'] ) > 0 ) {
	        add_term_meta( $term_id, 'thumbnail_id', absint( $_POST['docu_cat_thumbnail_id'] ) );
	    }
	}

	/**
	 * edit_category_fields function.
	 *
	 * @param mixed $term_id Term ID being saved
	 */
	public function edit_category_fields( $term_id, $tt_id = '' ) {

	    if ( isset( $_POST['display_type'] ) && '' !== $_POST['display_type'] ) {
	        $type = in_array( $_POST['display_type'], array( 'documents', 'subcategory', 'both' ) ) ? $_POST['display_type'] : '';
	        update_term_meta( $term_id, 'display_type', $type );
	    }
	    if ( isset( $_POST['docu_cat_thumbnail_id'] ) && absint( $_POST['docu_cat_thumbnail_id'] ) > 0 ) {
	        update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['docu_cat_thumbnail_id'] ) );
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

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $term_id
	 * @return array
	 */
	public function docu_cat_column( $content, $column, $term_id ) {

		if( $column == 'thumb' ){

			$thumbnail_id = absint( get_term_meta( $term_id, 'thumbnail_id', true ) );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = documentate_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			$content .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'documentate' ) . '" class="wp-post-image" height="48" width="48" />';

		}

		return $content;
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
