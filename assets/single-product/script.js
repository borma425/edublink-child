/**
 * Single Product Page JavaScript
 * Handles interactive functionality for the product page
 */

document.addEventListener("DOMContentLoaded", function () {
	// Gallery image click handler - change main image
	const galleryImages = document.querySelectorAll(".gallery-image-item img");
	const mainProductImage = document.querySelector(".main-product-image img");

	if (galleryImages.length > 0 && mainProductImage) {
		galleryImages.forEach((galleryImg) => {
			galleryImg.addEventListener("click", function () {
				mainProductImage.src = this.src;
				mainProductImage.alt = this.alt;
			});
		});
	}

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

	// Handle review form submit loading state
	const reviewForms = document.querySelectorAll(".add-review-section form");
	reviewForms.forEach((form) => {
		form.addEventListener("submit", function () {
			const submitBtn =
				form.querySelector('button[type="submit"]') ||
				form.querySelector('input[type="submit"]');
			if (!submitBtn) return;

			if (!submitBtn.dataset.originalText) {
				submitBtn.dataset.originalText = submitBtn.textContent;
			}

			submitBtn.classList.add("is-loading");
			submitBtn.disabled = true;
		});
	});

	// Custom star rating behavior for review form (hover + click to select)
	const starWrappers = document.querySelectorAll(
		".add-review-section p.stars"
	);
	starWrappers.forEach((wrapper) => {
		const stars = wrapper.querySelectorAll("a");
		const form = wrapper.closest("form");
		if (!form || stars.length === 0) return;

		const ratingSelect = form.querySelector("#rating");
		let currentRating = parseInt(ratingSelect?.value || "0", 10) || 0;

		const applyVisual = (value) => {
			stars.forEach((star, index) => {
				if (index < value) {
					star.classList.add("is-active");
				} else {
					star.classList.remove("is-active");
				}
			});
		};

		// Initial state
		if (currentRating > 0) {
			applyVisual(currentRating);
		}

		stars.forEach((star, index) => {
			const value = index + 1;

			star.addEventListener("mouseenter", () => {
				applyVisual(value);
			});

			star.addEventListener("mouseleave", () => {
				applyVisual(currentRating);
			});

			star.addEventListener("click", (e) => {
				e.preventDefault();
				currentRating = value;
				if (ratingSelect) {
					ratingSelect.value = String(currentRating);
				}
				applyVisual(currentRating);
			});
		});
	});
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

