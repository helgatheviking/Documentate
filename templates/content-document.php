<?php
/**
 * The template for displaying document content within loops.
 *
 * Override this template by copying it to yourtheme/documentate/content-documentate.php
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<li <?php post_class(); ?>>

	<?php do_action( 'documentate_before_document_loop_item' ); ?>

	<a href="<?php the_permalink(); ?>">

		<?php
			/**
			 * documentate_before_document_loop_item_title hook
			 */
			do_action( 'documentate_before_document_loop_item_title' );

			/**
			 * documentate_document_loop_item_title hook
			 *
			 * @hooked documentate_template_loop_title - 10
			 */
			do_action( 'documentate_document_loop_item_title' );

			/**
			 * documentate_after_document_loop_item_title hook
			 */
			do_action( 'documentate_after_document_loop_item_title' );
		?>

	</a>

	<?php

		/**
		 * documentate_after_document_loop_item hook
		 * @hooked documentate_template_loop_excerpt - 10
		 */
		do_action( 'documentate_after_document_loop_item' );

	?>

</li>
