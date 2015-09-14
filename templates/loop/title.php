<?php
/**
 * Document loop title
 *
 * @author      helgatheviking
 * @package     Documentate/Templates
 * @version     0.1-beta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<h3 itemprop="name" class="document_title entry-title"><?php the_title(); ?></h3>
