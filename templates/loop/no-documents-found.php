<?php
/**
 * Displayed when no documents are found matching the current query.
 *
 * Override this template by copying it to yourtheme/documentate/loop/no-documents-found.php
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<p class="documentate-info"><?php _e( 'No documents were found matching your selection.', 'documentate' ); ?></p>
