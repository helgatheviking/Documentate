<?php
/**
 * Single Document Content
 *
 * @author  helgatheviking
 * @package Documentate/Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

the_content();
