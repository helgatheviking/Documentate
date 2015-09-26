<?php
/**
 * Knowledgebase Template Functions
 *
 * @version     0.1-beta
 * @author      helgatheviking
 */

/**
 * Enables template debug mode
 */
function DOCUMENTATE_TEMPLATE_DEBUG_MODE() {
    if ( ! defined( 'DOCUMENTATE_TEMPLATE_DEBUG_MODE' ) ) {
        define( 'DOCUMENTATE_TEMPLATE_DEBUG_MODE', false );
    }
}
add_action( 'after_setup_theme', 'DOCUMENTATE_TEMPLATE_DEBUG_MODE', 20 );


/**
 * Get template part (for templates like the shop-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @since   1.0.0
 */
function documentate_get_template_part( $slug, $name = '' ) {

    $template_path = documentate_get_template_path();

    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/documentate/slug-name.php
    if ( $name && ! DOCUMENTATE_TEMPLATE_DEBUG_MODE ) {
        $template = locate_template( array( "{$slug}-{$name}.php", $template_path . "{$slug}-{$name}.php" ) );
    }

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( Docu()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
        $template = Docu()->plugin_path() . "/templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/documentate/slug.php
    if ( ! $template && ! DOCUMENTATE_TEMPLATE_DEBUG_MODE ) {
        $template = locate_template( array( "{$slug}.php", $template_path . "{$slug}.php" ) );
    }

    // Allow 3rd party plugin filter template file from their plugin
    if ( ( ! $template && DOCUMENTATE_TEMPLATE_DEBUG_MODE ) || $template ) {
        $template = apply_filters( 'documentate_get_template_part', $template, $slug, $name );
    }

    if ( $template ) {
        load_template( $template, false );
    }
}



/** Global ****************************************************************/

if ( ! function_exists( 'documentate_output_content_wrapper' ) ) {

    /**
     * Output the start of the page wrapper.
     *
     */
    function documentate_output_content_wrapper() {
        documentate_get_template( 'global/wrapper-start.php' );
    }
}
if ( ! function_exists( 'documentate_output_content_wrapper_end' ) ) {

    /**
     * Output the end of the page wrapper.
     *
     */
    function documentate_output_content_wrapper_end() {
        documentate_get_template( 'global/wrapper-end.php' );
    }
}

if ( ! function_exists( 'documentate_get_sidebar' ) ) {

    /**
     * Get the shop sidebar template.
     *
     */
    function documentate_get_sidebar() {
        documentate_get_template( 'global/sidebar.php' );
    }
}

if ( ! function_exists( 'documentate_breadcrumbs' ) ) {

    /**
     * Output the Documentate Breadcrumb
     */
    function documentate_breadcrumbs( $args = array() ) {

        $show = documentate_get_option( 'breadcrumbs_setting' );

        if( $show == 'show' ){
             $args = wp_parse_args( $args, apply_filters( 'documentate_breadcrumbs_defaults', array(
                'delimiter'   => '&nbsp;&#47;&nbsp;',
                'wrap_before' => '<nav class="documentate-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
                'wrap_after'  => '</nav>',
                'before'      => '',
                'after'       => '',
                'home'        => _x( 'Home', 'breadcrumb', 'documentate' )
            ) ) );

            $breadcrumbs = new Documentate_Breadcrumbs();

            if ( $args['home'] ) {
                $breadcrumbs->add_crumb( $args['home'], apply_filters( 'documentate_breadcrumbs_home_url', home_url() ) );
            }

            $args['breadcrumb'] = $breadcrumbs->generate();

            documentate_get_template( 'global/breadcrumbs.php', $args );           
        }

    }
}


if ( ! function_exists( 'document_search' ) ) {

    /**
     * Display document search form.
     *
     * For some reason simply adding get_document_search_form() to the hook doesn't seem to work
     */
    function document_search() {
        documentate_get_template( 'global/search.php' ); 
    }
}

if ( ! function_exists( 'get_document_search_form' ) ) {

    /**
     * Display document search form.
     *
     * Will first attempt to locate the document-searchform.php file in either the child or
     * the parent, then load it. If it doesn't exist, then the default search form
     * will be displayed.
     *
     * The default searchform uses html5.
     *
     * @subpackage  Forms
     * @param bool $echo (default: true)
     * @return string
     */
    function get_document_search_form( $echo = true  ) {
        ob_start();

        do_action( 'pre_get_document_search_form'  );

        documentate_get_template( 'document-searchform.php' );

        wp_enqueue_script( 'live-search' );
        wp_enqueue_script( 'documentate' );

        $form = apply_filters( 'get_document_search_form', ob_get_clean() );

        if ( $echo ) {
            echo $form;
        } else {
            return $form;
        }
    }
}

/** Single Document ********************************************************/


if ( ! function_exists( 'documentate_template_single_title' ) ) {

    /**
     * Output the Document title.
     *
     * @subpackage  Document
     */
    function documentate_template_single_title() {
        documentate_get_template( 'single-document/title.php' );
    }
}

if ( ! function_exists( 'documentate_template_single_content' ) ) {

    /**
     * Output the Document title.
     *
     * @subpackage  Document
     */
    function documentate_template_single_content() {
        documentate_get_template( 'single-document/content.php' );
    }
}

if ( ! function_exists( 'documentate_template_single_meta' ) ) {

    /**
     * Output the Document title.
     *
     * @subpackage  Document
     */
    function documentate_template_single_meta() {
        documentate_get_template( 'single-document/meta.php' );
    }
}

if ( ! function_exists( 'documentate_comments' ) ) {

    /**
     * Output the Review comments template.
     *
     * @subpackage  Document
     */
    function documentate_comments() {
        $display_comments = documentate_get_option( 'comments_setting' );
        documentate_get_template( 'single-document/comments.php', array( 'display_comments' => $display_comments ) );
    }
}

/** Loop ******************************************************************/

if ( ! function_exists( 'documentate_page_title' ) ) {

    /**
     * documentate_page_title function.
     *
     * @param  boolean $echo
     * @return string
     */
    function documentate_page_title( $echo = true ) {

        if ( is_search() ) {
            $page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'documentate' ), get_search_query() );

            if ( get_query_var( 'paged' ) )
                $page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'documentate' ), get_query_var( 'paged' ) );

        } elseif ( is_tax() ) {

            $page_title = single_term_title( "", false );

        } else {

            $archive_page_id = documentate_get_option( 'archive_page_id' );
            $page_title   = get_the_title( $archive_page_id );

        }

        $page_title = apply_filters( 'documentate_page_title', $page_title );

        if ( $echo )
            echo $page_title;
        else
            return $page_title;
    }
}

