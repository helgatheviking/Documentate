<?php
/**
 * Single Document Comments
 *
 * @author  helgatheviking
 * @package Documentate/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( $display_comments ) : ?>

	<?php comments_template(); ?>

<?php endif; ?>