<?php
/**
 * Custom Single Product Template - Empty Design
 * 
 * Custom product page template with different HTML elements and classes
 *
 * @package EduBlink_Child
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Get product object if not already set
if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
	$product = wc_get_product( get_the_ID() );
}

if ( ! $product ) {
	return;
}

// Get product data
$product_id = get_the_ID();
$product_title = get_the_title();
$product_content = get_the_content();
$product_excerpt = get_the_excerpt();
$product_price = $product->get_price_html();
$product_regular_price = $product->get_regular_price();
$product_sale_price = $product->get_sale_price();
$product_sku = $product->get_sku();
$product_weight = $product->get_weight();
$product_dimensions = $product->get_dimensions();
$product_categories = wc_get_product_terms( $product_id, 'product_cat', array( 'fields' => 'all' ) );
$product_tags = wc_get_product_terms( $product_id, 'product_tag', array( 'fields' => 'all' ) );
$product_rating = $product->get_average_rating();
$product_rating_count = $product->get_rating_count();
$product_stock_status = $product->get_stock_status();
$product_availability = $product->get_availability();
$product_image_id = $product->get_image_id();
$product_gallery_ids = $product->get_gallery_image_ids();
$product_type = $product->get_type();

// Check if product is on sale
$is_on_sale = $product->is_on_sale();
$is_in_stock = $product->is_in_stock();

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<!-- Custom Product Header -->
<section class="custom-product-header">
	<h1 class="custom-product-title"><?php echo esc_html( $product_title ); ?></h1>
</section>

<!-- Custom Product Images -->
<section class="custom-product-images-section">
	<?php if ( $product_image_id ) : ?>
		<div class="custom-product-main-image">
			<?php echo wp_get_attachment_image( $product_image_id, 'full', false, array( 'class' => 'custom-product-image' ) ); ?>
		</div>
	<?php endif; ?>
	
	<?php if ( ! empty( $product_gallery_ids ) ) : ?>
		<div class="custom-product-gallery">
			<?php foreach ( $product_gallery_ids as $gallery_id ) : ?>
				<div class="custom-product-gallery-item">
					<?php echo wp_get_attachment_image( $gallery_id, 'medium', false, array( 'class' => 'custom-gallery-image' ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<!-- Custom Product Summary -->
<section class="custom-product-summary-section">
	<div class="custom-product-info">
		<?php if ( $product_rating && $product_rating > 0 ) : ?>
			<div class="custom-product-rating">
				<span class="custom-rating-value"><?php echo esc_html( number_format( $product_rating, 1 ) ); ?></span>
				<span class="custom-rating-count">(<?php echo esc_html( $product_rating_count ); ?> تقييمات)</span>
			</div>
		<?php endif; ?>
		
		<div class="custom-product-price-section">
			<?php if ( $is_on_sale && $product_sale_price ) : ?>
				<span class="custom-price-sale"><?php echo $product->get_price_html(); ?></span>
				<?php if ( $product_regular_price ) : ?>
					<span class="custom-price-regular"><?php echo wc_price( $product_regular_price ); ?></span>
				<?php endif; ?>
			<?php else : ?>
				<span class="custom-price"><?php echo $product_price; ?></span>
			<?php endif; ?>
		</div>
		
		<?php if ( $product_excerpt ) : ?>
			<div class="custom-product-excerpt">
				<?php echo apply_filters( 'woocommerce_short_description', $product_excerpt ); ?>
			</div>
		<?php endif; ?>
		
		<div class="custom-product-stock-status">
			<?php if ( $is_in_stock ) : ?>
				<span class="custom-stock-in-stock">متوفر</span>
			<?php else : ?>
				<span class="custom-stock-out-of-stock">غير متوفر</span>
			<?php endif; ?>
		</div>
		
		<!-- Custom Add to Cart Form -->
		<div class="custom-add-to-cart-section">
			<?php
			// Output add to cart form based on product type
			if ( $product->is_type( 'simple' ) ) {
				woocommerce_simple_add_to_cart();
			} elseif ( $product->is_type( 'variable' ) ) {
				woocommerce_variable_add_to_cart();
			} elseif ( $product->is_type( 'grouped' ) ) {
				woocommerce_grouped_add_to_cart();
			} elseif ( $product->is_type( 'external' ) ) {
				woocommerce_external_add_to_cart();
			}
			?>
		</div>
		
		<!-- Custom Buy Now Button -->
		<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
		<div class="custom-buy-now-section">
			<?php
			// For simple products, add to cart and redirect to checkout
			if ( $product->is_type( 'simple' ) ) {
				$buy_now_url = add_query_arg( array(
					'add-to-cart' => $product_id,
					'buy-now' => '1'
				), wc_get_checkout_url() );
			} else {
				// For variable/grouped products, just go to checkout (they need to select options first)
				$buy_now_url = wc_get_checkout_url();
			}
			?>
			<a href="<?php echo esc_url( $buy_now_url ); ?>" class="custom-buy-now-btn">
				اشتري الآن
			</a>
		</div>
		<?php endif; ?>
		
		<!-- Custom Product Meta -->
		<div class="custom-product-meta-section">
			<?php if ( $product_sku ) : ?>
				<div class="custom-product-meta-item">
					<span class="custom-meta-label">SKU:</span>
					<span class="custom-meta-value"><?php echo esc_html( $product_sku ); ?></span>
				</div>
			<?php endif; ?>
			
			<?php if ( $product_categories && ! is_wp_error( $product_categories ) ) : ?>
				<div class="custom-product-meta-item">
					<span class="custom-meta-label">الفئات:</span>
					<span class="custom-meta-value">
						<?php
						$category_names = array();
						foreach ( $product_categories as $category ) {
							$category_names[] = '<a href="' . esc_url( get_term_link( $category ) ) . '">' . esc_html( $category->name ) . '</a>';
						}
						echo implode( ', ', $category_names );
						?>
					</span>
				</div>
			<?php endif; ?>
			
			<?php if ( $product_tags && ! is_wp_error( $product_tags ) ) : ?>
				<div class="custom-product-meta-item">
					<span class="custom-meta-label">العلامات:</span>
					<span class="custom-meta-value">
						<?php
						$tag_names = array();
						foreach ( $product_tags as $tag ) {
							$tag_names[] = '<a href="' . esc_url( get_term_link( $tag ) ) . '">' . esc_html( $tag->name ) . '</a>';
						}
						echo implode( ', ', $tag_names );
						?>
					</span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- Custom Product Content -->
<?php if ( $product_content ) : ?>
<section class="custom-product-content-section">
	<h2 class="custom-section-title">وصف المنتج</h2>
	<div class="custom-product-content">
		<?php echo apply_filters( 'the_content', $product_content ); ?>
	</div>
</section>
<?php endif; ?>

<!-- Custom Product Tabs -->
<section class="custom-product-tabs-section">
	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</section>

<?php do_action( 'woocommerce_after_single_product' ); ?>

