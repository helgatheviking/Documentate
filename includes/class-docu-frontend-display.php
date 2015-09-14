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

if ( ! class_exists( 'Docu_Frontend_Display' ) ) :

/**
 * Documentate_Admin_Settings Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class Docu_Frontend_Display {
   /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
        add_action( 'body_class', array( $this, 'body_class' ) );

        remove_action( 'wp_head', array( $this, 'adjacent_posts_rel_link_wp_head' ) );
    }


    /**
     * Documentate Enqueue Documentate frontend scripts/styles
     * @since  1.0.0
     */ 
    public function frontend_scripts(){
        wp_enqueue_style( 'documentate-frontend', Docu()->plugin_url(). '/assets/css/documentate-frontend-style.css', array('dashicons'), Docu()->version );
        wp_register_script( 'live-search', Docu()->plugin_url().  '/assets/js/jquery.livesearch.js', array('jquery'), '2.0', true );
        wp_register_script( 'documentate', Docu()->plugin_url().  '/assets/js/documentate.js', array('jquery', 'live-search'), Docu()->version, true );
    }


    /**
     * Add layout classes to body
     * @since  1.0.0
     */ 
    public function body_class( $c ) {
        $display_sidebar = 0;

        if( is_documentate() ){
            $c[] = 'documentate';
        }
    
        return $c;
    }


    /**
     * Set Document Views
     * @since  1.0.0
     */
    function set_post_views($postID) {
        $count_key = 'documentate_post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        
        if($count=='' ){
            $count = 1;
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '1' );
        }else{
            $count++;
            update_post_meta($postID, $count_key, $count);
        }
    }


    /**
     * Remove Pre-Fetching to keep count accurate
     * @since  1.0.0
     */
    function get_post_views($postID){
        $count_key = 'documentate_post_views_count';
        $count = get_post_meta($postID, $count_key, true);
        
        if($count=='' ){
            delete_post_meta($postID, $count_key);
            add_post_meta($postID, $count_key, '1' );
            $count = 1;
        }

        //return sprintf( _n( '%s View', '%s Views', $count, 'documentate' ), $count );
        return $count;
        
    }

}

endif;

new Docu_Frontend_Display();