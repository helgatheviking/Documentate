<?php
/**
 * Knowledgebase Admin
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
 * Documentate_Admin class.
 */
class Documentate_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// ajax callback for saving term order
		add_action( 'wp_ajax_documentate_term_ordering', array( $this, 'term_ordering' ) ) ;

		// clear category transients
		add_action( 'documentate_process_documentate_meta', array( $this, 'clear_transients' ) );

	}


	/**
	 * Include admin files
	 */
	public function includes() {
		include_once( 'class-docu-admin-post-type.php' );
		include_once( 'class-docu-admin-settings.php' );
	}

	/**
	 * Include admin files conditionally
	 */
	public function conditional_includes( $screen ) {
		if( 'edit-docu_cat' == $screen->id ){
			include_once( 'class-docu-admin-taxonomies.php' );
		} else if ( 'options-permalink'  == $screen->id ){
			include_once( 'class-docu-admin-permalink-settings.php' );
		}

	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Edit product category pages
		if ( in_array( $screen->id, array( 'edit-docu_cat' ) ) ) {
			wp_enqueue_media();
		}

		// Term ordering - only when sorting by term_order
		if ( ! empty( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], apply_filters( 'documentate_sortable_taxonomies', array( 'docu_cat' ) ) ) && ! isset( $_GET['orderby'] ) ) {

			wp_enqueue_script( 'documentate_term_ordering', Docu()->plugin_url() . '/assets/js/admin/term-ordering' . $suffix . '.js', array( 'jquery-ui-sortable' ), Docu()->version );

			$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';

			$documentate_term_order_params = array(
				'taxonomy' => $taxonomy
			);

			wp_localize_script( 'documentate_term_ordering', 'documentate_term_ordering_params', $documentate_term_order_params );
		}
	}

	/**
	 * Ajax request handling for categories ordering
	 */
	public function term_ordering() {

		// check permissions again and make sure we have what we need
		if ( ! current_user_can( 'edit_posts' ) || empty( $_POST['id'] ) ) {
			die('-1');
		}

		$id       = (int) $_POST['id'];
		$next_id  = isset( $_POST['nextid'] ) && (int) $_POST['nextid'] ? (int) $_POST['nextid'] : null;
		$taxonomy = isset( $_POST['thetaxonomy'] ) ? esc_attr( $_POST['thetaxonomy'] ) : null;
		$term     = get_term_by( 'id', $id, $taxonomy );

		if ( ! $id || ! $term || ! $taxonomy ) {
			die(0);
		}

		documentate_reorder_terms( $term, $next_id, $taxonomy );

		$children = get_terms( $taxonomy, "child_of=$id&menu_order=ASC&hide_empty=0" );

		if ( $term && sizeof( $children ) ) {
			echo 'children';
			die();
		}
	}


	/**
	 * Clear document transients
	 */
	public function clear_transients(){
		$docu_cats = get_terms( 'docu_cat' );
		if( $docu_cats ){
			foreach ( $docu_cats as $cat ){
				delete_transient('documentate_posts_in_' . $category->slug );
			}
		}
	}
}

return new Documentate_Admin();
