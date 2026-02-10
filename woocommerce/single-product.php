<?php
/**
 * Custom Single Product Template - Timber/Twig
 * 
 * Custom single product page template using Timber/Twig
 *
 * @package EduBlink_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Check if Timber is available
if ( ! class_exists( 'Timber\Timber' ) ) {
	echo 'Timber plugin is not installed.';
	return;
}

// Check if WooCommerce is active
if ( ! function_exists( 'wc_get_product' ) ) {
	echo 'WooCommerce is not active';
	return;
}

global $product;

// Get product object if not already set
if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
	$product = wc_get_product( get_the_ID() );
}

if ( ! $product ) {
	return;
}

// Get Timber context
$context = Timber::context();

// Add theme directory URI to context
$context['theme_uri'] = get_stylesheet_directory_uri();

// Get product post using Timber
$product_post = Timber::get_post( get_the_ID() );
$context['product'] = $product_post;

// Get product basic data
$context['product_id'] = $product->get_id();
$context['product_title'] = $product->get_name();
$context['product_content'] = $product->get_description();
$context['product_excerpt'] = $product->get_short_description();

// Get product prices
$context['regular_price'] = $product->get_regular_price() ? floatval( $product->get_regular_price() ) : null;
$context['sale_price'] = $product->get_sale_price() ? floatval( $product->get_sale_price() ) : null;
$context['price'] = $product->get_price() ? floatval( $product->get_price() ) : null;

// Format prices for display
if ( $context['regular_price'] ) {
	$context['regular_price_formatted'] = number_format( $context['regular_price'], 2, '.', ',' );
} else {
	$context['regular_price_formatted'] = null;
}

if ( $context['sale_price'] ) {
	$context['sale_price_formatted'] = number_format( $context['sale_price'], 2, '.', ',' );
} else {
	$context['sale_price_formatted'] = null;
}

if ( $context['price'] ) {
	$context['price_formatted'] = number_format( $context['price'], 2, '.', ',' );
} else {
	$context['price_formatted'] = null;
}

// Calculate discount percentage
if ( $context['sale_price'] && $context['regular_price'] && $context['regular_price'] > 0 ) {
	$context['discount_percent'] = round( ( ( $context['regular_price'] - $context['sale_price'] ) / $context['regular_price'] ) * 100 );
} else {
	$context['discount_percent'] = 0;
}

// Check if product is on sale
$context['is_on_sale'] = $product->is_on_sale();
$context['is_in_stock'] = $product->is_in_stock();
$context['stock_status'] = $product->get_stock_status();
$context['stock_quantity'] = $product->get_stock_quantity();

// Get product images
$context['product_image_id'] = $product->get_image_id();
$context['product_image'] = wp_get_attachment_image_url( $context['product_image_id'], 'full' );
if ( ! $context['product_image'] ) {
	$context['product_image'] = $context['theme_uri'] . '/assets/img/book.png'; // Fallback image
}

// Get product gallery images
$gallery_ids = $product->get_gallery_image_ids();
$context['gallery_images'] = array();
if ( ! empty( $gallery_ids ) ) {
	foreach ( $gallery_ids as $gallery_id ) {
		$image_url = wp_get_attachment_image_url( $gallery_id, 'full' );
		if ( $image_url ) {
			$context['gallery_images'][] = $image_url;
		}
	}
}

// If no gallery images, use main product image
if ( empty( $context['gallery_images'] ) && $context['product_image'] ) {
	$context['gallery_images'][] = $context['product_image'];
}

// Get product categories
$categories = wc_get_product_terms( $context['product_id'], 'product_cat', array( 'fields' => 'all' ) );
$context['categories'] = $categories && ! is_wp_error( $categories ) ? $categories : array();

// Get product tags
$tags = wc_get_product_terms( $context['product_id'], 'product_tag', array( 'fields' => 'all' ) );
$context['tags'] = $tags && ! is_wp_error( $tags ) ? $tags : array();

// Get product rating
$context['rating'] = $product->get_average_rating();
$context['rating_count'] = $product->get_rating_count();

// Get product meta (for books)
$context['book_pages'] = get_post_meta( $context['product_id'], '_book_pages', true );
$context['book_available_count'] = get_post_meta( $context['product_id'], '_book_available_count', true );

// Get product updated date
$context['product_updated'] = get_the_modified_date( 'F j, Y', $context['product_id'] );

// Get product author (if linked to Tutor course)
$context['product_author'] = null;
if ( function_exists( 'tutor_utils' ) ) {
	$course_id = tutor_utils()->get_course_id_by_product( $context['product_id'] );
	if ( $course_id ) {
		$instructors = tutor_utils()->get_instructors_by_course( $course_id );
		if ( ! empty( $instructors ) && isset( $instructors[0]->ID ) ) {
			$context['product_author'] = Timber::get_user( $instructors[0]->ID );
		}
	}
}

// Get product type
$context['product_type'] = $product->get_type();

// Get first 5 features from product description or short description
$context['first_five_features'] = array();
$description_text = $context['product_content'] ?: $context['product_excerpt'];
if ( $description_text ) {
	// Split by newlines or bullets and take first 5
	$lines = preg_split( '/\n|•|·/', $description_text );
	$features = array_filter( array_map( 'trim', $lines ) );
	$context['first_five_features'] = array_slice( $features, 0, 5 );
}

// Get students count (if product is linked to a course)
$context['students_count'] = 0;
if ( function_exists( 'tutor_utils' ) ) {
	$course_id = tutor_utils()->get_course_id_by_product( $context['product_id'] );
	if ( $course_id ) {
		$context['students_count'] = tutor_utils()->count_enrolled_users_by_course( $course_id );
	}
}

// Check if product has bundles
$context['has_bundles'] = false;
$context['bundle_items'] = array();
$context['bundle_items_count'] = 0;

// Check if product type is bundle or has bundle items in database
if ( $product->get_type() === 'bundle' || class_exists( 'AsanaPlugins\WooCommerce\ProductBundles\Plugin' ) ) {
	global $wpdb;
	$bundle_items = $wpdb->get_results( $wpdb->prepare(
		"SELECT product_id, quantity FROM {$wpdb->prefix}asnp_wepb_simple_bundle_items WHERE bundle_id = %d",
		$context['product_id']
	) );
	
	if ( ! empty( $bundle_items ) ) {
		$context['has_bundles'] = true;
		$context['bundle_items_count'] = count( $bundle_items );
		
		// Get bundle items details
		foreach ( $bundle_items as $item ) {
			$bundle_product = wc_get_product( $item->product_id );
			if ( $bundle_product && $bundle_product->is_visible() ) {
				$bundle_item_data = array(
					'id' => $item->product_id,
					'title' => $bundle_product->get_name(),
					'quantity' => $item->quantity,
					'image' => wp_get_attachment_image_url( $bundle_product->get_image_id(), 'full' ),
					'link' => get_permalink( $item->product_id ),
				);
				
				// Get course data if linked to Tutor
				if ( function_exists( 'tutor_utils' ) ) {
					$course_id = tutor_utils()->get_course_id_by_product( $item->product_id );
					if ( $course_id ) {
						$bundle_item_data['duration'] = get_tutor_course_duration_context( $course_id );
						$bundle_item_data['lesson_count'] = tutor_utils()->get_lesson_count_by_course( $course_id );
						$bundle_item_data['topics_count'] = $bundle_item_data['lesson_count'];
					}
				}
				
				$context['bundle_items'][] = $bundle_item_data;
			}
		}
	}
}

// Determine which template to use
$template_name = $context['has_bundles'] ? 'single-product-bundle.twig' : 'single-product.twig';

// Render the template
Timber::render( $template_name, $context );
