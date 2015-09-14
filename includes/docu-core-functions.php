<?php
/**
 * Knowledgebase Core Functions
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */


/**
 * Knowledgebase get option
 * combines both settings and permalinks
 * @since  1.0.0
 */ 
function documentate_get_option( $name = '' ){
	static $settings = array(); 

	if( empty( $settings ) ){
		$settings = (array) get_option( 'documentate_settings', array() );
		$permalinks = (array) get_option( 'documentate_permalinks', array() );

		$settings = array_merge( $settings, $permalinks );

		$defaults = array( 
			'archive_page_id' => 0,
			'archive_display' => 'subcategories',
			'docu_qty' => 0,
			'archive_display' => 'documents',
			'search_setting' =>  'hide',
			'breadcrumbs_setting' =>  'hide',
			'sidebar' => 'hide',
			'uninstall_mode' => 'none',
			'document_base' => _x( 'document', 'default slug', 'documentate' ),
			'category_base' => _x( 'docu_cat', 'default category slug', 'documentate' ),
			'tag_base' => _x( 'docu_tag', 'default tag slug', 'documentate' ),
			'use_verbose_page_rules' => false
		);
		
		$settings = wp_parse_args( $settings, $defaults );

	 }

	return isset( $settings[$name] ) ? $settings[$name] : '';

}


/**
 * Knowledgebase get template path
 * @since  1.0.0
 */ 
function documentate_get_template_path(){
	return apply_filters( 'documentate_template_path', 'wp_knowledgebase/' );
}


/**
 * Load a template.
 *
 * Handles template usage so that we can use our own templates instead of the themes.
 *
 * Templates are in the 'templates' folder. knowledgebase looks for theme
 * overrides in /theme/documentate/ by default
 *
 * @param mixed $template
 * @return string
 */
add_filter( 'template_include', 'documentate_template_chooser' );

function documentate_template_chooser($template){

	global $wp_query;

	$template_path = documentate_get_template_path();
	
	$archive_page_id = documentate_get_option( 'archive_page_id' );
	
	$find = array();
	$file = '';

	if ( $wp_query->is_search && get_post_type() == 'document' ){

		$file = 'documentate_search.php';
		$find[] = $file;
		$find[] = $template_path . $file;

	} elseif ( is_single() && get_post_type() == 'document' ) {

		$file   = 'single-document.php';
		$find[] = $file;
		$find[] = $template_path . $file;

	} elseif ( is_tax( array( 'docu_cat', 'docu_tag' ) ) ) {

		$term   = get_queried_object();

		$file = 'taxonomy-' . $term->taxonomy . '.php';

		$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
		$find[] = $template_path . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
		$find[] = 'taxonomy-' . $term->taxonomy . '.php';
		$find[] = $template_path . 'taxonomy-' . $term->taxonomy . '.php';
		$find[] = $file;
		$find[] = $template_path . $file;

	} elseif ( is_post_type_archive( 'document' ) || ( $archive_page_id && is_page( $archive_page_id ) ) ) {

		$file   = 'archive-document.php';
		$find[] = $file;
		$find[] = $template_path . $file;

	}

	if ( $file ) {
		$template       = locate_template( array_unique( $find ) );
		if ( ! $template ) {
			$template = trailingslashit( Docu()->plugin_path() ) . 'templates/' . $file;
		}
	}

	return $template;

}


/**
 * Get other templates (e.g. document attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function documentate_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = documentate_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'documentate_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'documentate_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'documentate_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function documentate_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = documentate_get_template_path();
	}

	if ( ! $default_path ) {
		$default_path = Docu()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template || Documentate_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'documentate_locate_template', $template, $template_name, $template_path );
}


/**
 * Format content to display shortcodes
 *
 * @since  2.3.0
 * @param  string $raw_string
 * @return string
 */
function documentate_format_content( $raw_string ) {
	return apply_filters( 'documentate_format_content', do_shortcode( shortcode_unautop( wpautop( $raw_string ) ) ), $raw_string );
}

