<?php
/**
 * The template for displaying a live document search form
 *
 * Override this template by copying it to yourtheme/documentate/document-searchform.php
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<form role="search" method="get" class="search-form documentate-document-search" action="<?php echo home_url( '/' ); ?>">
	<label>
		<span class="screen-reader-text"><?php _x( 'Search for documents', 'This string is a label for a search input that is only visible to screen readers.', 'documentate' ); ?></span>
		<input type="text" placeholder="<?php _x( 'Search Documents...', 'this string is the placeholder on the search input', 'documentate' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_e( 'Press Enter to submit your search', 'documentate' ); ?>" />
	</label>
	<input type="hidden" value="document" name="post_type" />
</form>