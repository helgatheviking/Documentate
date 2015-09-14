<?php
/**
 * The Template for displaying all single documents.
 *
 * Override this template by copying it to yourtheme/wp_knowledgebase/single-document.php
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header( 'document' ); ?>

    <?php
        /**
         * documentate_before_main_content hook
         *
         * @hooked documentate_output_content_wrapper - 10 (outputs opening divs for the content)
         * @hooked documentate_breadcrumb - 20
         */
        do_action( 'documentate_before_main_content' );

    ?>

        <?php while ( have_posts() ) : the_post(); ?>

            <?php documentate_get_template_part( 'content', 'single-document' ); ?>

        <?php endwhile; // end of the loop. ?>

    <?php
        /**
         * documentate_after_main_content hook
         *
         * @hooked documentate_output_content_wrapper_end - 10 (outputs closing divs for the content)
         */
        do_action( 'documentate_after_main_content' );
    ?>

    <?php
        /**
         * documentate_sidebar hook
         *
         * @hooked documentate_get_sidebar - 10
         */
        do_action( 'documentate_sidebar' );
    ?>

<?php get_footer( 'document' ); ?>
