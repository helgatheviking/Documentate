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
       	
    // Get Documents page and Delete all relevent Data
    $archive_page_id = isset( $settings['archive_page_id' ] ) ? $settings['archive_page_id'] : 0;
    				
    //Delete all documents page Child data from `posts` Table
    $wpdb->query("Delete From ".$wpdb->prefix."posts Where post_parent = $archive_page_id");

    //Delete documents page from `posts` Table
    $wpdb->query("Delete From ".$wpdb->prefix."posts Where ID = $archive_page_id");
    
    }
    	
    // Get all `document` post type and delete all relevant data
    $document_posts = $wpdb->get_results("Select * From ".$wpdb->prefix."posts Where post_type = 'document'");

    $upload_dir = wp_upload_dir();

    foreach( $document_posts as $post ){

        // delete all attached images
        $attachments = $wpdb->get_results("Select * From ".$wpdb->prefix."posts
                                                 Where post_parent = $post->ID,
                                                 And post_type = 'attachment'
                                                 And post_mime_type = 'image/jpeg'");
        foreach($attachments as $attachment){

            // Extract path from images
            $img_path = get_post_meta($attachment->ID, "_wp_attached_file", true);
            $main_img_name = substr( $img_path, strrpos( $img_path, '/' )+1 );
            $sub_path = substr($img_path, 0, strrpos( $img_path, '/'));

            $img_meta = get_post_meta($attachment->ID, "_wp_attachment_metadata", true);

            $thumbnail = $img_meta['sizes']['thumbnail']['file']; 
            $medium = $img_meta['sizes']['medium']['file']; 
            $post_thumbnail = $img_meta['sizes']['post-thumbnail']['file'];

            $documentate_upload_path = $upload_dir["basedir"];

            unlink($documentate_upload_path."/".$sub_path."/".$main_img_name);
            unlink($documentate_upload_path."/".$sub_path."/".$thumbnail);
            unlink($documentate_upload_path."/".$sub_path."/".$medium);
            unlink($documentate_upload_path."/".$sub_path."/".$post_thumbnail);

            //Delete all Documents Posts from `posts` Table
            $wpdb->query("Delete From ".$wpdb->prefix."postmeta Where post_id = $attachment->ID");
        }

        //Delete all Comments of `document` posts from `comments` Table
        $wpdb->query("Delete From ".$wpdb->prefix."comments Where comment_post_ID = $post->ID");

        //Delete all Meta Data of `document` posts from `postmeta` Table
        $wpdb->query("Delete From ".$wpdb->prefix."postmeta Where post_id = $post->ID");

        //Delete all `document` posts Realtion Data from `term_relationships` Table
        $wpdb->query("Delete From ".$wpdb->prefix."term_relationships Where object_id = $post->ID");

        //Delete all `document` Child data from `posts` Table
        $wpdb->query("Delete From ".$wpdb->prefix."posts Where post_parent = $post->ID");

        //Delete all document posts from `posts` Table
        $wpdb->query("Delete From ".$wpdb->prefix."posts Where ID = $post->ID");
    
    }
    	    	
    // Delete All Categories and Tags of Documents
    $document_terms = $wpdb->get_results("Select documentate_term.*, documentate_tax.*
                                         From ".$wpdb->prefix."terms As documentate_term
                                         Inner join ".$wpdb->prefix."term_taxonomy As documentate_tax
                                         On documentate_term.term_id = documentate_tax.term_id
                                         Where documentate_tax.taxonomy = 'docu_cat'
                                         Or documentate_tax.taxonomy = 'docu_tag'");

    foreach($document_terms as $term){
        $wpdb->query("Delete From ".$wpdb->prefix."terms Where term_id = $term->term_id");
    }
    	
    // Delete All Taxonomies and Tags of Documents
    $wpdb->query("Delete From ".$wpdb->prefix."term_taxonomy Where taxonomy = 'docu_cat'");

    $wpdb->query("Delete From ".$wpdb->prefix."term_taxonomy Where taxonomy = 'docu_tag'");

    // Delete `documentate_termmeta` table
    $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}documentate_termmeta" );


}

flush_rewrite_rules();