<?php
/**
 * Single Document Meta
 *
 * @author  helgatheviking
 * @package Documentate/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if( $taglist = get_the_term_list( $post->ID, 'docu_tag', '<ul class="entry-tags"><li>', '</li><li>', '</li></ul>'  ) ) { ?>
	<div class="entry-meta"><?php echo $taglist; ?></div>
<?php }