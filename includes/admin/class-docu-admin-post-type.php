<?php
/**
 * Post Types Admin
 *
 * @author   helgatheviking
 * @category Admin
 * @package  Documentate/Admin
 * @version  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Documentate_Admin_Post_Types' ) ) :

/**
 * Documentate_Admin_Post_Type Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class Documentate_Admin_Post_Type {

	/**
	 * Constructor
	 */
	public function __construct() {

		// WP List table columns. Defined here so they are always available for events such as inline editing.
		add_filter( 'manage_edit-document_columns', array( $this, 'edit_columns' ) );
		add_action( 'manage_document_posts_custom_column', array( $this, 'custom_columns' ) );    

		// Show post counts in the dashboard
		if ( ! class_exists( 'Gamajo_Dashboard_Glancer' ) ) {
			include_once( 'class-gamajo-dashboard-glancer.php' );
		}
		add_action( 'dashboard_glance_items', array( $this, 'add_glance_counts' ) );
		add_action( 'admin_head', array( $this, 'fix_glance_icon' ) );

		// Allow filtering of posts by taxonomy in the admin view
		add_action( 'restrict_manage_posts', array( $this, 'add_taxonomy_filters' ) );

		// Meta-Box Class
		include_once( 'class-docu-admin-meta-boxes.php' );

	}

	/**
	 * Custom Post Type Columns
	 * @param array $columns
	 * @since  1.0.0
	 */ 
	public function edit_columns($columns){
	    $columns = array(  
	        'cb' 		=> 	'<input type=\'checkbox\' />', 
	        'title' 	=> 	__( 'Title', 'kbe' ),
	        'author' 	=> 	__( 'Author', 'kbe' ),
	        'cat' 		=> 	__( 'Cateogry', 'kbe' ),
	        'tag' 		=> 	__( 'Tags', 'kbe' ),
	    //    'comment' 	=> 	__( 'Comments', 'kbe' ),
	        'views' 	=> 	__( 'Views', 'kbe' ),
	        'date' 		=> 	__( 'Date', 'kbe' )
	    );
	    return $columns;  
	}    

	/**
	 * Display of Custom Post Type Columns
	 * @param array $column
	 * @since  1.0.0
	 */ 
	public function custom_columns( $column ){
	    global $post;  
	    switch ( $column ){ 
	        case 'title':         
	            the_title();
	        break; 
	        case 'author':         
	            the_author();
	        break;
	        case 'cat':         
	            echo get_the_term_list( $post->ID, 'docu_cat' , ' ' , ', ' , '' );
	        break;
	        case 'tag':         
	            echo get_the_term_list( $post->ID, 'docu_tag' , ' ' , ', ' , '' );
	        break;
	        case 'comment':         
	            comments_number( __('No Comments','documentate'), __('1 Comment','documentate'), __('% Comments','documentate') );
	        break;
	        case 'views':
	            $views = get_post_meta($post->ID, 'documentate_post_views_count', true);
	            if( $views ){
	                printf( _n( '%s view', '%s views', $rating, 'documentate' ), $views );
	            }else{
	                echo __( 'No Views', 'documentate' );
	            }
	        break;
	        case 'date':         
	            the_date();
	        break;
	    }
	}


	/**
	 * Add taxonomy filters to the post type list page.
	 *
	 * Code artfully lifted from http://pippinsplugins.com/
	 *
	 * @global string $typenow
	 */
	public function add_taxonomy_filters() {
		global $typenow;
		// Must set this to the post type you want the filter(s) displayed on
		if ( 'document' !== $typenow ) {
			return;
		}
		foreach ( array( 'docu_cat' ) as $tax_slug ) {
			echo $this->build_taxonomy_filter( $tax_slug );
		}
	}


	/**
	 * Build an individual dropdown filter.
	 *
	 * @param  string $tax_slug Taxonomy slug to build filter for.
	 *
	 * @return string Markup, or empty string if taxonomy has no terms.
	 */
	protected function build_taxonomy_filter( $tax_slug ) {
		$terms = get_terms( $tax_slug );
		if ( 0 == count( $terms ) ) {
			return '';
		}
		$tax_name         = $this->get_taxonomy_name_from_slug( $tax_slug );
		$current_tax_slug = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : false;
		$filter  = '<select name="' . esc_attr( $tax_slug ) . '" id="' . esc_attr( $tax_slug ) . '" class="postform">';
		$filter .= '<option value="0">' . esc_html( $tax_name ) .'</option>';
		$filter .= $this->build_term_options( $terms, $current_tax_slug );
		$filter .= '</select>';
		return $filter;
	}


	/**
	 * Get the friendly taxonomy name, if given a taxonomy slug.
	 *
	 * @param  string $tax_slug Taxonomy slug.
	 *
	 * @return string Friendly name of taxonomy, or empty string if not a valid taxonomy.
	 */
	protected function get_taxonomy_name_from_slug( $tax_slug ) {
		$tax_obj = get_taxonomy( $tax_slug );
		if ( ! $tax_obj )
			return '';
		return $tax_obj->labels->name;
	}


	/**
	 * Build a series of option elements from an array.
	 *
	 * Also checks to see if one of the options is selected.
	 *
	 * @param  array  $terms            Array of term objects.
	 * @param  string $current_tax_slug Slug of currently selected term.
	 *
	 * @return string Markup.
	 */
	protected function build_term_options( $terms, $current_tax_slug ) {
		$options = '';
		foreach ( $terms as $term ) {
			$options .= sprintf(
				'<option value="%s"%s />%s</option>',
				esc_attr( $term->slug ),
				selected( $current_tax_slug, $term->slug ),
				esc_html( $term->name . '(' . $term->count . ')' )
			);
		}
		return $options;
	}


	/**
	 * Add counts to "At a Glance" dashboard widget in WP 3.8+
	 *
	 * @since 0.1.0
	 */
	public function add_glance_counts() {
		$glancer = new Gamajo_Dashboard_Glancer;
		$glancer->add( 'document', array( 'publish', 'pending' ) );
	}


	/**
	 * Fix the dashboard widget icons
	 *
	 * @since 0.1.0
	 */
	public function fix_glance_icon() { ?>
		<style>#dashboard_right_now .document-count a:before { content: '\f331'; }</style>
	<?php
	}

	

}

endif;

new Documentate_Admin_Post_Type();