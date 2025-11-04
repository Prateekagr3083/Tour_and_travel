// Tours Management JavaScript for user-side Tours.php
(function() {
    'use strict';

    // Initialize tours page functionality
    function initToursPage() {
        console.log('initToursPage() called');

        // Setup tour card click handlers
        setupTourCardClicks();
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
