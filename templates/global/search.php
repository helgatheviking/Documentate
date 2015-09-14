<?php
/**
 * Search Section
 *
 * @author      helgatheviking
 * @package 	Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( documentate_get_option( 'search_setting' ) == 'show' ){?>
	<div class="documentate-search">
		<?php get_document_search_form(); ?>
	</div>
	<?php
}
