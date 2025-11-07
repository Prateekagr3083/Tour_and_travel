// Tours Management JavaScript for user-side Tours.php
(function() {
    'use strict';

    // Initialize tours page functionality
    function initToursPage() {
        console.log('initToursPage() called');

        // Setup image sliders for each tour card
        setupImageSliders();

        // Setup tour card click handlers
        setupTourCardClicks();
    }

    // Setup image sliders for tour cards
    function setupImageSliders() {
        const sliders = document.querySelectorAll('.image-slider');

        sliders.forEach(slider => {
            const images = slider.querySelectorAll('.tour-image');
            let currentIndex = 0;
            let autoSlideInterval;

            // Function to show slide
            function showSlide(index) {
                images.forEach(img => img.classList.remove('active'));
                images[index].classList.add('active');
                currentIndex = index;
            }

            // Function to next slide
            function nextSlide() {
                currentIndex = (currentIndex + 1) % images.length;
                showSlide(currentIndex);
            }

            // Auto slide functionality
            function startAutoSlide() {
                autoSlideInterval = setInterval(nextSlide, 3000); // Change image every 3 seconds
            }

            function stopAutoSlide() {
                clearInterval(autoSlideInterval);
            }

            // Pause auto slide on hover
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);

            // Start auto slide if there are multiple images
            if (images.length > 1) {
                startAutoSlide();
            }
        });
    }

    // Setup click handlers for tour cards
    function setupTourCardClicks() {
        // Handle "View Details" button clicks
        document.addEventListener('click', function(e) {
            const viewDetailsBtn = e.target.closest('.cart-button');
            if (viewDetailsBtn) {
                e.preventDefault();
                const tourCard = viewDetailsBtn.closest('.card');
                if (tourCard) {
                    const tourId = tourCard.getAttribute('data-tour-id');
                    if (tourId) {
                        window.location.href = 'TourDetails.php?id=' + tourId;
                    }
                }
            }
        });
    }

    // Call initToursPage when DOM is ready
    document.addEventListener('DOMContentLoaded', initToursPage);

})();
