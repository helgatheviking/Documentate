<?php
/**
 * Knowledgebase Template Hooks
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */

/**
 * Content Wrappers
 *
 * @see documentate_output_content_wrapper()
 * @see documentate_output_content_wrapper_end()
 */
add_action( 'documentate_before_main_content', 'documentate_output_content_wrapper', 10 );
add_action( 'documentate_after_main_content', 'documentate_output_content_wrapper_end', 10 );

/**
 * Live Search
 *
 * @see documentate_search()
 */
add_action( 'documentate_before_main_content', 'document_search', 10 );

/**
 * Breadcrumbs
 *
 * @see documentate_breadcrumbs()
 */
add_action( 'documentate_before_main_content', 'documentate_breadcrumbs', 20 );


/**
 * Sidebar
 *
 * @see documentate_get_sidebar()
 */
add_action( 'documentate_sidebar', 'documentate_get_sidebar', 10 );


/**
 * Archive descriptions
 *
 * @see docu_cat_archive_description()
 * @see documentate_document_archive_description()
 */
add_action( 'documentate_archive_description', 'docu_cat_archive_description', 10 );
add_action( 'documentate_archive_description', 'documentate_document_archive_description', 10 );


/**
 * Product Loop Items
 *
 * @see documentate_template_loop_title()
 * @see documentate_template_loop_excerpt()
 */
add_action( 'documentate_document_loop_item_title', 'documentate_template_loop_title', 10 );
add_action( 'documentate_after_document_loop_item', 'documentate_template_loop_excerpt', 10 );


/**
 * Pagination after shop loops
 *
 * @see documentate_pagination()
 */
add_action( 'documentate_after_document_loop', 'documentate_pagination', 10 );


/**
 * Subcategories
 *
 * @see documentate_subcategory_thumbnail()
 * @see documentate_subcategory_documents()
 */
add_action( 'documentate_before_subcategory_title', 'documentate_subcategory_thumbnail', 10 );
add_action( 'documentate_after_subcategory', 'documentate_subcategory_documents', 10 );



/**
 * Document Content
 *
 * @see documentate_template_single_title()
 * @see documentate_template_single_content()
 * @see documentate_template_single_meta()
 */
add_action( 'documentate_single_document_content', 'documentate_template_single_title', 10 );
add_action( 'documentate_single_document_content', 'documentate_template_single_content', 20 );
add_action( 'documentate_single_document_content', 'documentate_template_single_meta', 30 );

/**
 * Document Comments
 *
 * @see documentate_comments()
 */
add_action( 'documentate_after_single_document', 'documentate_comments' );