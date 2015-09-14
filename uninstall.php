<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit ();

global $wpdb;

$documentate_tbl_prefix = $wpdb->prefix;

$settings = get_option( 'documentate_settings' );
$uninstall_mode = isset( $settings['uninstall_mode'] ) ? $settings['uninstall_mode'] : 'none';
	
// Delete Plugin Settings From options Table
if( in_array( $uninstall_mode, array( 'settings', 'nuclear' ) ){
    delete_option('documentate_settings');
    delete_option( 'documentate_permalinks' );
    delete_option('documentate_db_version');
}

// Delete ALL the things
if( $uninstall_mode == 'nuclear' ){
    // Delete `documentate_termmeta` table
    $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}documentate_termmeta" );


    	
    // Get Knowledgebase page and Delete all relivent Data
    $archive_page_id = isset( $settings['archive_page_id' ] ) ? $settings['archive_page_id'] : 0;
    wp_trash_post( $archive_page_id );
    				
    //Delete all Knowledgebase page Relivent data from `postmeta` Table
    $wpdb->query("Delete From ".$wpdb->prefix."postmeta Where post_id = $archive_page_id");

    //Delete all Knowledgebase page Child data from `posts` Table
    $wpdb->query("Delete From ".$wpdb->prefix."posts Where post_parent = $archive_page_id");

    //Delete Knowledgebase page from `posts` Table
    $wpdb->query("Delete From ".$wpdb->prefix."posts Where ID = $archive_page_id");
    
    }
    	
    // Get all Images of `documentate` post type and Delete all Images Data
    $documentate_get_post_images = $wpdb->get_results("Select * From ".$wpdb->prefix."posts Where post_type = 'documentate'");

    $documentate_upload_dir = wp_upload_dir();

    foreach($documentate_get_post_images as $get_post_images){
        $documentate_posts_img_ID = $get_post_images->ID;

        $documentate_post_imgs_qry = $wpdb->get_results("Select * From ".$wpdb->prefix."posts
                                                 Where post_parent = $documentate_posts_img_ID
                                                 And post_type = 'attachment'
                                                 And post_mime_type = 'image/jpeg'");
        foreach($documentate_post_imgs_qry as $get_post_img){
            $documentate_img_ID = $get_post_img->ID;

            // Extract path from images
            $documentate_img_path = get_post_meta($documentate_img_ID, "_wp_attached_file", true);
            $documentate_main_img_name = substr( $documentate_img_path, strrpos( $documentate_img_path, '/' )+1 );
            $documentate_sub_path = substr($documentate_img_path, 0, strrpos( $documentate_img_path, '/'));

            $documentate_img_meta = get_post_meta($documentate_img_ID, "_wp_attachment_metadata", true);

            $documentate_thumbnail = $documentate_img_meta['sizes']['thumbnail']['file']; 
            $documentate_medium = $documentate_img_meta['sizes']['medium']['file']; 
            $documentate_post_thumbnail = $documentate_img_meta['sizes']['post-thumbnail']['file'];

            $documentate_upload_path = $documentate_upload_dir["basedir"];

            unlink($documentate_upload_path."/".$documentate_sub_path."/".$documentate_main_img_name);
            unlink($documentate_upload_path."/".$documentate_sub_path."/".$documentate_thumbnail);
            unlink($documentate_upload_path."/".$documentate_sub_path."/".$documentate_medium);
            unlink($documentate_upload_path."/".$documentate_sub_path."/".$documentate_post_thumbnail);

            //Delete all Knowledgebase Posts from `posts` Table
            $wpdb->query("Delete From ".$wpdb->prefix."postmeta Where post_id = $documentate_img_ID");
        }
    }
    	
    // Get all Posts of `documentate` post type and Delete all relivent Data
    $documentate_get_posts = $wpdb->get_results("Select * From ".$wpdb->prefix."posts Where post_type = 'documentate'");

    foreach($documentate_get_posts as $get_posts){
        $documentate_posts_ID = $get_posts->ID;

        //Delete all Comments of `documentate` posts from `comments` Table
        $wpdb->query("Delete From ".$wpdb->prefix."comments Where comment_post_ID = $documentate_posts_ID");

        //Delete all Meta Data of `documentate` posts from `postmeta` Table
        $wpdb->query("Delete From ".$wpdb->prefix."postmeta Where post_id = $documentate_posts_ID");

        //Delete all `documentate` posts Realtion Data from `term_relationships` Table
        $wpdb->query("Delete From ".$wpdb->prefix."term_relationships Where object_id = $documentate_posts_ID");

        //Delete all `documentate` Child data from `posts` Table
        $wpdb->query("Delete From ".$wpdb->prefix."posts Where post_parent = $documentate_posts_ID");

        //Delete all Knowledgebase Posts from `posts` Table
        $wpdb->query("Delete From ".$wpdb->prefix."posts Where ID = $documentate_posts_ID");
    }
    	
    // Delete All Categories and Tags of Knowledgebase
    $documentate_get_terms = $wpdb->get_results("Select documentate_term.*, documentate_tax.*
                                         From ".$wpdb->prefix."terms As documentate_term
                                         Inner join ".$wpdb->prefix."term_taxonomy As documentate_tax
                                         On documentate_term.term_id = documentate_tax.term_id
                                         Where documentate_tax.taxonomy = 'docu_cat'
                                         Or documentate_tax.taxonomy = 'docu_tag'");

    foreach($documentate_get_terms as $get_term){
        $documentate_term_ID = $get_term->term_id;

        $wpdb->query("Delete From ".$wpdb->prefix."terms Where term_id = $documentate_term_ID");
    }
    	
    // Delete All Taxonomies and Tags of Knowledgebase
    $wpdb->query("Delete From ".$wpdb->prefix."term_taxonomy Where taxonomy = 'docu_cat'");

    $wpdb->query("Delete From ".$wpdb->prefix."term_taxonomy Where taxonomy = 'docu_tag'");

}

flush_rewrite_rules();