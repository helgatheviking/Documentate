<?php
/**
 * Documentate Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @author      helgatheviking
 * @category    Core
 * @package     Documentate/Functions
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * is_documentate - Returns true if on a page which uses Documentate templates (cart and checkout are standard pages with shortcodes and thus are not included)
 * @return bool
 */
function is_documentate() {
	return apply_filters( 'is_documentate', ( is_document_archive() || is_document_taxonomy() || is_document() ) ? true : false );
}

if ( ! function_exists( 'is_document_archive' ) ) {

	/**
	 * is_document_archive - Returns true when viewing the document type archive (shop).
	 * @return bool
	 */
	function is_document_archive() {
		return ( is_post_type_archive( 'document' ) || is_page( documentate_get_option( 'archive_page_id' ) ) ) ? true : false;
	}
}

if ( ! function_exists( 'is_document_taxonomy' ) ) {

	/**
	 * is_document_taxonomy - Returns true when viewing a document taxonomy archive.
	 * @return bool
	 */
	function is_document_taxonomy() {
		return is_tax( get_object_taxonomies( 'document' ) );
	}
}

if ( ! function_exists( 'is_document_category' ) ) {

	/**
	 * is_document_category - Returns true when viewing a document category.
	 * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_document_category( $term = '' ) {
		return is_tax( 'docu_cat', $term );
	}
}

if ( ! function_exists( 'is_document_tag' ) ) {

	/**
	 * is_document_tag - Returns true when viewing a document tag.
	 * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_document_tag( $term = '' ) {
		return is_tax( 'docu_tag', $term );
	}
}

if ( ! function_exists( 'is_document' ) ) {

	/**
	 * is_document - Returns true when viewing a single document.
	 * @return bool
	 */
	function is_document() {
		return is_singular( array( 'document' ) );
	}
}



