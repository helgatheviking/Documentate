<?php
/**
 * Installation related functions and actions.
 *
 * @author   helgatheviking
 * @category Admin
 * @package  Documentate/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Docu_Install Class
 */
class Docu_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'wpmu_new_blog', array( __CLASS__, 'activate_sitewide_plugins' ) );
		add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
	}


	/**
	 * Install Documentate
	 */
	public static function install() {
		global $wpdb;

		self::create_option();
		self::create_table();

		// Register post types
		Docu_Post_Type::register_post_type();
		Docu_Post_Type::register_category_taxonomy();
		Docu_Post_Type::register_tag_taxonomy();

		// Flush rules after install
		flush_rewrite_rules();

		self::update_version();

		// Trigger action
		do_action( 'documentate_installed' );
	}


    /**
     * Trigger activation on new blogs if network activated
     * @since 1.0.0
     */
    function activate_sitewide_plugins( $blog_id ){
        // Switch to new website
        switch_to_blog( $blog_id );
     
        // Activate
        self::install();
     
        // Restore current website
        restore_current_blog();
    }


	/**
	 * Update version to current
	 */
	private static function update_version() {
		delete_option( 'documentate_version' );
		add_option( 'documentate_version', Docu()->version );
	}


	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 */
	private static function create_option() {
		// set default settings
        $settings = get_option( 'documentate_settings', array() );

        if( empty( $settings ) ){
            $settings = array( 
                'archive_page_id' => 0,
            );
        }

        // create the archive page
        $archive_page_id = isset( $settings['archive_page_id'] ) ? $settings['archive_page_id'] : 0;

        // check to see if has settings page and it exists
        if ( $archive_page_id > 0 && ( $page_object = get_post( $archive_page_id ) ) ) {
            if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ){
                return; // found the page and it is published so we're good
            } 
        }

        $settings['archive_page_id'] = self::create_page();
        update_option( 'documentate_settings', $settings );
	}


	/**
	 * Create page that the plugin relies on, storing page id's in variables.
	 */
	public static function create_page() {

        $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => get_current_user_id(),
                'post_name'      => _x( 'documents', 'default slug', 'documentate' ),
                'post_title'     => __( 'Documents', 'documentate' ),
                'post_content'   => '',
                'comment_status' => 'closed',
                'ping_status'           =>  'closed',
            );
        return wp_insert_post( apply_filters( 'documentate_create_page', $page_data ) );

	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *		documentate_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
	 */
	private static function create_table() {
		global $wpdb;

		//$wpdb->hide_errors();

		$collate = $wpdb->get_charset_collate();

		$sql = "
CREATE TABLE {$wpdb->prefix}documentate_termmeta (
  meta_id bigint(20) NOT NULL auto_increment,
  documentate_term_id bigint(20) NOT NULL,
  meta_key varchar(255) NULL,
  meta_value longtext NULL,
  PRIMARY KEY  (meta_id),
  KEY documentate_term_id (documentate_term_id),
  KEY meta_key (meta_key)
) $collate;
		";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}


	/**
	 * Uninstall tables when MU blog is deleted.
	 * @param  array $tables
	 * @return array
	 */
	public static function wpmu_drop_tables( $tables ) {
		global $wpdb;
		$tables[] = $wpdb->prefix . 'documentate_termmeta';
		return $tables;
	}


}

Docu_Install::init();
