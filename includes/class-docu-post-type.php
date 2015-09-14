<?php
/**
 * Custom Post Type and Custom Taxonomies
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Docu_Post_Type' ) ) :

    /**
     * Docu_Post_Type Class
     *
     * Handles the edit posts views and some functionality on the edit post screen for WC post types.
     */
    class Docu_Post_Type {
        /**
         * Pseudo Constructor
         */
        public static function init() {
            add_action( 'init', array( __CLASS__, 'register_post_type' ), 10 );
            add_action( 'init', array( __CLASS__, 'register_category_taxonomy' ), 20 );
            add_action( 'init', array( __CLASS__, 'register_tag_taxonomy' ), 30 );
            add_filter( 'pre_get_posts', array( __CLASS__, 'fix_wp_tax_archives' ) );
        }

    /**
     * Register Post Type
     * @since  1.0.0
     */
    public static function register_post_type() {

        $archive_page_id = documentate_get_option( 'archive_page_id' );
        
        $document_permalink = documentate_get_option( 'document_base' );

        $labels = array(
            'name'                  =>  __( 'Documents', 'documentate' ),
            'singular_name'         =>  __( 'Document', 'documentate' ),
            'all_items'             =>  __( 'Documents', 'documentate' ),
            'add_new'               =>  __( 'New Document', 'documentate' ),
            'add_new_item'          =>  __( 'Add New Document', 'documentate' ),
            'edit_item'             =>  __( 'Edit Document', 'documentate' ),
            'new_item'              =>  __( 'New Document', 'documentate' ),
            'view_item'             =>  __( 'View Document', 'documentate' ),
            'search_items'          =>  __( 'Search Documents', 'documentate' ),
            'not_found'             =>  __( 'Nothing found', 'documentate' ),
            'not_found_in_trash'    =>  __( 'Nothing found in Trash', 'documentate' ),
            'parent_item_colon'     =>  ''
        );
        
        $args = apply_filters( 'documentate_post_type_args', array(
            'labels'                =>  $labels,
            'description'         => __( 'This is where you can add new documents to your knowledgebase.', 'documentate' ),
            'public'                =>  true,
            'publicly_queryable'    =>  true,
            'show_ui'               =>  true,
            'query_var'             =>  true,
            'menu_icon'             =>  'dashicons-book-alt',
            'capability_type'       =>  'post',
            'hierarchical'          =>  false,
            'supports'              =>  array( 'title','editor','thumbnail','tags','revisions', 'excerpt' ),
            'rewrite'               =>  $document_permalink ? array( 'slug' => untrailingslashit( $document_permalink ), 'with_front' => false, 'feeds' => true ) : false,
            'show_in_menu'          =>  true,
            'show_in_nav_menus'     =>  true,
            'show_in_admin_bar'     =>  true,
            'can_export'            =>  true,
            'has_archive'           =>  $archive_page_id && get_post( $archive_page_id ) ? get_page_uri( $archive_page_id ) : false,
            'exclude_from_search'   =>  true, // WP stupidly kills tax archives if true. see fix_wp_tax_archives() for fix 

        ) );
     
        register_post_type( 'document' , $args );
    }


    /**
     * Register Custom Category
     * @since  1.0.0
     */
    public static function register_category_taxonomy() {
        
        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name'              =>  __( 'Document Category', 'documentate' ),
            'singular_name'     =>  __( 'Document Category', 'documentate' ),
            'search_items'      =>  __( 'Search Document Category', 'documentate' ),
            'all_items'         =>  __( 'All Document Categories', 'documentate' ),
            'parent_item'       =>  __( 'Parent Document Category', 'documentate' ),
            'parent_item_colon' =>  __( 'Parent Document Category:', 'documentate' ),
            'edit_item'         =>  __( 'Edit Document Category', 'documentate' ),
            'update_item'       =>  __( 'Update Document Category', 'documentate' ),
            'add_new_item'      =>  __( 'Add New Document Category', 'documentate' ),
            'new_item_name'     =>  __( 'New Document Category Name', 'documentate' ),
            'menu_name'         =>  __( 'Categories', 'documentate' )
        );  

        $category_base = documentate_get_option( 'category_base' );

        $args = apply_filters( 'docu_cat_args', array (
            'hierarchical'      =>  true,
            'labels'            =>  $labels,
            'show_ui'           =>  true,
            'query_var'         =>  true,
            'rewrite'           =>  array( 'slug' => $category_base, 'with_front' => false, 'hierarchical' => true )
        ) );

        register_taxonomy( 'docu_cat', array( 'document' ), $args );

    }

    /**
     * Register Custom Tags Taxonomy
     * @since  1.0.0
     */
    public static function register_tag_taxonomy() {
        
        $labels = array(
                        'name'      =>  __( 'Document Tags', 'documentate' ),
                        'singular_name'     =>  __( 'Document Tag', 'documentate' ),
                        'search_items'  =>  __( 'Search Document Tags', 'documentate' ),
                        'all_items'     =>  __( 'All Document Tags', 'documentate' ),
                        'edit_item'     =>  __( 'Edit Document Tag', 'documentate' ),
                        'update_item'   =>  __( 'Update Document Tag', 'documentate' ),
                        'add_new_item'  =>  __( 'Add New Document Tag', 'documentate' ),
                        'new_item_name'     =>  __( 'New Document Tag Name', 'documentate' ),
                        'menu_name'     =>  __( 'Tags', 'documentate' )
                );

        $tag_base = documentate_get_option( 'tag_base' );

        $args = apply_filters( 'docu_tag_args', array (
            'hierarchical'      =>  false,
            'labels'            =>  $labels,
            'show_ui'           =>  true,
            'query_var'         =>  true,
            'rewrite'           =>  array( 'slug' => $tag_base, 'with_front' => true )
        ) );

        register_taxonomy( 'docu_tag', array( 'document' ), $args );

    }


    /**
     * WordPress uses exclude_from_search where it shouldn't
     * resulting in empty taxonomy archives! 
     * see: https://core.trac.wordpress.org/ticket/17592
     * @since  1.0.0
     */
    public static function fix_wp_tax_archives( $q ) {
        if ( is_tax( array( 'docu_cat', 'docu_tag' ) ) )
            $q->set( 'post_type', 'document' );
    }


}

endif;

Docu_Post_Type::init();