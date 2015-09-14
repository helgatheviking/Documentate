<?php
/**
 * Single document excerpt
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<div class="excerpt" itemprop="excerpt">
	<?php echo apply_filters( 'documentate_document_excerpt', $post->post_excerpt ) ?>
</div>
