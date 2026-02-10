<?php
/**
 * Custom WooCommerce Cart Template - Empty Design
 * 
 * Custom cart page template with different HTML elements and classes
 *
 * @package EduBlink_Child
 */

defined( 'ABSPATH' ) || exit;

?>

<!-- Custom Cart Header -->
<section class="custom-woo-cart-header">
	<h1 class="custom-woo-cart-title">سلة التسوق</h1>
</section>

<?php do_action( 'woocommerce_before_cart' ); ?>

<section class="custom-woo-cart-content">
	<?php if ( WC()->cart->is_empty() ) : ?>
		<!-- Custom Empty Cart State -->
		<section class="custom-woo-empty-cart-section">
			<div class="custom-woo-empty-cart-content">
				<p class="custom-woo-empty-cart-message">السلة فارغة</p>
				<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="custom-woo-browse-products-btn">
					تصفح المنتجات
				</a>
			</div>
		</section>
	<?php else : ?>
		
		<form class="custom-woo-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>
			
			<div class="custom-woo-cart-items-list">
				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						?>
						<div class="custom-woo-cart-item">
							<div class="custom-woo-cart-item-image">
								<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
								if ( ! $product_permalink ) {
									echo $thumbnail; // PHPCS: XSS ok.
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
								}
								?>
							</div>
							
							<div class="custom-woo-cart-item-info">
								<h3 class="custom-woo-cart-item-title">
									<?php
									if ( ! $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									}
									?>
								</h3>
								
								<div class="custom-woo-cart-item-meta">
									<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								
								<div class="custom-woo-cart-item-quantity">
									<?php
									if ( $_product->is_sold_individually() ) {
										$min_quantity = 1;
										$max_quantity = 1;
									} else {
										$min_quantity = 0;
										$max_quantity = $_product->get_max_purchase_quantity();
									}
									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $max_quantity,
											'min_value'    => $min_quantity,
											'product_name' => $_product->get_name(),
										),
										$_product,
										false
									);
									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
									?>
								</div>
							</div>
							
							<div class="custom-woo-cart-item-price-section">
								<div class="custom-woo-cart-item-price">
									<?php
									echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</div>
								
								<div class="custom-woo-cart-item-subtotal">
									<?php
									echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								</div>
								
								<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="custom-woo-remove-item-btn" aria-label="%s" data-product_id="%s" data-product_sku="%s">إزالة</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
								?>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
			
			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>
		
		<div class="custom-woo-cart-summary-section">
			<div class="custom-woo-cart-summary">
				<h2 class="custom-woo-summary-title">ملخص الطلب</h2>
				
				<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
				
				<div class="custom-woo-cart-collaterals">
					<?php
					/**
					 * Cart collaterals hook.
					 *
					 * @hooked woocommerce_cross_sell_display
					 * @hooked woocommerce_cart_totals - 10
					 */
					do_action( 'woocommerce_cart_collaterals' );
					?>
				</div>
			</div>
		</div>
		
	<?php endif; ?>
</section>

<?php do_action( 'woocommerce_after_cart' ); ?>

