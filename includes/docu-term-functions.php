<?php
/**
 * Documentate Terms
 *
 * Functions for handling terms/term meta.
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
 * Wrapper for wp_get_post_terms which supports ordering by parent.
 *
 * NOTE: At this point in time, ordering by menu_order for example isn't possible with this function. wp_get_post_terms has no
 *   filters which we can utilise to modify it's query. https://core.trac.wordpress.org/ticket/19094
 *
 * @param  int $document_id
 * @param  string $taxonomy
 * @param  array  $args
 * @return array
 */
function documentate_get_document_terms( $document_id, $taxonomy, $args = array() ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	if ( empty( $args['orderby'] ) && taxonomy_is_document_attribute( $taxonomy ) ) {
		$args['orderby'] = documentate_attribute_orderby( $taxonomy );
	}

	// Support ordering by parent
	if ( ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'name_num', 'parent' ) ) ) {
		$fields  = isset( $args['fields'] ) ? $args['fields'] : 'all';
		$orderby = $args['orderby'];

		// Unset for wp_get_post_terms
		unset( $args['orderby'] );
		unset( $args['fields'] );

		$terms = wp_get_post_terms( $document_id, $taxonomy, $args );

		switch ( $orderby ) {
			case 'name_num' :
				usort( $terms, '_documentate_get_document_terms_name_num_usort_callback' );
			break;
			case 'parent' :
				usort( $terms, '_documentate_get_document_terms_parent_usort_callback' );
			break;
		}

		switch ( $fields ) {
			case 'names' :
				$terms = wp_list_pluck( $terms, 'name' );
				break;
			case 'ids' :
				$terms = wp_list_pluck( $terms, 'term_id' );
				break;
			case 'slugs' :
				$terms = wp_list_pluck( $terms, 'slug' );
				break;
		}
	} elseif ( ! empty( $args['orderby'] ) && $args['orderby'] === 'menu_order' ) {
		// wp_get_post_terms doesn't let us use custom sort order
		$args['include'] = wp_get_post_terms( $document_id, $taxonomy, array( 'fields' => 'ids' ) );

		if ( empty( $args['include'] ) ) {
			$terms = array();
		} else {
			// This isn't needed for get_terms
			unset( $args['orderby'] );

			// Set args for get_terms
			$args['menu_order'] = isset( $args['order'] ) ? $args['order'] : 'ASC';
			$args['hide_empty'] = isset( $args['hide_empty'] ) ? $args['hide_empty'] : 0;
			$args['fields']     = isset( $args['fields'] ) ? $args['fields'] : 'names';

			// Ensure slugs is valid for get_terms - slugs isn't supported
			$args['fields']     = $args['fields'] === 'slugs' ? 'id=>slug' : $args['fields'];
			$terms              = get_terms( $taxonomy, $args );
		}
	} else {
		$terms = wp_get_post_terms( $document_id, $taxonomy, $args );
	}

	return apply_filters( 'documentate_get_document_terms' , $terms, $document_id, $taxonomy, $args );
}


/**
 * Sort by name (numeric)
 * @param  WP_POST object $a
 * @param  WP_POST object $b
 * @return int
 */
function _documentate_get_document_terms_name_num_usort_callback( $a, $b ) {
	if ( $a->name + 0 === $b->name + 0 ) {
		return 0;
	}
	return ( $a->name + 0 < $b->name + 0 ) ? -1 : 1;
}
/**
 * Sort by parent
 * @param  WP_POST object $a
 * @param  WP_POST object $b
 * @return int
 */
function _documentate_get_document_terms_parent_usort_callback( $a, $b ) {
	if ( $a->parent === $b->parent ) {
		return 0;
	}
	return ( $a->parent < $b->parent ) ? 1 : -1;
}

/**
 * Documentate Dropdown categories
 *
 * Stuck with this until a fix for http://core.trac.wordpress.org/ticket/13258
 * We use a custom walker, just like WordPress does
 *
 * @param int $deprecated_show_uncategorized (default: 1)
 * @return string
 */
