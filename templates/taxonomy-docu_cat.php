<?php
/**
 * The Template for displaying products in an document category. Simply includes the archive taxonomies template.
 *
 * Override this template by copying it to yourtheme/documentate/taxonomy-docu_cat.php
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
documentate_get_template( 'archive-document.php' );
