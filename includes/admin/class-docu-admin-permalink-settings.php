<?php
/**
 * Knowledgebase Admin Permalinks
 *
 * @version     0.1-beta
 * @author      helgatheviking
 * @category    Admin
 * @package     Documentate/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Documentate_Admin_Permalink_Settings class.
 */
class Documentate_Admin_Permalink_Settings {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->add_settings();
		$this->validate_permalinks();
	}


	/**
	 * Register plugin settings
	 * @since  1.0.0
	 */
	public function add_settings() {

		// Add a section to the permalinks page
		add_settings_section( 'documentate-permalink', __( 'Document permalink base', 'documentate' ), array( $this, 'permalink_display_settings' ), 'permalink' );

		// Add our permalink settings
		add_settings_field(
			'documentate_document_category_slug',            // id
			__( 'Document category base', 'documentate' ),   // setting title
			array( $this, 'document_category_slug_input' ),  // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);
		add_settings_field(
			'documentate_document_tag_slug',                 // id
			__( 'Document tag base', 'documentate' ),        // setting title
			array( $this, 'document_tag_slug_input' ),       // display callback
			'permalink',                                    // settings page
			'optional'                                      // settings section
		);

	}


	/**
	 * Show a slug input box.
	 * @since  1.0.0
	 */
	public function document_category_slug_input() {  ?>
	    <input name="documentate_permalinks[category_base]" type="text" class="regular-text code" value="<?php echo esc_attr( documentate_get_option( 'category_base' ) ); ?>" placeholder="<?php echo esc_attr_x( 'docu_cat', 'slug', 'documentate' ) ?>" />
	    <?php
	}


	/**
	 * Show a slug input box.
	 * @since  1.0.0
	 */
	public function document_tag_slug_input() { ?>
	    <input name="documentate_permalinks[tag_base]" type="text" class="regular-text code" value="<?php echo esc_attr( documentate_get_option( 'tag_base' ) ); ?>" placeholder="<?php echo esc_attr_x( 'docu_tag', 'slug', 'documentate' ) ?>" />
	    <?php
	}


	/**
	 * Show the settings
	 * @since  1.0.0
	 */
	public function permalink_display_settings() {
		include_once( 'views/html-permalinks-settings.php' );
	}


	/**
	 * Save the permalinks
	 * @since  1.0.0
	 */
	public function validate_permalinks(){

		// @todo check permissions

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page
		if ( isset( $_POST['documentate_permalinks'] ) ) {

			$input = $_POST['documentate_permalinks'];

			$permalinks = array();

			$permalinks['category_base'] = isset( $input['category_base'] ) ? untrailingslashit( sanitize_text_field( $input['category_base'] ) ) : '';
			$permalinks['tag_base'] = isset( $input['tag_base'] ) ? untrailingslashit( sanitize_text_field( $input['tag_base'] ) ) : '';

			// document base
			$document_permalink = isset( $input['document_permalink'] ) ? untrailingslashit( sanitize_text_field( $input['document_permalink'] ) ) : '';

			if ( $document_permalink == 'custom' ) {
				// Get permalink without slashes
				$document_permalink = trim( sanitize_text_field( $input['document_permalink_structure'] ), '/' );

				// This is an invalid base structure and breaks pages
				if ( '%docu_cat%' == $document_permalink ) {
					$document_permalink = _x( 'document', 'slug', 'documentate' ) . '/' . $document_permalink;
				}

				// Prepending slash
				$document_permalink = '/' . $document_permalink;
			} elseif ( empty( $document_permalink ) ) {
				$document_permalink = false;
			}

			$permalinks['document_base'] = untrailingslashit( $document_permalink );

			// Shop base may require verbose page rules if nesting pages
			$archive_page_id = documentate_get_option( 'archive_page_id' );
			$archive_permalink = ( $archive_page_id > 0 && get_post( $archive_page_id ) ) ? get_page_uri( $archive_page_id ) : _x( 'documents', 'default-slug', 'documentate' );

			if ( $archive_page_id && trim( $permalinks['document_base'], '/' ) === $archive_permalink ) {
				$permalinks['use_verbose_page_rules'] = true;
			}

			update_option( 'documentate_permalinks', $permalinks );

		}

	}
}

new Documentate_Admin_Permalink_Settings();
