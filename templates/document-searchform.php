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
<form role="search" method="get" class="searchform documentate-document-search" action="<?php echo home_url( '/' ); ?>" autocomplete="off">
	<label class="screen-reader-text" for="s"><?php _e( 'Search for:', 'documentate' ); ?></label>
	<input type="text" placeholder="<?php _e( 'Search Documents...', 'documentate' ); ?>" value="<?php echo get_search_query(); ?>" name="s" id="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'documentate' ); ?>" />
	<input type="hidden" value="document" name="post_type" />
</form>