if ( ! function_exists( 'documentate_document_loop_start' ) ) {

    /**
     * Output the start of a document loop. By default this is a UL
     *
     * @param bool $echo
     * @return string
     */
    function documentate_document_loop_start( $echo = true, $class = 'documents' ) {
        ob_start();
        documentate_get_template( 'loop/loop-start.php' );
        if ( $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }
}
if ( ! function_exists( 'documentate_document_loop_end' ) ) {

    /**
     * Output the end of a document loop. By default this is a UL
     *
     * @param bool $echo
     * @return string
     */
    function documentate_document_loop_end( $echo = true ) {
        ob_start();

        documentate_get_template( 'loop/loop-end.php' );

        if ( $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }
}


if (  ! function_exists( 'documentate_template_loop_title' ) ) {

    /**
     * Show the document title in the document loop. By default this is an H3
     */
    function documentate_template_loop_title() {
        documentate_get_template( 'loop/title.php' );
    }
}

if (  ! function_exists( 'documentate_template_loop_excerpt' ) ) {

    /**
     * Show the document title in the document loop. By default this is an H3
     */
    function documentate_template_loop_excerpt() {
        documentate_get_template( 'loop/excerpt.php' );
    }
}

if ( ! function_exists( 'docu_cat_archive_description' ) ) {

    /**
     * Show an archive description on taxonomy archives
     *
     * @subpackage  Archives
     */
    function docu_cat_archive_description() {
        if ( is_tax( array( 'docu_cat', 'docu_tag' ) ) ) {
            $description = documentate_format_content( term_description() );
            if ( $description ) {
                echo '<div class="term-description">' . $description . '</div>';
            }
        }
    }
}
if ( ! function_exists( 'documentate_document_archive_description' ) ) {

    /**
     * Show a shop page description on document archives
     *
     * @subpackage  Archives
     */
    function documentate_document_archive_description() {
        if ( is_post_type_archive( 'documentate' ) && get_query_var( 'paged' ) == 0 ) {
            $archive_page_id   = documentate_get_option( 'archive_page_id' );
            $archive_page = get_post( $archive_page_id );

            if ( $archive_page ) {
                $description = documentate_format_content( $archive_page->post_content );
                if ( $description ) {
                    echo '<div class="page-description">' . $description . '</div>';
                }
            }
        }
    }
}

if ( ! function_exists( 'documentate_result_count' ) ) {

    /**
     * Output the result count text (Showing x - x of x results).
     *
     * @subpackage  Loop
     */
    function documentate_result_count() {
        documentate_get_template( 'loop/result-count.php' );
    }
}

if ( ! function_exists( 'documentate_pagination' ) ) {

    /**
     * Output the pagination.
     *
     * @subpackage  Loop
     */
    function documentate_pagination() {
        documentate_get_template( 'loop/pagination.php' );
    }
}

/** Subcategory ******************************************************************/

if ( ! function_exists( 'documentate_document_subcategories' ) ) {

    /**
     * Display document sub categories as thumbnails.
     *
     * @subpackage  Loop
     * @param array $args
     * @return null|boolean
     */
    function documentate_document_subcategories( $args = array() ) {
        global $wp_query;

        $defaults = array(
            'before'        => '',
            'after'         => '',
            'force_display' => false
        );

        $args = wp_parse_args( $args, $defaults );

        extract( $args );

        // Main query only
        if ( ! is_main_query() && ! $force_display ) {
            return;
        }

        // Don't show when, searching or when on page > 1 and ensure we're on an document archive
        if ( is_search() || is_paged() || ( ! is_document_category() && ! is_document_archive() ) ) {
            return;
        }

        // Check categories are enabled
        if ( is_document_archive() && documentate_get_option( 'archive_display' ) == 'documents' ) {
            return;
        }

        // Find the category + category parent, if applicable
        $term           = get_queried_object();
        $parent_id      = ! empty( $term->term_id ) ? $term->term_id : 0;

        if ( is_document_category() ) {
            $display_type = get_term_meta( $term->term_id, 'display_type', true );
            switch ( $display_type ) {
                case 'documents' :
                    return;
                break;
                case '' :
                    if ( documentate_get_option( 'category_display' ) == 'documents' ) {
                        return;
                    }
                break;
            }
        }

        // NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( http://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work
        $document_categories = get_categories( apply_filters( 'documentate_document_subcategories_args', array(
            'parent'       => $parent_id,
            'menu_order'   => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'taxonomy'     => 'docu_cat',
            'pad_counts'   => 1
        ) ) );

        if ( ! apply_filters( 'documentate_document_subcategories_hide_empty', false ) ) {
            $document_categories = wp_list_filter( $document_categories, array( 'count' => 0 ), 'NOT' );
        }

        if ( $document_categories ) {
            echo $before;

            foreach ( $document_categories as $category ) {
                documentate_get_template( 'content-docu_cat.php', array(
                    'category' => $category
                ) );
            }

            // If we are hiding documents disable the loop and pagination
            if ( is_document_category() ) {
                $display_type = get_term_meta( $term->term_id, 'display_type', true );
                switch ( $display_type ) {
                    case 'subcategories' :
                        $wp_query->post_count    = 0;
                        $wp_query->max_num_pages = 0;
                    break;
                    case '' :
                        if ( documentate_get_option( 'category_display' ) == 'subcategories' ) {
                            $wp_query->post_count    = 0;
                            $wp_query->max_num_pages = 0;
                        }
                    break;
                }
            }

            if ( is_document_archive() && documentate_get_option( 'archive_display' ) == 'subcategories' ) {
                $wp_query->post_count    = 0;
                $wp_query->max_num_pages = 0;
            }

            echo $after;

            return true;
        }
    }
}

if ( ! function_exists( 'documentate_subcategory_thumbnail' ) ) {

    /**
     * Show subcategory thumbnails.
     *
     * @param mixed $category
     * @subpackage  Loop
     */
    function documentate_subcategory_thumbnail( $category ) {
        $small_thumbnail_size   = apply_filters( 'single_document_small_thumbnail_size', 'document_catalog' );
        $dimensions             = array( 'width' => 300, 'height' => 300 );
        $thumbnail_id           = get_term_meta( $category->term_id, 'thumbnail_id', true  );

        if ( $thumbnail_id ) {
            $image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size  );
            $image = $image[0];
        } else {
            //$image = documentate_placeholder_img_src();
            $image = '';
        }

        if ( $image ) {
            // Prevent esc_url from breaking spaces in urls for image embeds
            // Ref: http://core.trac.wordpress.org/ticket/23605
            $image = str_replace( ' ', '%20', $image );

            echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
        }
    }
}

if ( ! function_exists( 'documentate_subcategory_documents' ) ) {

    /**
     * Show subcategory thumbnails.
     *
     * @param mixed $category
     * @subpackage  Loop
     */
    function documentate_subcategory_documents( $category ) {

        $number_docs = intval( documentate_get_option( 'docu_qty' ) );

        if( $number_docs > 0 ){
           
            if ( false === ( $documents = get_transient( 'documentate_posts_in_' . $category->slug ) ) ) {
                 $args = array(
                    'post_type' => 'document',
                    'posts_per_page' => $number_docs,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'docu_cat',
                            'field'    => 'slug',
                            'terms'    => $category->slug,
                        ),
                    ),
                );

                $documents = new WP_Query( apply_filters( 'documentate_category_documents', $args, $category ) );
                set_transient( 'documentate_posts_in_' . $category->slug, $documents );

            }

            if ( $documents->have_posts() ) :

                documentate_document_loop_start();

                while ( $documents->have_posts() ) : $documents->the_post();

                    documentate_get_template_part( 'content', 'document' ); 

                endwhile; // end of the loop. 

                documentate_document_loop_end();

            endif;

            wp_reset_postdata();
        }

    }
}