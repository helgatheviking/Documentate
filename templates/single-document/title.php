<?php
/**
 * Single Document title
 *
 * @author  helgatheviking
 * @package Documentate/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<h1 itemprop="name" class="document_title entry-title"><?php the_title(); ?></h1>
