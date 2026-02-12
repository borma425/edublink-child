<?php
/**
 * Child Theme Footer
 *
 * Global footer wrapper that renders the Timber-based footer
 * and preserves WordPress footer hooks.
 *
 * @package EduBlink_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Render the new Timber footer component, or fall back if Timber is unavailable.
if ( class_exists( 'Timber\Timber' ) ) {
	Timber\Timber::render( 'components/footer.twig', Timber\Timber::context() );
} else {
	// Fallback: keep parent theme footer action if needed.
	do_action( 'edublink_footer' );
}

/**
 * Keep all scripts printed in the footer by WordPress, plugins, and the parent theme.
 */
wp_footer();

?>
</body>
</html>


