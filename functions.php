<?php
/**
 * EduBlink Child Theme functions and definitions
 *
 * @package EduBlink_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load Composer dependencies
 */
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

add_filter('show_admin_bar', '__return_false');

/**
 * Initialize Timber
 */
if ( class_exists( 'Timber\Timber' ) ) {
	Timber\Timber::init();
	
	/**
	 * Set Timber locations
	 */
	Timber::$dirname = array( 'views', 'templates' );
	
	/**
	 * Add WordPress conditional functions to Timber context
	 */
	add_filter( 'timber/context', 'edublink_child_add_to_context' );
	function edublink_child_add_to_context( $context ) {
		$context['is_front_page'] = is_front_page();
		$context['is_home'] = is_home();
		$context['is_user_logged_in'] = is_user_logged_in();
		
		if ( is_user_logged_in() ) {
			$context['user'] = Timber::get_user( get_current_user_id() );
		}
		
		// Add main menu (القائمة الرئيسية - ID: 28)
		$main_menu = Timber::get_menu( 28 );
		if ( $main_menu ) {
			$context['main_menu'] = $main_menu;
		}
		
		// Add courses archive URL
		if ( function_exists( 'tutor_utils' ) ) {
			$context['courses_archive_url'] = tutor_utils()->course_archive_page_url();
		} else {
			$context['courses_archive_url'] = home_url( '/courses/' );
		}
		
		return $context;
	}
}

/* ==========================================================================
   FORCE FRONT-PAGE.PHP & DISABLE ELEMENTOR (FINAL NUCLEAR SOLUTION)
   ========================================================================== */

/**
 * 1. Force load front-page.php using the strongest filter (template_include)
 */
add_filter( 'template_include', 'edublink_child_force_front_page_template', 99999 );

function edublink_child_force_front_page_template( $template ) {
    // Check if we are on the front page or the specific page ID 9834 found in your HTML
    if ( is_front_page() || is_home() || get_the_ID() == 9834 ) {
        $custom_front = get_stylesheet_directory() . '/front-page.php';
        
        if ( file_exists( $custom_front ) ) {
            return $custom_front;
        }
    }
    return $template;
}

/**
 * 2. Completely Dequeue Elementor Styles & Scripts on Front Page
 * Updated with specific IDs found in your guest HTML analysis
 */
add_action( 'wp_enqueue_scripts', 'edublink_child_unload_elementor_assets', 99999 );

function edublink_child_unload_elementor_assets() {
    // Only run on front page
    if ( is_front_page() || is_home() || get_the_ID() == 9834 ) {
        
        // Remove Elementor Core
        wp_dequeue_script( 'elementor-frontend' );
        wp_dequeue_style( 'elementor-frontend' );
        wp_dequeue_style( 'elementor-icons' );
        wp_dequeue_style( 'elementor-global' );
        
        // Remove Specific Elementor Files found in Guest View
        wp_dequeue_style( 'elementor-post-24541' ); // Global Kit
        wp_dequeue_style( 'elementor-post-9834' );  // Specific Page Style
        
        // Remove Elementor Pro
        wp_dequeue_script( 'elementor-pro-frontend' );
        wp_dequeue_style( 'elementor-pro' );
        
        // Remove Header Footer Elementor (HFE)
        wp_dequeue_script( 'hfe-frontend-js' );
        wp_dequeue_style( 'hfe-style' );
        wp_dequeue_style( 'hfe-widgets-style' );
        
        // Remove EduBlink Theme specific Elementor styles
        wp_dequeue_style( 'edublink-elementor' );
        wp_dequeue_style( 'edublink-style' ); 
        
        // Remove Google Fonts loaded by Elementor
        add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
    }
}

/**
 * 3. Force Disable Cache Headers for Front Page
 * This attempts to tell browsers and proxies NOT to cache the homepage
 */
add_action( 'send_headers', 'edublink_child_prevent_caching_front_page' );
function edublink_child_prevent_caching_front_page() {
    if ( is_front_page() || is_home() || get_the_ID() == 9834 ) {
        header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
    }
}

/**
 * 4. Clean Body Classes
 * Removes 'elementor-page' classes to prevent any residual CSS from applying
 */
add_filter( 'body_class', 'edublink_child_clean_body_classes', 999 );
function edublink_child_clean_body_classes( $classes ) {
    if ( is_front_page() || is_home() || get_the_ID() == 9834 ) {
        $remove_classes = array( 
            'elementor-default', 
            'elementor-kit-24541', 
            'elementor-page', 
            'elementor-page-9834' 
        );
        $classes = array_diff( $classes, $remove_classes );
    }
    return $classes;
}

