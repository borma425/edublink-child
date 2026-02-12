<?php
/**
 * Child Theme Header
 *
 * Global header wrapper that keeps WordPress head hooks
 * and renders the Timber-based header component.
 *
 * @package EduBlink_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	/**
	 * Keep all styles/scripts/meta injected by WordPress, plugins, and the parent theme.
	 */
	wp_head();
	?>
</head>
<body <?php body_class(); ?>>
<?php
// Ensure body open hook for analytics, etc.
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}

// Render the new Timber header component, or fall back if Timber is unavailable.
if ( class_exists( 'Timber\Timber' ) ) {
	Timber\Timber::render( 'components/header.twig', Timber\Timber::context() );
} else {
	// Fallback: keep parent theme header action if needed.
	do_action( 'edublink_header' );
}

/**
 * Note:
 * We intentionally do NOT open or close additional layout wrappers here
 * so that existing page templates and CSS structure remain unchanged.
 */


