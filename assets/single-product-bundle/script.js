/**
 * Single Product Bundle Page JavaScript
 * Handles interactive functionality for the bundle product page
 */

document.addEventListener("DOMContentLoaded", function () {
	// Add to Cart functionality for WooCommerce products
	const addToCartButtons = document.querySelectorAll(
		".add-to-cart-button"
	);
	addToCartButtons.forEach((button) => {
		button.addEventListener("click", function (e) {
			e.preventDefault();
			const productId = this.getAttribute("data-product-id");
			if (!productId) return;

			// Show loading state
			const originalText = this.textContent;
			this.textContent = "جاري الإضافة...";
			this.disabled = true;

			// Use WooCommerce AJAX add to cart
			if (typeof wc_add_to_cart_params !== "undefined") {
				const data = {
					action: "woocommerce_add_to_cart",
					product_id: productId,
					quantity: 1,
				};

				fetch(
					wc_add_to_cart_params.wc_ajax_url
						.toString()
						.replace("%%endpoint%%", "add_to_cart"),
					{
						method: "POST",
						headers: {
							"Content-Type":
								"application/x-www-form-urlencoded; charset=UTF-8",
						},
						body: new URLSearchParams(data),
					}
				)
					.then((response) => response.json())
					.then((data) => {
						if (data.error && data.product_url) {
							window.location = data.product_url;
							return;
						}
						// Trigger cart update event
						document.body.dispatchEvent(
							new CustomEvent("added_to_cart", {
								detail: {
									fragments: data.fragments,
									cart_hash: data.cart_hash,
								},
							})
						);
						// Update button text
						this.textContent = "تمت الإضافة!";
						setTimeout(() => {
							this.textContent = originalText;
							this.disabled = false;
						}, 2000);
					})
					.catch((error) => {
						console.error("Error adding to cart:", error);
						this.textContent = originalText;
						this.disabled = false;
					});
			} else {
				// Fallback to regular link
				window.location.href = this.href;
			}
		});
	});

	// Description card expand/collapse functionality
	const showMoreBtn = document.querySelector(".show-more-btn");
	const descriptionCard = document.querySelector(".description-card");
	
	if (showMoreBtn && descriptionCard) {
		showMoreBtn.addEventListener("click", function () {
			const isExpanded = descriptionCard.classList.contains("expanded");
			
			if (isExpanded) {
				descriptionCard.classList.remove("expanded");
				this.textContent = "عرض المزيد";
			} else {
				descriptionCard.classList.add("expanded");
				this.textContent = "عرض أقل";
			}
		});
	}
});

// Share product function
function shareProduct() {
	if (navigator.share) {
		navigator
			.share({
				title: document.title,
				text: document.querySelector(".course-title")?.textContent || "",
				url: window.location.href,
			})
			.catch((error) => {
				console.log("Error sharing:", error);
			});
	} else {
		// Fallback: copy to clipboard
		navigator.clipboard.writeText(window.location.href).then(
			() => {
				alert("تم نسخ رابط المنتج!");
			},
			() => {
				alert("فشل نسخ الرابط");
			}
		);
	}
}
