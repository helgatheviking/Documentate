<?php
/**
 * Plugin Name: Documentate
 * Plugin URI: http://wordpress.org/plugins/documentate
 * Description: Create wikis and Knowledgebases
 * Author: helgatheviking
 * Author URI: http://kathyisawesome.com
 * Version: 0.1-beta
 * Requires at least: 4.4.0
 * Tested up to: 4.4.1
 *
 * Text Domain: documentate
 * Domain Path: /languages/
 *
 * @package Documentate
 * @category Core
 * @author helgatheviking
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Documentate' ) ) :

/**
 * Main Documentate Class
 *
 * @class Documentate
 * @version 2.4.0
 */
final class Documentate {

    /**
     * @var string
     */
    public $version = '0.1-beta';

    /**
     * @var Documentate The single instance of the class
     * @since 2.1
     */
    protected static $_instance = null;

    /**
     * Main Documentate Instance
     *
     * Ensures only one instance of Documentate is loaded or can be loaded.
     *
     * @since 2.1
     * @static
     * @see Docu()
     * @return Documentate - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Cloning is forbidden.
     * @since 2.1
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'documentate' ), '2.1' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     * @since 2.1
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'documentate' ), '2.1' );
    }


    /**
     * Documentate Constructor.
     */
    public function __construct() {
 
        $this->define_constants();
        $this->includes();

        register_activation_hook( __FILE__, array( 'Docu_Install', 'install' ) );

        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
        add_action( 'after_theme_setup', array( $this, 'add_thumbnail_support' ) );
        add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );

    }

    /**
     * Define Constants
     */
    private function define_constants() {
        if ( ! defined( 'DOCU_PLUGIN_FILE' ) ) {
            define( 'DOCU_PLUGIN_FILE', __FILE__ );
        }
        if ( ! defined( 'DOCU_PLUGIN_BASENAME' ) ) {
            define( 'DOCU_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        }
        if ( ! defined( 'DOCU_VERSION' ) ) {
            define( 'DOCU_VERSION', $this->version );
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {

        include_once( 'includes/class-docu-post-type.php' );   //  install tables, etc
        include_once( 'includes/class-docu-install.php' );   //  Post type and taxonomies
        include_once( 'includes/docu-core-functions.php' );   //  Core functions
        include_once( 'includes/docu-term-functions.php' );  // term functions

        if ( is_admin() ) {
            $this->admin = include_once( 'includes/admin/class-docu-admin.php' );            
        } else {
            $this->frontend_includes();
        }

    }

    /**
     * Include required frontend files.
     */
    public function frontend_includes() {
               
        $this->display = include_once( "includes/class-docu-frontend-display.php" ); //  Front end display functions
        
        include_once( 'includes/docu-template-hooks.php' ); // Template hooks
        include_once( 'includes/docu-conditional-functions.php' ); // conditional functions
        include_once( 'includes/class-docu-breadcrumbs.php' ); // breadcrumbs class

        //  Widgets
        include_once( 'widgets/widget-category.php' );
        include_once( 'widgets/widget-document.php' );
        include_once( 'widgets/widget-search.php' );
        include_once( 'widgets/widget-tags.php' );
    }

    /**
     * Load Localisation files.
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Frontend/global Locales found in:
     *      - WP_LANG_DIR/documentate/documentate-LOCALE.mo
     *      - documentate/i18n/languages/documentate-LOCALE.mo (which if not found falls back to:)
     *      - WP_LANG_DIR/plugins/documentate-LOCALE.mo
     */
    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'documentate' );
        load_textdomain( 'documentate', WP_LANG_DIR . '/documentate/documentate-' . $locale . '.mo' );
        load_plugin_textdomain( 'documentate', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
    }

    /**
     * Ensure post thumbnail support is turned on.
     * 
     */
    private function add_thumbnail_support() {
        if ( ! current_theme_supports( 'post-thumbnails' ) ) {
            add_theme_support( 'post-thumbnails' );
        }
        add_post_type_support( 'document', 'thumbnail' );
    }

    /**
     * Function used to Init Documentate Template Functions - This makes them pluggable by plugins and themes.
     */
    public function include_template_functions() {
        include_once( 'includes/docu-template-functions.php' ); //  Template functions
    }

    /******************
     * HELPER FUNCTIONS
     ******************/

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }


    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get the template path.
     * @return string
     */
    public function template_path() {
        return apply_filters( 'documentate_template_path', 'documentate/' );
    }


}

endif;

/**
 * Returns the main instance of Documentate to prevent the need to use globals.
 *
 * @since  2.1
 * @return Documentate
 */
function Docu() {
    return Documentate::instance();
}

// Launch the plugin
Docu();