function documentate_document_dropdown_categories( $args = array(), $deprecated_hierarchical = 1, $deprecated_show_uncategorized = 1, $deprecated_orderby = '' ) {
	global $wp_query;

	if ( ! is_array( $args ) ) {
		_deprecated_argument( 'documentate_document_dropdown_categories()', '2.1', 'show_counts, hierarchical, show_uncategorized and orderby arguments are invalid - pass a single array of values instead.' );

		$args['show_count']         = $args;
		$args['hierarchical']       = $deprecated_hierarchical;
		$args['show_uncategorized'] = $deprecated_show_uncategorized;
		$args['orderby']            = $deprecated_orderby;
	}

	$current_docu_cat = isset( $wp_query->query['docu_cat'] ) ? $wp_query->query['docu_cat'] : '';
	$defaults            = array(
		'pad_counts'         => 1,
		'show_count'         => 1,
		'hierarchical'       => 1,
		'hide_empty'         => 1,
		'show_uncategorized' => 1,
		'orderby'            => 'name',
		'selected'           => $current_docu_cat,
		'menu_order'         => false
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['orderby'] == 'order' ) {
		$args['menu_order'] = 'asc';
		$args['orderby']    = 'name';
	}

	$terms = get_terms( 'docu_cat', apply_filters( 'documentate_document_dropdown_categories_get_terms_args', $args ) );

	if ( ! $terms ) {
		return;
	}

	$output  = "<select name='docu_cat' class='dropdown_docu_cat'>";
	$output .= '<option value="" ' .  selected( $current_docu_cat, '', false ) . '>' . __( 'Select a category', 'woocommerce' ) . '</option>';
	$output .= documentate_walk_category_dropdown_tree( $terms, 0, $args );
	if ( $args['show_uncategorized'] ) {
		$output .= '<option value="0" ' . selected( $current_docu_cat, '0', false ) . '>' . __( 'Uncategorized', 'woocommerce' ) . '</option>';
	}
	$output .= "</select>";

	echo $output;
}

/**
 * Walk the Product Categories.
 *
 * @return mixed
 */
function documentate_walk_category_dropdown_tree() {
	if ( ! class_exists( 'WC_Product_Cat_Dropdown_Walker' ) ) {
		include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-dropdown-walker.php' );
	}

	$args = func_get_args();

	// the user's options are the third parameter
	if ( empty( $args[2]['walker']) || !is_a($args[2]['walker'], 'Walker' ) ) {
		$walker = new WC_Product_Cat_Dropdown_Walker;
	} else {
		$walker = $args[2]['walker'];
	}

	return call_user_func_array( array( &$walker, 'walk' ), $args );
}

/**
 * Documentate Term/Order item Meta API - set table name
 */
function documentate_taxonomy_metadata_wpdbfix() {
	global $wpdb;
	$termmeta_name = 'documentate_termmeta';
	$itemmeta_name = 'documentate_order_itemmeta';

	$wpdb->documentate_termmeta = $wpdb->prefix . $termmeta_name;
	$wpdb->order_itemmeta = $wpdb->prefix . $itemmeta_name;

	$wpdb->tables[] = 'documentate_termmeta';
	$wpdb->tables[] = 'documentate_order_itemmeta';
}
add_action( 'init', 'documentate_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'documentate_taxonomy_metadata_wpdbfix', 0 );

/**
 * When a term is split, ensure meta data maintained.
 * @param  int $old_term_id
 * @param  int $new_term_id
 * @param  string $term_taxonomy_id
 * @param  string $taxonomy
 */
function documentate_taxonomy_metadata_update_content_for_split_terms( $old_term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
	global $wpdb;

	if ( 'docu_cat' === $taxonomy || taxonomy_is_document_attribute( $taxonomy ) ) {
		$old_meta_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}documentate_termmeta WHERE documentate_term_id = %d;", $old_term_id ) );

		// Copy across to split term
		if ( $old_meta_data ) {
			foreach ( $old_meta_data as $meta_data ) {
				$wpdb->insert(
					"{$wpdb->prefix}documentate_termmeta",
					array(
						'documentate_term_id' => $new_term_id,
						'meta_key'            => $meta_data->meta_key,
						'meta_value'          => $meta_data->meta_value
					)
				);
			}
		}
	}
}
add_action( 'split_shared_term', 'documentate_taxonomy_metadata_update_content_for_split_terms', 10, 4 );

/**
 * Documentate Term Meta API - Update term meta
 *
 * @param mixed $term_id
 * @param string $meta_key
 * @param mixed $meta_value
 * @param string $prev_value (default: '')
 * @return bool
 */
function update_documentate_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'documentate_term', $term_id, $meta_key, $meta_value, $prev_value );
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
function add_documentate_term_meta( $term_id, $meta_key, $meta_value, $unique = false ){
	return add_metadata( 'documentate_term', $term_id, $meta_key, $meta_value, $unique );
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
function delete_documentate_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
	return delete_metadata( 'documentate_term', $term_id, $meta_key, $meta_value, $delete_all );
}

/**
 * Documentate Term Meta API - Get term meta
 *
 * @param mixed $term_id
 * @param string $key
 * @param bool $single (default: true)
 * @return mixed
 */
function get_documentate_term_meta( $term_id, $key, $single = true ) {
	return get_metadata( 'documentate_term', $term_id, $key, $single );
}

