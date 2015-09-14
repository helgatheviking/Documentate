<?php
/**
 * The template for displaying document category thumbnails within loops.
 *
 * Override this template by copying it to yourtheme/documentate/content-docu_cat.php
 *
 * @author 		helgatheviking
 * @package 	Documentate/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<li class="document-category">
	<?php do_action( 'documentate_before_subcategory', $category ); ?>

	<a href="<?php echo get_term_link( $category->slug, 'docu_cat' ); ?>">

		<?php
			/**
			 * documentate_before_subcategory_title hook
			 *
			 * @hooked documentate_subcategory_thumbnail - 10
			 */
			do_action( 'documentate_before_subcategory_title', $category );
		?>

		<h2>
			<?php
				echo $category->name;

				if ( $category->count > 0 )
					echo apply_filters( 'documentate_subcategory_count_html', ' <mark class="count">' . sprintf( _n( '%s document', '%s documents', $category->count ), $category->count ) . '</mark>', $category );
			?>
		</h2>

		<?php
			/**
			 * documentate_after_subcategory_title hook
			 */
			do_action( 'documentate_after_subcategory_title', $category );
		?>

	</a>

	<?php do_action( 'documentate_after_subcategory', $category ); ?>
</li>
