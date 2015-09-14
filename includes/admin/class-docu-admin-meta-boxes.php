<?php
/**
 * Knowledgebase Meta Boxes
 *
 * Sets up the write panels used by documents and orders (custom post types)
 *
 * @author      helgatheviking
 * @category    Admin
 * @package     Documentate/Admin/Meta Boxes
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Documentate_Admin_Meta_Boxes
 */
class Documentate_Admin_Meta_Boxes {

	private static $saved_meta_boxes = false;
	public static $meta_box_errors  = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// clear category transients
		add_action( 'documentate_process_documentate_meta', array( Docu()->admin, 'clear_transients' ) );
	}

	/**
	 * Add an error message
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option
	 */
	public function save_errors() {
		update_option( 'documentate_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'documentate_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="documentate_errors" class="error">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'documentate_meta_box_errors' );
		}
	}

	/**
	 * Add Meta boxes
	 */
	public function add_meta_boxes() {
		add_meta_box( 'postexcerpt', __( 'Document Short Description', 'documentate' ), array( $this, 'short_description_metabox_output' ), 'document', 'normal' );
	}

	public function short_description_metabox_output( $post ){
		$settings = array(
			'textarea_name' => 'excerpt',
			'media_buttons' => false,
			'quicktags'     => array( 'buttons' => 'em,strong,link' ),
			'tinymce'       => array(
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			),
			'editor_css'    => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

		wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', apply_filters( 'documentate_short_description_editor_settings', $settings ) );
	}


	/**
	 * Remove bloat
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'document', 'normal' );
		remove_meta_box( 'pageparentdiv', 'document', 'side' );
	}


	/**
	 * Check if we're saving, the trigger an action based on the post type
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['documentate_meta_nonce'] ) || ! wp_verify_nonce( $_POST['documentate_meta_nonce'], 'documentate_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// save our data
		if ( $post->post_type == 'document' ) {
			do_action( 'documentate_process_documentate_meta', $post_id, $post );
		}
	}

}

new Documentate_Admin_Meta_Boxes();
