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
		add_action( 'admin_init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_filter( 'plugin_action_links_' . DOCU_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );

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
		include_once( 'class-docu-admin-taxonomies.php' );
	}

	/**
	 * Include admin files conditionally
	 */
	public function conditional_includes( $screen ) {
		if ( 'options-permalink'  == $screen->id ){
			include_once( 'class-docu-admin-permalink-settings.php' );
		}
	}


	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'edit.php?post_type=document&page=documentate_options' ) . '" title="' . esc_attr( __( 'View Documentate Settings', 'documentate' ) ) . '">' . __( 'Settings', 'documentate' ) . '</a>',
		);

		return array_merge( $action_links, $links );
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
