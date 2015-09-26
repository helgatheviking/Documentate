<?php
/**
 * Documentate Pre-WordPress 4.4 Compatibility
 * This won't be needed once term meta is added to core in 4.4
 *
 * Functions for term meta.
 *
 * @author 		helgatheviking
 * @category 	Core
 * @package 	Documentate/Functions
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Documentate Term/Order item Meta API - set table name
 * shouldn't be needed in WP4.4
 */
function documentate_taxonomy_metadata_wpdbfix() {
	global $wpdb;

	$wpdb->termmeta = $wpdb->prefix . 'termmeta';

	$wpdb->tables[] = 'termmeta';
	
}
add_action( 'init', 'documentate_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'documentate_taxonomy_metadata_wpdbfix', 0 );

/**
 * Documentate Term Meta API - Update term meta
 *
 * @param mixed $term_id
 * @param string $meta_key
 * @param mixed $meta_value
 * @param string $prev_value (default: '')
 * @return bool
 */
function update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'term', $term_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Documentate Term Meta API - Add term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return bool
 */
function add_term_meta( $term_id, $meta_key, $meta_value, $unique = false ){
	return add_metadata( 'term', $term_id, $meta_key, $meta_value, $unique );
}

/**
 * Documentate Term Meta API - Delete term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param string $meta_value (default: '')
 * @param bool $delete_all (default: false)
 * @return bool
 */
function delete_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
	return delete_metadata( 'term', $term_id, $meta_key, $meta_value, $delete_all );
}

/**
 * Documentate Term Meta API - Get term meta
 *
 * @param mixed $term_id
 * @param string $key
 * @param bool $single (default: true)
 * @return mixed
 */
function get_term_meta( $term_id, $key, $single = true ) {
	return get_metadata( 'term', $term_id, $key, $single );
}