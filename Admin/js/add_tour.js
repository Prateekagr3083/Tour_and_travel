// Add Tour Form JavaScript - Simple and direct approach
(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Add Tour JS loaded');

        // Get the button
        const addBtn = document.getElementById('add-tour-btn');
        console.log('Add button element:', addBtn);

        if (addBtn) {
            // Add click event listener
            addBtn.addEventListener('click', function(e) {
                console.log('Add Tour button clicked!');
                e.preventDefault();

                // Get the form
                const form = document.getElementById('add-tour-form');
                console.log('Form element:', form);

                if (form) {
                    // Show the form
                    form.style.display = 'block';
                    console.log('Form displayed');

                    // Scroll to form
                    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                    // Focus on first input
                    const firstInput = form.querySelector('input');
                    if (firstInput) {
                        firstInput.focus();
                    }
                } else {
                    console.error('Form not found!');
                    alert('Form not found! Please check the page structure.');
                }
            });

            console.log('Event listener attached to add button');
        } else {
            console.error('Add Tour button not found!');
        }

        // Handle cancel button
        const cancelBtn = document.getElementById('cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('add-tour-form');
                if (form) {
                    form.style.display = 'none';
                    // Reset form
                    const tourForm = document.getElementById('tour-form');
                    if (tourForm) {
                        tourForm.reset();
                    }
                }
            });
        }

        // Handle form submission
        const tourForm = document.getElementById('tour-form');
        if (tourForm) {
            tourForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');

                // Basic validation
                const tourName = document.getElementById('tour-name').value.trim();
                const destination = document.getElementById('destination').value.trim();
                const price = parseFloat(document.getElementById('price').value);
                const duration = parseInt(document.getElementById('duration').value);
                const image = document.getElementById('image').files[0];

                if (!tourName || !destination || isNaN(price) || isNaN(duration) || !image) {
                    alert('Please fill in all required fields.');
                    return;
                }

                if (price <= 0) {
                    alert('Price must be greater than 0.');
                    return;
                }

                if (duration <= 0) {
                    alert('Duration must be at least 1 day.');
                    return;
                }

                // Success
                alert('Tour added successfully! (Note: Backend processing needs to be implemented)');

                // Hide form and reset
                const form = document.getElementById('add-tour-form');
                if (form) {
                    form.style.display = 'none';
                    tourForm.reset();
                }
            });
        }
    });

    console.log('Add Tour JS initialized');
})();
