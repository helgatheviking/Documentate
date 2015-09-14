<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author 		helgatheviking
 * @package 	Documentate/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

// if no more than 1 page OR if post type archive and display mode is categories only
if ( $wp_query->max_num_pages <= 1 || ( is_document_archive() && documentate_get_option( 'archive_display' ) === 0 ) ) {
	return;
}
?>
<nav class="documentate-pagination">
	<?php
		echo paginate_links( apply_filters( 'documentate_pagination_args', array(
			'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
			'format'       => '',
			'add_args'     => '',
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $wp_query->max_num_pages,
			'prev_text'    => '&larr;',
			'next_text'    => '&rarr;',
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		) ) );
	?>
</nav>
