<?php
/**
 * The template for displaying document content in the single-document.php template
 *
 * Override this template by copying it to yourtheme/wp_knowledgebase/content-single-document.php
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * documentate_before_single_document hook
	 *
	 */
	 do_action( 'documentate_before_single_document' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="https://schema.org/Document" id="documentate-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * documentate_single_document_content hook
		 *
		 * @hooked documentate_template_single_title 10
		 * @hooked documentate_template_single_content 20
		 * @hooked documentate_template_single_meta 30
		 */
		do_action( 'documentate_single_document_content' );

	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #documentate-<?php the_ID(); ?> -->

<?php
	/**
	 * documentate_after_single_document hook
	 *
	 * @hooked documentate_comments 10
	 * @hooked documentate_template_single_content 20
	 * @hooked documentate_template_single_meta 30
	 */
	do_action( 'documentate_after_single_document' );