/**
 * Format excerpt
 * Adds support for Jetpack Markdown
 *
 * @since  2.4.0
 * @param  string $content
 * @return string
 */
function documentate_format_document_excerpt( $content ) {
	// Add support for Jetpack Markdown
	if ( class_exists( 'WPCom_Markdown' ) ) {
		$markdown = WPCom_Markdown::get_instance();

		return wpautop( $markdown->transform( $content, array( 'unslash' => false ) ) );
	}

	return $content;
}

add_filter( 'documentate_document_excerpt', 'documentate_format_document_excerpt', 9999999 );

/**
 * Document Excerpt 
 */
add_filter( 'documentate_document_excerpt', 'wptexturize' );
add_filter( 'documentate_document_excerpt', 'convert_smilies' );
add_filter( 'documentate_document_excerpt', 'convert_chars' );
add_filter( 'documentate_document_excerpt', 'wpautop' );
add_filter( 'documentate_document_excerpt', 'shortcode_unautop' );
add_filter( 'documentate_document_excerpt', 'prepend_attachment' );
add_filter( 'documentate_document_excerpt', 'do_shortcode', 11 ); // AFTER wpautop()


/**
 * Init for our rewrite rule fixes
 */
function documentate_fix_rewrite_rules_init() {
	$permalinks = get_option( 'documentate_permalinks' );

	if ( ! empty( $permalinks['use_verbose_page_rules'] ) ) {
		$GLOBALS['wp_rewrite']->use_verbose_page_rules = true;
	}
}
add_action( 'init', 'documentate_fix_rewrite_rules_init' );

/**
 * Various rewrite rule fixes
 *
 * @since 2.2
 * @param array $rules
 * @return array
 */
function documentate_fix_rewrite_rules( $rules ) {
	global $wp_rewrite;

	$permalinks        = get_option( 'documentate_permalinks' );
	$document_permalink = ! empty( $permalinks['document_base'] ) ? $permalinks['document_base'] : _x( 'document', 'slug', 'documentate' );

	// Fix the rewrite rules when the document permalink have %docu_cat% flag
	if ( preg_match( '`/(.+)(/%docu_cat%)`' , $document_permalink, $matches ) ) {
		foreach ( $rules as $rule => $rewrite ) {

			if ( preg_match( '`^' . preg_quote( $matches[1], '`' ) . '/\(`', $rule ) && preg_match( '/^(index\.php\?docu_cat)(?!(.*document))/', $rewrite ) ) {
				unset( $rules[ $rule ] );
			}
		}
	}

	// If the shop page is used as the base, we need to enable verbose rewrite rules or sub pages will 404
	if ( ! empty( $permalinks['use_verbose_page_rules'] ) ) {
		$page_rewrite_rules = $wp_rewrite->page_rewrite_rules();
		$rules              = array_merge( $page_rewrite_rules, $rules );
	}

	return $rules;
}
add_filter( 'rewrite_rules_array', 'documentate_fix_rewrite_rules' );


/**
 * Filter to allow docu_cat in the permalinks for documents.
 *
 * @access public
 * @param string $permalink The existing permalink URL.
 * @param WP_Post $post
 * @return string
 */
function documentate_document_post_type_link( $permalink, $post ) {
	// Abort if post is not a document
	if ( $post->post_type !== 'document' ) {
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

		$category_object = apply_filters( 'documentate_document_post_type_link_docu_cat', $terms[0], $terms, $post );
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
		$docu_cat = _x( 'uncategorized', 'slug', 'woocommerce' );
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
		'%docu_cat%'
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
add_filter( 'post_type_link', 'documentate_document_post_type_link', 10, 2 );


/**
 * Get the placeholder image URL for products etc
 *
 * @access public
 * @return string
 */
function documentate_placeholder_img_src() {
    return apply_filters( 'documentate_placeholder_img_src', Docu()->plugin_url() . '/assets/images/placeholder.png' );
}


