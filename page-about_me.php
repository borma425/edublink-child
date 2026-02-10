<?php
/**
 * About Me Page Template
 * 
 * Template for displaying the About Me page
 * Uses Timber Twig template
 * 
 * @package EduBlink_Child
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if Timber is available
if ( ! class_exists( 'Timber\Timber' ) ) {
	echo 'Timber plugin is not installed.';
	return;
}

// Get Timber context
$context = Timber::context();

// Add theme directory URI to context
$context['theme_uri'] = get_stylesheet_directory_uri();

// Render Twig template
Timber::render( 'about-me.twig', $context );

