<?php
/**
 * Rewrite Rules
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */

/**
 * Whether to use verbose page rules or not
 * @since  1.0.0
 */
//add_action( 'init', 'documentate_fix_rewrite_rules_init' );
function fix_rewrite_rules_init() {
	$permalinks = get_option( 'documentate_permalinks' );

	if ( ! empty( $permalinks['use_verbose_page_rules'] ) ) {
		$GLOBALS['wp_rewrite']->use_verbose_page_rules = true;
	}
}

/**
 * Various rewrite rule fixes
 *
 * @since 1.0.0
 * @param array $rules
 * @return array
 */
//add_filter( 'rewrite_rules_array', 'documentate_fix_rewrite_rules' );
function fix_rewrite_rules( $rules ) { 
	global $wp_rewrite;

	$permalinks        = get_option( 'documentate_permalinks' );
	$document_permalink = ! empty( $permalinks['document_base'] ) ? $permalinks['document_base'] : _x( 'knowledgebase', 'default slug', 'documentate' );

	// Fix the rewrite rules when the product permalink have %document_cat% flag
	if ( preg_match( '`/(.+)(/%document_cat%)`' , $document_permalink, $matches ) ) { 

		$x = array(); $i=0;

		foreach ( $rules as $rule => $rewrite ) { 

			// rules that start with docu
			if ( preg_match( '`^' . preg_quote( $matches[1], '`' ) . '`', $rule ) ){
				$x[$rule] = $rewrite;
			}
			

			if ( preg_match( '`^' . preg_quote( $matches[1], '`' ) . '/\(`', $rule ) && preg_match( '/^(index\.php\?docu_cat)(?!(.*documentate))/', $rewrite ) ) {
//				unset( $rules[ $rule ] );
			}

			
		}

		die(var_dump($x));
	}

	// If the shop page is used as the base, we need to enable verbose rewrite rules or sub pages will 404
	if ( ! empty( $permalinks['use_verbose_page_rules'] ) ) {
		$page_rewrite_rules = $wp_rewrite->page_rewrite_rules();
//		$rules              = array_merge( $page_rewrite_rules, $rules );
	}
die(var_dump($i));
	return $rules;
}


/**
 * Filter to allow docu_cat (aka document category) in the permalinks for products.
 *
 * @access public
 * @param string $permalink The existing permalink URL.
 * @param WP_Post $post
 * @return string
 */
//add_filter( 'post_type_link', 'documentate_document_post_type_link', 10, 2 );
//add_filter( 'post_link', 'documentate_document_post_type_link', 10, 2 );
function document_post_type_link( $permalink, $post ) {
	// Abort if post is not a documentate document
	if ( $post->post_type !== 'documentate' ) {
		return $permalink;
	}

	// Abort early if the placeholder rewrite tag isn't in the generated URL
	if ( false === strpos( $permalink, '%' ) ) {
		return $permalink;
	}

	// Get the custom taxonomy terms in use by this post
	$terms = get_the_terms( $post->ID, 'docu_cat' );

	if ( ! empty( $terms ) ) {
		usort( $terms, '_usort_terms_by_ID' ); // order by ID

		$category_object = apply_filters( 'documentate_document_post_type_link_document_cat', $terms[0], $terms, $post );
		$category_object = get_term( $category_object, 'docu_cat' );
		$docu_cat     = $category_object->slug;

		if ( $parent = $category_object->parent ) {
			$ancestors = get_ancestors( $category_object->term_id, 'docu_cat' );
			foreach ( $ancestors as $ancestor ) {
				$ancestor_object = get_term( $ancestor, 'docu_cat' );
				$docu_cat     = $ancestor_object->slug . '/' . $docu_cat;
			}
		}
	} else {
		// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
		$docu_cat = _x( 'uncategorized', 'slug', 'documentate' );
	}

	$find = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		'%post_id%',
		'%category%',
		'%document_cat%'
	);

	$replace = array(
		date_i18n( 'Y', strtotime( $post->post_date ) ),
		date_i18n( 'm', strtotime( $post->post_date ) ),
		date_i18n( 'd', strtotime( $post->post_date ) ),
		date_i18n( 'H', strtotime( $post->post_date ) ),
		date_i18n( 'i', strtotime( $post->post_date ) ),
		date_i18n( 's', strtotime( $post->post_date ) ),
		$post->ID,
		$docu_cat,
		$docu_cat
	);

	$permalink = str_replace( $find, $replace, $permalink );

	return $permalink;
}



//add_filter('post_link', 'rating_permalink', 10, 3);
//add_filter('post_type_link', 'rating_permalink', 10, 3);
 
function rating_permalink($permalink, $post_id, $leavename) {
    if (strpos($permalink, '%document_cat%') === FALSE) return $permalink;
     
        // Get post
        $post = get_post($post_id);
        if (!$post) return $permalink;
 
        // Get taxonomy terms
        $terms = wp_get_object_terms($post->ID, 'docu_cat');   
        if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) $taxonomy_slug = $terms[0]->slug;
        else $taxonomy_slug = 'uncategorized';
 
    return str_replace('%document_cat%', $taxonomy_slug, $permalink);
}   