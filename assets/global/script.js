/**
 * Learnsimply Homepage - Interactive Functionality
 * Pure Vanilla JavaScript - No Libraries
 */

console.log("🚀 Script loaded successfully!");

// ===== GLOBAL FAQ FUNCTION =====
window.toggleFaq = function (button) {
  console.log("🔥 toggleFaq called!", button);

  const faqItem = button.closest(".faq-accordion-item");
  console.log("📦 Found FAQ item:", faqItem);

  if (!faqItem) {
    console.error("❌ Could not find faq-accordion-item parent");
    return;
  }

  const isActive = faqItem.classList.contains("active");
  console.log("🔍 Is currently active:", isActive);

  // Close all other FAQ items
  document.querySelectorAll(".faq-accordion-item").forEach((item) => {
    if (item !== faqItem) {
      item.classList.remove("active");
    }
  });

  // Toggle current FAQ item
  if (isActive) {
    faqItem.classList.remove("active");
    console.log("⬇️ Removed active class from current item");
  } else {
    faqItem.classList.add("active");
    console.log("⬆️ Added active class to current item");
  }
};

console.log("🚀 FAQ script loaded successfully!");

(function () {
  "use strict";

  // ===== UTILITY FUNCTIONS =====

  /**
   * Smoothly scroll to element
   */
  function smoothScrollTo(targetElement) {
    if (!targetElement) return;

    const headerOffset = 100;
    const elementPosition = targetElement.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

    window.scrollTo({
      top: offsetPosition,
      behavior: "smooth",
    });
  }

  /**
   * Debounce function to limit execution rate
   */
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // ===== HEADER NAVIGATION FUNCTIONALITY =====

  /**
   * Initialize header navigation menu items
   */
  function initHeaderNavigation() {
    const menuItems = document.querySelectorAll(
      ".learnsimply-header-menu-item",
    );

    if (!menuItems.length) return;

    menuItems.forEach((menuItem) => {
      menuItem.addEventListener("click", function () {
        const target = this.getAttribute("data-target");

        // Remove active state from all menu items
        menuItems.forEach((item) => {
          item.classList.remove("learnsimply-header-menu-item-active");
          const menuText = item.querySelector(".learnsimply-header-menu-text");
          if (menuText) {
            menuText.classList.remove("learnsimply-header-menu-text-active");
          }
        });

        // Add active state to clicked item
        this.classList.add("learnsimply-header-menu-item-active");
        const clickedMenuText = this.querySelector(
          ".learnsimply-header-menu-text",
        );
        if (clickedMenuText) {
          clickedMenuText.classList.add("learnsimply-header-menu-text-active");
        }

        // Smooth scroll to target section
        if (target && target.startsWith("#")) {
          const targetElement = document.querySelector(target);
          if (targetElement) {
            smoothScrollTo(targetElement);
          }
        }
      });
    });
  }

  /**
   * Initialize hero section buttons
   */
  function initHeroSectionButtons() {
    const btnBrowseCourses = document.querySelector(
      ".learnsimply-button-light",
    );
    const btnBrowseBooks = document.querySelector(".learnsimply-button-dark");

    if (btnBrowseCourses) {
      btnBrowseCourses.addEventListener("click", function (event) {
        event.preventDefault();
        // WordPress/Twig integration point
        console.log("Browse Courses button clicked");
        // window.location.href = '/courses';
      });
    }

    if (btnBrowseBooks) {
      btnBrowseBooks.addEventListener("click", function (event) {
        event.preventDefault();
        // WordPress/Twig integration point
        console.log("Browse Books button clicked");
        // window.location.href = '/books';
      });
    }
  }

  /**
   * Initialize auth buttons functionality
   */
  function initHeaderAuthButtons() {
    const loginButton = document.querySelector(".learnsimply-header-btn-login");
    const signupButton = document.querySelector(
      ".learnsimply-header-btn-signup",
    );

    if (loginButton) {
      loginButton.addEventListener("click", function (event) {
        event.preventDefault();
        // WordPress/Twig integration point
        console.log("Login button clicked");
        // window.location.href = '/login';
      });
    }

    if (signupButton) {
      signupButton.addEventListener("click", function (event) {
        event.preventDefault();
        // WordPress/Twig integration point
        console.log("Signup button clicked");
        // window.location.href = '/register';
      });
    }
  }

  /**
   * Update active navigation on scroll
   */
  function updateHeaderNavigationOnScroll() {
    const sections = document.querySelectorAll("section[id]");
    const menuItems = document.querySelectorAll(
      ".learnsimply-header-menu-item",
    );

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const sectionId = entry.target.getAttribute("id");

            menuItems.forEach((item) => {
              const target = item.getAttribute("data-target");

              if (target === `#${sectionId}`) {
                menuItems.forEach((mi) => {
                  mi.classList.remove("learnsimply-header-menu-item-active");
                  const text = mi.querySelector(
                    ".learnsimply-header-menu-text",
                  );
                  if (text)
                    text.classList.remove(
                      "learnsimply-header-menu-text-active",
                    );
                });

                item.classList.add("learnsimply-header-menu-item-active");
                const menuText = item.querySelector(
                  ".learnsimply-header-menu-text",
                );
                if (menuText) {
                  menuText.classList.add("learnsimply-header-menu-text-active");
                }
              }
            });
          }
        });
      },
      {
        threshold: 0.3,
        rootMargin: "-100px 0px -66%",
      },
    );

    sections.forEach((section) => observer.observe(section));
  }

  // ===== FAQ ACCORDION FUNCTIONALITY =====

  /**
   * Initialize FAQ accordion
   */
  function initFaqAccordion() {
    const faqQuestions = document.querySelectorAll(".learnsimply-faq-question");

    faqQuestions.forEach((question) => {
      question.addEventListener("click", function () {
        const faqItem = this.closest(".learnsimply-faq-item");
        const isActive = faqItem.classList.contains(
          "learnsimply-faq-item-active",
        );

        // Close all other FAQ items
        document.querySelectorAll(".learnsimply-faq-item").forEach((item) => {
          if (item !== faqItem) {
            item.classList.remove("learnsimply-faq-item-active");
          }
        });

        // Toggle current FAQ item
        if (isActive) {
          faqItem.classList.remove("learnsimply-faq-item-active");
        } else {
          faqItem.classList.add("learnsimply-faq-item-active");
        }
      });
    });
  }

  /**
   * Initialize new FAQ accordion
   */
  function initNewFaqAccordion() {
    // Small delay to ensure DOM is fully ready
    setTimeout(() => {
      console.log("🚀 Initializing new FAQ accordion...");

      // Add click handlers to FAQ questions using event delegation
      document.addEventListener("click", function (e) {
        if (e.target.closest(".faq-question")) {
          console.log("✅ FAQ question clicked!");
          e.preventDefault();
          e.stopPropagation();

          const question = e.target.closest(".faq-question");
          const faqItem = question.closest(".faq-accordion-item");

          console.log(
            "📝 Question text:",
            question.querySelector(".faq-question-text").textContent.trim(),
          );

          if (!faqItem) {
            console.error("❌ Could not find faq-accordion-item parent");
            return;
          }

          const isActive = faqItem.classList.contains("active");
          console.log("🔍 Is currently active:", isActive);

          // Close all other FAQ items
          document.querySelectorAll(".faq-accordion-item").forEach((item) => {
            if (item !== faqItem) {
              item.classList.remove("active");
            }
          });

          // Toggle current FAQ item
          if (isActive) {
            faqItem.classList.remove("active");
            console.log("⬇️ Removed active class from current item");
          } else {
            faqItem.classList.add("active");
            console.log("⬆️ Added active class to current item");
          }
        }
      });

      console.log("✨ New FAQ accordion initialized with event delegation");
    }, 100);
  }
  // ===== NEW FAQ ACCORDION FUNCTIONALITY =====

  // ===== TESTIMONIALS SLIDER FUNCTIONALITY =====

  /**
   * Initialize testimonials slider
   */
  function initTestimonialsSlider() {
    const slider = document.querySelector(".learnsimply-testimonials-slider");
    const prevButton = document.querySelector(".learnsimply-arrow-prev");
    const nextButton = document.querySelector(".learnsimply-arrow-next");

    if (!slider || !prevButton || !nextButton) return;

    const cardWidth = 492.667 + 24; // Card width + gap
    let currentPosition = 0;

    function updateSliderPosition() {
      slider.scrollTo({
        left: currentPosition,
        behavior: "smooth",
      });
    }

    function updateButtonStates() {
      const maxScroll = slider.scrollWidth - slider.clientWidth;

      prevButton.style.opacity = currentPosition <= 0 ? "0.5" : "1";
      prevButton.style.pointerEvents = currentPosition <= 0 ? "none" : "auto";

      nextButton.style.opacity = currentPosition >= maxScroll ? "0.5" : "1";
      nextButton.style.pointerEvents =
        currentPosition >= maxScroll ? "none" : "auto";
    }

    prevButton.addEventListener("click", function () {
      currentPosition = Math.max(0, currentPosition - cardWidth);
      updateSliderPosition();
      updateButtonStates();
    });

    nextButton.addEventListener("click", function () {
      const maxScroll = slider.scrollWidth - slider.clientWidth;
      currentPosition = Math.min(maxScroll, currentPosition + cardWidth);
      updateSliderPosition();
      updateButtonStates();
    });

    // Update button states on slider scroll
    slider.addEventListener(
      "scroll",
      debounce(function () {
        currentPosition = slider.scrollLeft;
        updateButtonStates();
      }, 100),
    );

    // Initial button state
    updateButtonStates();

    // Handle touch/mouse drag scrolling
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener("mousedown", (e) => {
      isDown = true;
      slider.style.cursor = "grabbing";
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener("mouseleave", () => {
      isDown = false;
      slider.style.cursor = "grab";
    });

    slider.addEventListener("mouseup", () => {
      isDown = false;
      slider.style.cursor = "grab";
      currentPosition = slider.scrollLeft;
      updateButtonStates();
    });

    slider.addEventListener("mousemove", (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 2;
      slider.scrollLeft = scrollLeft - walk;
    });
  }

  // ===== NEW TESTIMONIALS GRID SLIDER =====
  function initNewTestimonialsSlider() {
    console.log("🚀 initNewTestimonialsSlider starting");
    const grid = document.getElementById("testimonialsGrid");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    if (!grid || !prevBtn || !nextBtn) return;
    
    // Skip if already initialized by page-specific script (home/script.js or about-me/script.js)
    if (grid.dataset.sliderInitialized === 'true') {
      console.log("🚀 Testimonials slider already initialized by page-specific script");
      return;
    }

    const cards = Array.from(
      grid.querySelectorAll(".learnsimply-new-testimonial-card"),
    );
    if (!cards.length) return;

    const GAP = 24; // should match CSS gap

    function getSlideWidth() {
      const rect = cards[0].getBoundingClientRect();
      return Math.round(rect.width) + GAP;
    }

    function scrollToLeft(left) {
      grid.scrollTo({ left, behavior: "smooth" });
    }

    function nextSlide() {
      const slideWidth = getSlideWidth();
      const maxScroll = grid.scrollWidth - grid.clientWidth;
      let target = Math.round(grid.scrollLeft + slideWidth);
      if (target > maxScroll - 2) target = 0; // loop
      scrollToLeft(target);
    }

    function prevSlide() {
      const slideWidth = getSlideWidth();
      const maxScroll = grid.scrollWidth - grid.clientWidth;
      let target = Math.round(grid.scrollLeft - slideWidth);
      if (target < 2) target = maxScroll; // to end
      scrollToLeft(target);
    }

    prevBtn.addEventListener("click", function () {
      console.log("🔹 testimonials prevBtn clicked");
      prevSlide();
      restartAuto();
    });

    nextBtn.addEventListener("click", function () {
      console.log("🔹 testimonials nextBtn clicked");
      nextSlide();
      restartAuto();
    });

    // Auto-advance every 3s
    let autoId = setInterval(nextSlide, 3000);
    function restartAuto() {
      clearInterval(autoId);
      autoId = setInterval(nextSlide, 3000);
    }

    // Pause on interaction
    grid.addEventListener("mouseenter", () => clearInterval(autoId));
    grid.addEventListener("mouseleave", () => restartAuto());
    grid.addEventListener("pointerdown", () => clearInterval(autoId));
    grid.addEventListener("pointerup", () => restartAuto());

    document.addEventListener("visibilitychange", () => {
      if (document.hidden) clearInterval(autoId);
      else restartAuto();
    });

    // Handle resize - snap to nearest
    let resizeTimer;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        const slide = getSlideWidth();
        const index = Math.round(grid.scrollLeft / slide);
        scrollToLeft(index * slide);
      }, 150);
    });
  }

  // ===== HEADER MOBILE MENU TOGGLE =====
  (function () {
    const mobileToggle = document.querySelector(
      ".learnsimply-header-mobile-toggle",
    );
    const navMenu = document.querySelector(
      ".learnsimply-header-navigation-menu",
    );
    const authButtons = document.querySelector(
      ".learnsimply-header-auth-buttons-container",
    );

    console.log("Mobile toggle elements:", mobileToggle, navMenu, authButtons);

    if (mobileToggle && navMenu) {
      console.log("Attaching event listener to toggle");
      mobileToggle.addEventListener("click", function (e) {
        e.stopPropagation();
        console.log("Toggle clicked");
        this.classList.toggle("active");
        navMenu.classList.toggle("active");
      });

      // Close menu when clicking menu items
      const menuItems = document.querySelectorAll(
        ".learnsimply-header-menu-item",
      );
      menuItems.forEach(function (item) {
        item.addEventListener("click", function () {
          console.log("Menu item clicked");
          mobileToggle.classList.remove("active");
          navMenu.classList.remove("active");
        });
      });

      // Close menu when clicking outside
      document.addEventListener("click", function (event) {
        const isClickInside =
          mobileToggle.contains(event.target) || navMenu.contains(event.target);

        if (!isClickInside && navMenu.classList.contains("active")) {
          console.log("Clicked outside, closing menu");
          mobileToggle.classList.remove("active");
          navMenu.classList.remove("active");
        }
      });

      // Close menu on window resize if screen gets larger
      window.addEventListener(
        "resize",
        debounce(function () {
          if (window.innerWidth > 900 && navMenu.classList.contains("active")) {
            mobileToggle.classList.remove("active");
            navMenu.classList.remove("active");
          }
        }, 250),
      );
    } else {
      console.log("Some elements not found");
    }
  })();

  // ===== SCROLL TO TOP BUTTON =====

  /**
   * Initialize scroll to top button
   */
  function initScrollToTop() {
    const scrollButton = document.querySelector(".learnsimply-scroll-to-top");

    if (!scrollButton) return;

    // Show/hide button based on scroll position
    function toggleScrollButton() {
      if (window.pageYOffset > 300) {
        scrollButton.classList.add("learnsimply-visible");
      } else {
        scrollButton.classList.remove("learnsimply-visible");
      }
    }

    // Scroll to top on click
    scrollButton.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });

    // Listen to scroll events
    window.addEventListener("scroll", debounce(toggleScrollButton, 100));

    // Initial check
    toggleScrollButton();
  }

  // ===== CARD ANIMATIONS =====

  /**
   * Add intersection observer for card animations
   */
  function initCardAnimations() {
    const cards = document.querySelectorAll(
      ".learnsimply-course-card, .learnsimply-book-card, .learnsimply-feature-card, .learnsimply-testimonial-card",
    );

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry, index) => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.style.opacity = "1";
              entry.target.style.transform = "translateY(0)";
            }, index * 100);
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
      },
    );

    cards.forEach((card) => {
      card.style.opacity = "0";
      card.style.transform = "translateY(20px)";
      card.style.transition = "opacity 0.6s ease, transform 0.6s ease";
      observer.observe(card);
    });
  }

  // ===== HEADER SCROLL EFFECT =====

  /**
   * Add background blur effect to header on scroll
   */
  function initHeaderScrollEffect() {
    const header = document.querySelector(".learnsimply-header-main-container");

    if (!header) return;

    function updateHeaderStyle() {
      if (window.pageYOffset > 50) {
        header.style.backgroundColor = "rgba(10, 15, 26, 0.95)";
        header.style.backdropFilter = "blur(20px)";
        header.style.webkitBackdropFilter = "blur(20px)";
      } else {
        header.style.backgroundColor = "rgba(10, 15, 26, 0.8)";
        header.style.backdropFilter = "blur(10px)";
        header.style.webkitBackdropFilter = "blur(10px)";
      }
    }

    window.addEventListener("scroll", debounce(updateHeaderStyle, 50));
  }

  // ===== BUTTON RIPPLE EFFECT =====

  /**
   * Add ripple effect to buttons on click
   */
  function initButtonRippleEffect() {
    const buttons = document.querySelectorAll(
      ".learnsimply-login-button, .learnsimply-cta-primary-btn, .learnsimply-buy-now-button, .learnsimply-primary-cta-btn",
    );

    buttons.forEach((button) => {
      button.addEventListener("click", function (e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const ripple = document.createElement("span");
        ripple.style.position = "absolute";
        ripple.style.left = x + "px";
        ripple.style.top = y + "px";
        ripple.style.width = "0";
        ripple.style.height = "0";
        ripple.style.borderRadius = "50%";
        ripple.style.background = "rgba(255, 255, 255, 0.5)";
        ripple.style.transform = "translate(-50%, -50%)";
        ripple.style.animation = "ripple-animation 0.6s ease-out";

        this.style.position = "relative";
        this.style.overflow = "hidden";
        this.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
      });
    });

    // Add ripple animation to page
    const style = document.createElement("style");
    style.textContent = `
            @keyframes ripple-animation {
                to {
                    width: 200px;
                    height: 200px;
                    opacity: 0;
                }
            }
        `;
    document.head.appendChild(style);
  }

  // ===== STATS COUNTER ANIMATION =====

  /**
   * Animate stats numbers on scroll into view
   */
  function initStatsCounter() {
    const statNumbers = document.querySelectorAll(".learnsimply-stat-number");

    function animateValue(element, start, end, duration) {
      const range = end - start;
      const increment = range / (duration / 16);
      let current = start;

      const timer = setInterval(() => {
        current += increment;
        if (
          (increment > 0 && current >= end) ||
          (increment < 0 && current <= end)
        ) {
          element.textContent = end.toLocaleString("ar-EG");
          clearInterval(timer);
        } else {
          element.textContent = Math.floor(current).toLocaleString("ar-EG");
        }
      }, 16);
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const targetText = entry.target.textContent.replace(/[^0-9]/g, "");
            const targetNumber = parseInt(targetText, 10);

            if (!isNaN(targetNumber)) {
              animateValue(entry.target, 0, targetNumber, 2000);
              observer.unobserve(entry.target);
            }
          }
        });
      },
      {
        threshold: 0.5,
      },
    );

    statNumbers.forEach((stat) => observer.observe(stat));
  }

  // ===== ADD TO CART FUNCTIONALITY =====

  /**
   * Handle add to cart button clicks
   */
  function initAddToCart() {
    const addCartButtons = document.querySelectorAll(
      ".learnsimply-add-cart-button",
    );

    addCartButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.preventDefault();

        // Add visual feedback
        const originalContent = this.innerHTML;
        this.innerHTML = "✓";
        this.style.background = "#18A963";

        setTimeout(() => {
          this.innerHTML = originalContent;
          this.style.background = "";
        }, 1500);

        // Here you would typically send data to a cart system
        console.log("Item added to cart");
      });
    });
  }

  // ===== BUY NOW FUNCTIONALITY =====

  /**
   * Handle buy now button clicks
   */
  function initBuyNow() {
    const buyButtons = document.querySelectorAll(".learnsimply-buy-now-button");

    buyButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.preventDefault();

        // Here you would typically redirect to checkout
        console.log("Proceeding to checkout");
      });
    });
  }

  // ===== LAZY LOAD IMAGES =====

  /**
   * Lazy load images as they come into viewport
   */
  function initLazyLoading() {
    const images = document.querySelectorAll("img[data-src]");

    const imageObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.removeAttribute("data-src");
            imageObserver.unobserve(img);
          }
        });
      },
      {
        rootMargin: "50px 0px",
      },
    );

    images.forEach((img) => imageObserver.observe(img));
  }

  // ===== BANNER ANIMATION =====

  /**
   * Duplicate banner content for seamless scrolling
   */
  function initBannerAnimation() {
    const bannerContent = document.querySelector(".learnsimply-banner-content");

    if (!bannerContent) return;

    // Clone banner content for seamless loop
    const clone = bannerContent.cloneNode(true);
    bannerContent.parentNode.appendChild(clone);
  }

  // ===== FORM VALIDATION (if forms exist) =====

  /**
   * Basic form validation
   */
  function initFormValidation() {
    const forms = document.querySelectorAll("form");

    forms.forEach((form) => {
      form.addEventListener("submit", function (e) {
        const inputs = this.querySelectorAll(
          "input[required], textarea[required]",
        );
        let isValid = true;

        inputs.forEach((input) => {
          if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = "#F96A7B";
          } else {
            input.style.borderColor = "";
          }
        });

        if (!isValid) {
          e.preventDefault();
          alert("الرجاء ملء جميع الحقول المطلوبة");
        }
      });
    });
  }

  // ===== KEYBOARD ACCESSIBILITY =====

  /**
   * Enhance keyboard navigation
   */
  function initKeyboardAccessibility() {
    // Allow Enter key to trigger button clicks
    const buttons = document.querySelectorAll(
      "button, .learnsimply-nav-link, .learnsimply-faq-question",
    );

    buttons.forEach((button) => {
      button.setAttribute("tabindex", "0");

      button.addEventListener("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          this.click();
        }
      });
    });
  }

  // Replace availability SVG icons with `Archive Check.png`
  function initArchiveCheckIcons() {
    // Replace any SVG with class .info-icon that pairs with a "+40 متوفر" label
    document.querySelectorAll('.book-info .info-item').forEach((item) => {
      const label = item.querySelector('p');
      if (!label) return;
      if (label.textContent.trim() !== '+40 متوفر') return;

      const svg = item.querySelector('svg.info-icon');
      if (svg) {
        const img = document.createElement('img');
        img.className = 'info-icon';
        img.src = 'img/Archive Check.png';
        img.alt = 'متوفر';
        svg.replaceWith(img);
      }
    });
  }

  // ===== INITIALIZE ALL FUNCTIONALITY =====

  /**
   * Main initialization function
   */
  function init() {
    // Wait for DOM to be fully loaded
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", init);
      return;
    }

    console.log("Initializing Learnsimply Homepage...");
    HeaderNavigation();
    initHeaderAuthButtons();
    initHeroSectionButtons();
    initNavigationSmoothScroll();
    updateActiveNavigationOnScroll();
    initFaqAccordion();
    initTestimonialsSlider();
    initNewTestimonialsSlider();
    initScrollToTop();
    initCardAnimations();
    initHeaderScrollEffect();
    initButtonRippleEffect();
    initStatsCounter();
    initAddToCart();
    initBuyNow();
    initLazyLoading();
    initArchiveCheckIcons();
    initBannerAnimation();
    initFormValidation();
    initKeyboardAccessibility();

    console.log("Learnsimply Homepage initialized successfully!");
  }

  // Start initialization
  init();
})();
