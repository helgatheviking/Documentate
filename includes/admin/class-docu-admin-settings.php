<?php
/**
 * Knowledgebase Settings
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Documentate_Admin_Settings' ) ) :

/**
 * Documentate_Admin_Settings Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class Documentate_Admin_Settings {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );  
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'current_screen', array( $this, 'settings_scripts' ) );
	}


	/**
	 * Add plugin menus
	 * @since  1.0.0
	 */
	public function plugin_menu() {
	    add_submenu_page( 'edit.php?post_type=document', __( 'Settings', 'documentate' ), __( 'Settings', 'documentate' ), 'manage_options', 'documentate_options', array( $this, 'options_callback' ) );
	}

	/**
	 * Register plugin settings
	 * @since  1.0.0
	 */
	public function register_settings() {
		register_setting( 'documentate_settings', 'documentate_settings', array( $this, 'validate_settings' ) );
	}

	/**
	 * Conditionally enqueue admin scripts/styles
	 * @since  1.0.0
	 */
	public function settings_scripts( $screen ){
	    if( $screen->id == 'document_page_documentate_options' ){
	    	wp_enqueue_style( 'documentate_admin_css', Docu()->plugin_url() . '/assets/css/documentate-admin-style.css', array(), Docu()->version );
	    } 
	}


	/**
	 * Callback for main plugin settings
	 * @since  1.0.0
	 */
	public function options_callback(){
	    require "views/html-plugin-settings.php";
	}


	/**
	 * Sanitize and validate plugin settings
	 * @param  array $input
	 * @return array
	 * @since  1.0.0
	 */
	public function validate_settings( $input ) {
	 
		$clean = array(); 

		$clean['archive_page_id'] = isset( $input['archive_page_id'] ) && ! is_wp_error( get_post( intval( $input['archive_page_id'] ) ) ) ? intval( $input['archive_page_id'] ) : 0;

		$clean['archive_display'] =  isset( $input['archive_display'] ) && in_array( $input['archive_display'], array( 'documents', 'subcategories', 'both' ) ) ? $input['archive_display'] : 'subcategories' ;  

		$clean['docu_qty'] = isset( $input['archive_display'] ) ? intval( $input['docu_qty'] ) : 0;

		$clean['category_display'] =  isset( $input['category_display'] ) && in_array( $input['category_display'], array( 'documents', 'subcategories', 'both' ) ) ? $input['category_display'] : 'subcategories' ;  

		$clean['search_setting'] =  isset( $input['search_setting'] ) && $input['search_setting'] == 'show' ? 'show' : 'hide' ;  
		
		$clean['breadcrumbs_setting'] =  isset( $input['breadcrumbs_setting'] ) && $input['breadcrumbs_setting'] == 'show' ? 'show' : 'hide' ;  

		$radio = array( 'left', 'right', 'hide' );

		$clean['sidebar'] = isset( $input['sidebar'] ) && $input['sidebar'] == 'show' ? 'show' : 'hide' ;  
		
		$clean['comments_setting'] =  isset( $input['comments_setting'] ) && $input['comments_setting'] == 'show' ? 'show' : 'hide' ;  

		$clean['uninstall_mode'] = isset( $input['uninstall_mode'] ) && in_array( $input['uninstall_mode'], array( 'nuclear', 'settings', 'none' ) ) ? $input['uninstall_mode'] : 'hide';

		// maybe clear category transients
		$docu_qty = max( array( documentate_get_option( 'docu_qty' ), documentate_get_option( 'cat_docu_qty' ) ) );
		if( ( isset( $clean['docu_qty'] ) && $clean['docu_qty'] > $docu_qty ) || ( isset( $clean['cat_docu_qty'] ) && $clean['cat_docu_qty'] > $docu_qty ) ){
			Docu()->admin->clear_transients();		
		}
		
		return $clean;
		
	}

}

endif;

new Documentate_Admin_Settings();