/**
 * Move a term before the a	given element of its hierarchy level
 *
 * @param int $the_term
 * @param int $next_id the id of the next sibling element in save hierarchy level
 * @param string $taxonomy
 * @param int $index (default: 0)
 * @param mixed $terms (default: null)
 * @return int
 */
function documentate_reorder_terms( $the_term, $next_id, $taxonomy, $index = 0, $terms = null ) {

	if( ! $terms ) $terms = get_terms($taxonomy, 'menu_order=ASC&hide_empty=0&parent=0' );
	if( empty( $terms ) ) return $index;

	$id	= $the_term->term_id;

	$term_in_level = false; // flag: is our term to order in this level of terms

	foreach ($terms as $term) {

		if( $term->term_id == $id ) { // our term to order, we skip
			$term_in_level = true;
			continue; // our term to order, we skip
		}
		// the nextid of our term to order, lets move our term here
		if(null !== $next_id && $term->term_id == $next_id) {
			$index++;
			$index = documentate_set_term_order($id, $index, $taxonomy, true);
		}

		// set order
		$index++;
		$index = documentate_set_term_order($term->term_id, $index, $taxonomy);

		// if that term has children we walk through them
		$children = get_terms($taxonomy, "parent={$term->term_id}&menu_order=ASC&hide_empty=0");
		if( !empty($children) ) {
			$index = documentate_reorder_terms( $the_term, $next_id, $taxonomy, $index, $children );
		}
	}

	// no nextid meaning our term is in last position
	if( $term_in_level && null === $next_id )
		$index = documentate_set_term_order($id, $index+1, $taxonomy, true);

	return $index;
}

/**
 * Set the sort order of a term
 *
 * @param int $term_id
 * @param int $index
 * @param string $taxonomy
 * @param bool $recursive (default: false)
 * @return int
 */
function documentate_set_term_order( $term_id, $index, $taxonomy, $recursive = false ) {

	$term_id 	= (int) $term_id;
	$index 		= (int) $index;

	update_documentate_term_meta( $term_id, 'order', $index );

	if( ! $recursive ) return $index;

	$children = get_terms($taxonomy, "parent=$term_id&menu_order=ASC&hide_empty=0");

	foreach ( $children as $term ) {
		$index ++;
		$index = documentate_set_term_order($term->term_id, $index, $taxonomy, true);
	}

	clean_term_cache( $term_id, $taxonomy );

	return $index;
}

/**
 * Add term ordering to get_terms
 *
 * It enables the support a 'menu_order' parameter to get_terms for the docu_cat taxonomy.
 * By default it is 'ASC'. It accepts 'DESC' too
 *
 * To disable it, set it ot false (or 0)
 *
 * @param array $clauses
 * @param array $taxonomies
 * @param array $args
 * @return array
 */
function documentate_terms_clauses( $clauses, $taxonomies, $args ) {
	global $wpdb;

	// No sorting when menu_order is false
	if ( isset( $args['menu_order'] ) && $args['menu_order'] == false ) {
		return $clauses;
	}

	// No sorting when orderby is non default
	if ( isset( $args['orderby'] ) && $args['orderby'] != 'name' ) {
		return $clauses;
	}

	// No sorting in admin when sorting by a column
	if ( is_admin() && isset( $_GET['orderby'] ) ) {
		return $clauses;
	}

	// wordpress should give us the taxonomies asked when calling the get_terms function. Only apply to categories
	$found = false;
	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( in_array( $taxonomy, apply_filters( 'documentate_sortable_taxonomies', array( 'docu_cat' ) ) ) ) {
			$found = true;
			break;
		}
	}
	if ( ! $found ) {
		return $clauses;
	}

	// Meta name
	$meta_name = 'order';

	// query fields
	if ( strpos( 'COUNT(*)', $clauses['fields'] ) === false )  {
		$clauses['fields']  .= ', tm.* ';
	}

	//query join
	$clauses['join'] .= " LEFT JOIN {$wpdb->documentate_termmeta} AS tm ON (t.term_id = tm.documentate_term_id AND tm.meta_key = '". $meta_name ."') ";

	// default to ASC
	if ( ! isset( $args['menu_order'] ) || ! in_array( strtoupper($args['menu_order']), array('ASC', 'DESC')) ) {
		$args['menu_order'] = 'ASC';
	}

	$order = "ORDER BY tm.meta_value+0 " . $args['menu_order'];

	if ( $clauses['orderby'] ):
		$clauses['orderby'] = str_replace('ORDER BY', $order . ',', $clauses['orderby'] );
	else:
		$clauses['orderby'] = $order;
	endif;

	return $clauses;
}
add_filter( 'terms_clauses', 'documentate_terms_clauses', 10, 3 );
