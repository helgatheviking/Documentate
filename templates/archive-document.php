<?php
/**
 * The Template for displaying document archives, including the main document page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/documentate/archive-document.php
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     2.0.0
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

        <?php if ( apply_filters( 'documentate_show_page_title', true ) ) : ?>

            <h1 class="page-title"><?php documentate_page_title(); ?></h1>

        <?php endif; ?>

        <?php
            /**
             * documentate_archive_description hook
             *
             * @hooked docu_cat_archive_description - 10
             * @hooked documentate_document_archive_description - 10
             */
            do_action( 'documentate_archive_description' );
        ?>

        <?php if ( have_posts() ) : ?>

            <?php
                /**
                 * documentate_before_document_loop hook
                 */
                do_action( 'documentate_before_document_loop' );
            ?>

           <?php documentate_document_loop_start(); ?>

                <?php documentate_document_subcategories(); ?>

                <?php while ( have_posts() ) : the_post(); ?>

                <?php documentate_get_template_part( 'content', 'document' ); ?>

                <?php endwhile; // end of the loop. ?>

            <?php documentate_document_loop_end(); ?>

            <?php
                /**
                 * documentate_after_document_loop hook
                 *
                 * @hooked documentate_pagination - 10
                 */
                do_action( 'documentate_after_document_loop' );
            ?>

        <?php elseif ( ! documentate_document_subcategories( array( 'before' => documentate_document_loop_start( false ), 'after' => documentate_document_loop_end( false ) ) ) ) : ?>

            <?php documentate_get_template( 'loop/no-documents-found.php' ); ?>

        <?php endif; ?>

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