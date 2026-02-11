/**
 * Home Page Scripts
 * 
 * Custom JavaScript for the home page
 * Located in: /assets/home/script.js
 * 
 * @package EduBlink_Child
 */

(function() {
	'use strict';

	// Wait for DOM to be ready
	document.addEventListener('DOMContentLoaded', function() {
		// Add to cart functionality
		initAddToCart();
		
		// Testimonials Slider (if exists on page)
		initTestimonialsSlider();
		
		// FAQ Accordion (if exists on page)
		initFAQAccordion();
	});

	/**
	 * Initialize add to cart buttons
	 */
	function initAddToCart() {
		const addToCartButtons = document.querySelectorAll('.learnsimply-add-cart-button');
		
		addToCartButtons.forEach(function(button) {
			button.addEventListener('click', function(e) {
				e.preventDefault();
				
				const productId = this.getAttribute('data-product-id');
				if (!productId) {
					console.error('Product ID not found');
					return;
				}
				
				// Add loading state
				const originalHTML = this.innerHTML;
				this.innerHTML = '<span style="display: inline-block; width: 20px; height: 20px; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite;"></span>';
				this.disabled = true;
				this.style.opacity = '0.6';
				this.style.cursor = 'wait';
				
				// Add spin animation if not exists
				if (!document.getElementById('learnsimply-spin-style')) {
					const style = document.createElement('style');
					style.id = 'learnsimply-spin-style';
					style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
					document.head.appendChild(style);
				}
				
				// Prepare data for AJAX request
				const data = {
					action: 'woocommerce_add_to_cart',
					product_id: productId,
					quantity: 1
				};
				
				// Get AJAX URL
				const ajaxUrl = typeof wc_add_to_cart_params !== 'undefined' 
					? wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart')
					: (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
				
				// Check if jQuery is available
				if (typeof jQuery !== 'undefined') {
					// Use WooCommerce AJAX
					jQuery.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: data,
						success: function(response) {
							if (response.error && response.product_url) {
								// Redirect to product page if there's an error
								window.location = response.product_url;
								return;
							}
							
							// Trigger fragments refresh
							jQuery(document.body).trigger('wc_fragment_refresh');
							
							// Show success message
							showAddToCartMessage('تم إضافة المنتج إلى السلة بنجاح!', 'success');
							
							// Restore button
							button.innerHTML = originalHTML;
							button.disabled = false;
							button.style.opacity = '1';
							button.style.cursor = 'pointer';
							
							// Add "added" class temporarily
							button.classList.add('added');
							setTimeout(function() {
								button.classList.remove('added');
							}, 2000);
						},
						error: function() {
							// Show error message
							showAddToCartMessage('حدث خطأ أثناء إضافة المنتج. يرجى المحاولة مرة أخرى.', 'error');
							
							// Restore button
							button.innerHTML = originalHTML;
							button.disabled = false;
							button.style.opacity = '1';
							button.style.cursor = 'pointer';
						}
					});
				} else {
					// Fallback: Use fetch API
					fetch(ajaxUrl, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: new URLSearchParams(data)
					})
					.then(response => response.json())
					.then(result => {
						if (result.error && result.product_url) {
							window.location = result.product_url;
							return;
						}
						
						// Trigger fragments refresh if jQuery is available
						if (typeof jQuery !== 'undefined') {
							jQuery(document.body).trigger('wc_fragment_refresh');
						} else {
							// Reload page to update cart
							location.reload();
						}
						
						showAddToCartMessage('تم إضافة المنتج إلى السلة بنجاح!', 'success');
						
						// Restore button
						button.innerHTML = originalHTML;
						button.disabled = false;
						button.style.opacity = '1';
						button.style.cursor = 'pointer';
					})
					.catch(error => {
						// Fallback: redirect to product page with add to cart parameter
						const productUrl = button.getAttribute('data-product-url');
						if (productUrl) {
							window.location.href = productUrl + '?add-to-cart=' + productId;
						} else {
							showAddToCartMessage('حدث خطأ أثناء إضافة المنتج. يرجى المحاولة مرة أخرى.', 'error');
							button.innerHTML = originalHTML;
							button.disabled = false;
							button.style.opacity = '1';
							button.style.cursor = 'pointer';
						}
					});
				}
			});
		});
	}

	/**
	 * Show add to cart message
	 */
	function showAddToCartMessage(message, type) {
		// Remove existing messages
		const existingMessage = document.querySelector('.learnsimply-cart-message');
		if (existingMessage) {
			existingMessage.remove();
		}
		
		// Create message element
		const messageEl = document.createElement('div');
		messageEl.className = 'learnsimply-cart-message';
		messageEl.style.cssText = `
			position: fixed;
			top: 20px;
			right: 20px;
			background: ${type === 'success' ? '#4caf50' : '#f44336'};
			color: white;
			padding: 15px 25px;
			border-radius: 8px;
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			z-index: 9999;
			font-size: 14px;
			font-weight: 500;
			animation: slideInRight 0.3s ease-out;
			max-width: 300px;
		`;
		messageEl.textContent = message;
		
		// Add animation if not exists
		if (!document.getElementById('learnsimply-message-animations')) {
			const style = document.createElement('style');
			style.id = 'learnsimply-message-animations';
			style.textContent = `
				@keyframes slideInRight {
					from {
						transform: translateX(100%);
						opacity: 0;
					}
					to {
						transform: translateX(0);
						opacity: 1;
					}
				}
				@keyframes slideOutRight {
					from {
						transform: translateX(0);
						opacity: 1;
					}
					to {
						transform: translateX(100%);
						opacity: 0;
					}
				}
			`;
			document.head.appendChild(style);
		}
		
		// Append to body
		document.body.appendChild(messageEl);
		
		// Auto remove after 3 seconds
		setTimeout(function() {
			messageEl.style.animation = 'slideOutRight 0.3s ease-out';
			setTimeout(function() {
				if (messageEl.parentNode) {
					messageEl.remove();
				}
			}, 300);
		}, 3000);
	}

	/**
	 * Initialize Testimonials Slider
	 */
	function initTestimonialsSlider() {
		const grid = document.getElementById("testimonialsGrid");
		const prevBtn = document.getElementById("prevBtn");
		const nextBtn = document.getElementById("nextBtn");

		if (!grid || !prevBtn || !nextBtn) return;
		
		// Mark as initialized to prevent global script from interfering
		grid.dataset.sliderInitialized = 'true';

		let currentIndex = 0;
		const cards = Array.from(
			grid.querySelectorAll(".learnsimply-new-testimonial-card")
		);
		const totalCards = cards.length;

		// Determine cards per view based on screen size
		function getCardsPerView() {
			if (window.innerWidth >= 1200) return 3;
			if (window.innerWidth >= 768) return 2;
			return 1;
		}

		function getCardWidth() {
			if (cards.length === 0) return 0;
			const cardWidth = cards[0].offsetWidth;
			const gap = 24; // gap from CSS
			return cardWidth + gap;
		}

		function updateSlider() {
			const cardsPerView = getCardsPerView();
			const maxIndex = Math.max(0, totalCards - cardsPerView);

			// Clamp current index
			currentIndex = Math.min(Math.max(0, currentIndex), maxIndex);

			// Calculate and apply transform (positive for RTL)
			const cardWidth = getCardWidth();
			const translateX = currentIndex * cardWidth;
			
			// Use positive translateX for RTL layout
			grid.style.transform = `translateX(${translateX}px)`;

			// Update button states with visual feedback
			prevBtn.disabled = currentIndex === 0;
			nextBtn.disabled = currentIndex >= maxIndex;
			
			// Add/remove disabled class for additional styling
			prevBtn.classList.toggle('is-disabled', currentIndex === 0);
			nextBtn.classList.toggle('is-disabled', currentIndex >= maxIndex);
		}

		// Previous button - go to previous cards (move right in RTL)
		prevBtn.addEventListener("click", function () {
			if (currentIndex > 0) {
				currentIndex--;
				updateSlider();
			}
		});

		// Next button - go to next cards (move left in RTL)
		nextBtn.addEventListener("click", function () {
			const cardsPerView = getCardsPerView();
			const maxIndex = totalCards - cardsPerView;
			if (currentIndex < maxIndex) {
				currentIndex++;
				updateSlider();
			}
		});

		// Update on window resize
		let resizeTimeout;
		window.addEventListener("resize", function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function () {
				currentIndex = 0; // Reset to first slide on resize
				updateSlider();
			}, 250);
		});

		// Initial setup
		updateSlider();
	}

	/**
	 * Initialize FAQ Accordion
	 * Note: FAQ uses onclick="toggleFaq(this)" in HTML, so no event listeners needed here
	 * The toggleFaq function is defined globally in assets/global/script.js
	 */
	function initFAQAccordion() {
		// FAQ functionality is handled by window.toggleFaq() via onclick attribute
		// No additional initialization needed
		return;
	}

})();