/**
 * 5. Disable Elementor Locations Logic
 */
add_action( 'wp', 'edublink_child_disable_elementor_locations', 0 );

function edublink_child_disable_elementor_locations() {
    if ( is_front_page() || is_home() || get_the_ID() == 9834 ) {
        // Stop Elementor Theme Builder
        add_filter( 'elementor/theme/get_location_templates', '__return_empty_array', 999 );
        add_filter( 'elementor/theme/get_location_template_id', '__return_false', 999 );
        
        // Stop Header Footer Elementor Plugin
        add_filter( 'hfe_header_enabled', '__return_false' );
        add_filter( 'hfe_footer_enabled', '__return_false' );
        add_filter( 'enable_hfe_render_header', '__return_false' );
        add_filter( 'enable_hfe_render_footer', '__return_false' );
        
        // Remove Theme Hooks
        remove_all_actions( 'edublink_header' ); 
        remove_all_actions( 'edublink_footer' );
    }
}

/* ==========================================================================
   OTHER ASSETS & WOOCOMMERCE
   ========================================================================== */

/**
 * Enqueue parent and child theme styles
 */
function edublink_child_enqueue_styles() {
	wp_enqueue_style( 'edublink-parent-style', get_template_directory_uri() . '/style.css', array(), '2.0.8' );
	wp_enqueue_style( 'edublink-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'edublink-parent-style' ), wp_get_theme()->get( 'Version' ) );
	
    // Custom logic for products
	if ( is_product() ) {
		wp_enqueue_style( 'edublink-custom-product-style', get_stylesheet_directory_uri() . '/custom_product.css', array( 'edublink-child-style' ), wp_get_theme()->get( 'Version' ) );
	}
    // Custom logic for archives
	if ( is_shop() || is_product_category() || is_product_tag() ) {
		wp_enqueue_style( 'edublink-custom-product-archive-style', get_stylesheet_directory_uri() . '/custom_product_archive.css', array( 'edublink-child-style' ), wp_get_theme()->get( 'Version' ) );
	}
    // Custom logic for Tutor LMS
	if ( function_exists( 'tutor_utils' ) ) {
		$course_post_type = tutor()->course_post_type;
		if ( is_singular( $course_post_type ) || get_post_type() === $course_post_type ) {
			wp_enqueue_style( 'edublink-custom-course-style', get_stylesheet_directory_uri() . '/custom_course.css', array( 'edublink-child-style' ), wp_get_theme()->get( 'Version' ) );
		}
		if ( is_post_type_archive( $course_post_type ) || is_tax( 'course-category' ) || is_tax( 'course-tag' ) ) {
			wp_enqueue_style( 'edublink-custom-course-archive-style', get_stylesheet_directory_uri() . '/custom_course_archive.css', array( 'edublink-child-style' ), wp_get_theme()->get( 'Version' ) );
			wp_dequeue_style( 'tutor-frontend' );
			wp_dequeue_style( 'tutor' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'edublink_child_enqueue_styles', 99 );

/**
 * Enqueue global assets
 */
function edublink_child_enqueue_global_assets() {
	$global_css = get_stylesheet_directory() . '/assets/global/styles.css';
	$global_js = get_stylesheet_directory() . '/assets/global/script.js';
	
	if ( file_exists( $global_css ) ) {
		wp_enqueue_style( 'edublink-global-styles', get_stylesheet_directory_uri() . '/assets/global/styles.css', array( 'edublink-child-style' ), filemtime( $global_css ) );
	}
	if ( file_exists( $global_js ) ) {
		wp_enqueue_script( 'edublink-global-scripts', get_stylesheet_directory_uri() . '/assets/global/script.js', array( 'jquery' ), filemtime( $global_js ), true );
	}
}
add_action( 'wp_enqueue_scripts', 'edublink_child_enqueue_global_assets', 100 );

/**
 * Ensure WooCommerce scripts are loaded for AJAX add to cart
 */
function edublink_child_enqueue_woocommerce_scripts() {
	if ( class_exists( 'WooCommerce' ) && is_front_page() ) {
		// Ensure WooCommerce add to cart script is loaded
		if ( ! wp_script_is( 'wc-add-to-cart', 'enqueued' ) ) {
			wp_enqueue_script( 'wc-add-to-cart' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'edublink_child_enqueue_woocommerce_scripts', 101 );

/**
 * Dynamic Assets Loader
 */
function edublink_child_load_page_assets() {
	$assets_dir = get_stylesheet_directory() . '/assets';
	$assets_uri = get_stylesheet_directory_uri() . '/assets';
	$page_type = '';
	
	if ( is_404() ) $page_type = '404';
	elseif ( is_front_page() ) $page_type = 'home';
	elseif ( is_page( 'about_me' ) || is_page_template( 'page-about_me.php' ) ) $page_type = 'about-me';
	elseif ( is_product() ) {
		// Check if product has bundles
		global $wpdb;
		$product_id = get_the_ID();
		$has_bundles = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}asnp_wepb_simple_bundle_items WHERE bundle_id = %d",
			$product_id
		) );
		$page_type = ( $has_bundles > 0 ) ? 'single-product-bundle' : 'single-product';
	}
	elseif ( is_shop() || is_product_category() || is_product_tag() ) $page_type = 'product_archive';
	elseif ( is_cart() ) $page_type = 'cart';
	elseif ( is_checkout() ) $page_type = 'checkout';
	elseif ( function_exists( 'tutor_utils' ) ) {
		$course_post_type = tutor()->course_post_type;
		if ( is_singular( $course_post_type ) ) $page_type = 'single_course';
		elseif ( is_post_type_archive( $course_post_type ) || is_tax( 'course-category' ) ) $page_type = 'course_archive';
	}
	
	if ( empty( $page_type ) ) {
		$template = get_page_template_slug();
		if ( ! empty( $template ) ) $page_type = str_replace( array( '.php', '-', '/' ), array( '', '_', '_' ), basename( $template ) );
		// Also check page slug
		if ( empty( $page_type ) && is_page() ) {
			$page_slug = get_post_field( 'post_name', get_the_ID() );
			if ( $page_slug === 'about_me' ) $page_type = 'about-me';
		}
	}
	
	if ( ! empty( $page_type ) && is_dir( $assets_dir . '/' . $page_type ) ) {
		$css_file = $assets_dir . '/' . $page_type . '/style.css';
		$js_file = $assets_dir . '/' . $page_type . '/script.js';
		
		if ( file_exists( $css_file ) ) {
			wp_enqueue_style( 'edublink-' . $page_type . '-style', $assets_uri . '/' . $page_type . '/style.css', array( 'edublink-child-style' ), filemtime( $css_file ) );
		}
		if ( file_exists( $js_file ) ) {
			wp_enqueue_script( 'edublink-' . $page_type . '-script', $assets_uri . '/' . $page_type . '/script.js', array( 'jquery' ), filemtime( $js_file ), true );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'edublink_child_load_page_assets', 100 );

/**
 * Remove WooCommerce CSS
 */
function edublink_child_remove_woocommerce_styles() {
	if ( is_product() || is_shop() || is_product_category() || is_product_tag() ) {
		wp_dequeue_style( 'woocommerce-general' );
		wp_dequeue_style( 'woocommerce-layout' );
		wp_dequeue_style( 'woocommerce-smallscreen' );
		wp_dequeue_style( 'edublink-woocommerce' );
	}
}
add_action( 'wp_enqueue_scripts', 'edublink_child_remove_woocommerce_styles', 999 );

/**
 * Override WooCommerce Templates
 */
function edublink_child_override_woocommerce_templates( $template, $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( 'cart/cart.php' === $template_name ) $child_template = get_stylesheet_directory() . '/woocommerce/cart/cart.php';
	elseif ( 'content-single-product.php' === $template_name ) $child_template = get_stylesheet_directory() . '/woocommerce/content-single-product.php';
	elseif ( 'single-product/tabs/tabs.php' === $template_name ) $child_template = get_stylesheet_directory() . '/woocommerce/single-product/tabs/tabs.php';
	
	if ( isset($child_template) && file_exists( $child_template ) ) return $child_template;
	return $template;
}
add_filter( 'wc_get_template', 'edublink_child_override_woocommerce_templates', 5, 5 );
add_filter( 'woocommerce_locate_template', 'edublink_child_override_woocommerce_templates', 1, 4 );

/**
 * Override Course Archive
 */
function edublink_child_override_course_archive_template( $template ) {
	if ( function_exists( 'tutor_utils' ) ) {
		$course_post_type = tutor()->course_post_type;
		$post_type = get_query_var( 'post_type' );
		$course_category = get_query_var( 'course-category' );
		
		if ( ( is_post_type_archive( $course_post_type ) || ( ! empty( $post_type ) && in_array( $course_post_type, (array) $post_type, true ) ) || ! empty( $course_category ) ) && is_archive() ) {
			$child_template = get_stylesheet_directory() . '/archive-courses.php';
			if ( file_exists( $child_template ) ) return $child_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'edublink_child_override_course_archive_template', 999 );

/**
 * Disable Elementor Product Templates
 */
function edublink_child_disable_elementor_product_mods() {
	if ( is_product() ) {
		if ( class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
			add_filter( 'elementor/theme/get_location_templates', '__return_empty_array', 999 );
			add_filter( 'elementor/theme/get_location_template_id', '__return_false', 999 );
		}
        add_filter( 'wpr_theme_builder_template_id', '__return_false', 999 );
        add_filter( 'wpr_theme_builder_should_render', '__return_false', 999 );
	}
}
add_action( 'template_redirect', 'edublink_child_disable_elementor_product_mods', 1 );

/**
 * Remove PROMO BAR
 */
function edublink_child_remove_promo_bar_enhanced() {
	if ( ! is_product() ) return;
	?>
	<style id="edublink-remove-promo-bar">
		#promo-bar, .promo-bar, [id*="promo"], [class*="promo-bar"], .promo-inner, .promo-left, .promo-timer, .promo-btn { display: none !important; visibility: hidden !important; height: 0 !important; overflow: hidden !important; }
	</style>
	<script>
	(function() {
		function removePromoBar() {
			const selectors = ['#promo-bar', '.promo-bar', '[id*="promo"]', '[class*="promo-bar"]'];
			selectors.forEach(function(s) { document.querySelectorAll(s).forEach(el => el.remove()); });
		}
		if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', removePromoBar);
		else removePromoBar();
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'edublink_child_remove_promo_bar_enhanced', 999 );

/* ==========================================================================
   BOOK PRODUCT METABOX
   ========================================================================== */

/**
 * Add custom metabox for book products
 */
function edublink_child_add_book_metabox() {
	add_meta_box(
		'edublink_book_details',
		'تفاصيل الكتاب',
		'edublink_child_book_metabox_callback',
		'product',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'edublink_child_add_book_metabox' );

/**
 * Metabox callback function
 */
function edublink_child_book_metabox_callback( $post ) {
	// Add nonce for security
	wp_nonce_field( 'edublink_book_metabox_nonce', 'edublink_book_metabox_nonce' );
	
	// Get current values
	$book_pages = get_post_meta( $post->ID, '_book_pages', true );
	$book_available_count = get_post_meta( $post->ID, '_book_available_count', true );
	
	?>
	<div class="edublink-book-metabox" style="padding: 10px 0;">
		<p>
			<label for="book_pages" style="display: block; margin-bottom: 5px; font-weight: 600;">
				عدد الصفحات:
			</label>
			<input 
				type="number" 
				id="book_pages" 
				name="book_pages" 
				value="<?php echo esc_attr( $book_pages ); ?>" 
				placeholder="مثال: 260"
				style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
				min="0"
			/>
			<span style="display: block; margin-top: 5px; color: #666; font-size: 12px;">
				أدخل عدد صفحات الكتاب
			</span>
		</p>
		
		<p>
			<label for="book_available_count" style="display: block; margin-bottom: 5px; font-weight: 600;">
				العدد المتوفر:
			</label>
			<input 
				type="number" 
				id="book_available_count" 
				name="book_available_count" 
				value="<?php echo esc_attr( $book_available_count ); ?>" 
				placeholder="مثال: 40"
				style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
				min="0"
			/>
			<span style="display: block; margin-top: 5px; color: #666; font-size: 12px;">
				أدخل عدد النسخ المتوفرة من الكتاب
			</span>
		</p>
	</div>
	<?php
}

/**
 * Save metabox data
 */
function edublink_child_save_book_metabox( $post_id ) {
	// Check if nonce is set
	if ( ! isset( $_POST['edublink_book_metabox_nonce'] ) ) {
		return;
	}
	
	// Verify nonce
	if ( ! wp_verify_nonce( $_POST['edublink_book_metabox_nonce'], 'edublink_book_metabox_nonce' ) ) {
		return;
	}
	
	// Check if this is an autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	// Check user permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	// Check if this is a product
	if ( get_post_type( $post_id ) !== 'product' ) {
		return;
	}
	
	// Save book pages
	if ( isset( $_POST['book_pages'] ) ) {
		$book_pages = sanitize_text_field( $_POST['book_pages'] );
		update_post_meta( $post_id, '_book_pages', $book_pages );
	} else {
		delete_post_meta( $post_id, '_book_pages' );
	}
	
	// Save book available count
	if ( isset( $_POST['book_available_count'] ) ) {
		$book_available_count = sanitize_text_field( $_POST['book_available_count'] );
		update_post_meta( $post_id, '_book_available_count', $book_available_count );
	} else {
		delete_post_meta( $post_id, '_book_available_count' );
	}
}
add_action( 'save_post', 'edublink_child_save_book_metabox' );
add_action( 'woocommerce_process_product_meta', 'edublink_child_save_book_metabox' );