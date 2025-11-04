// Tours Management JavaScript
(function() {
    'use strict';

    // Initialize tours management
    function initToursManagement() {
        console.log('initToursManagement() called');

        // Check if we're on the tours page (look for add tour button or form)
        const addBtn = document.getElementById('add-tour-btn');
        const form = document.getElementById('add-tour-form');

        console.log('Add Tour button found:', !!addBtn);
        console.log('Add Tour form found:', !!form);

        if (!addBtn && !form) {
            console.warn('Neither Add Tour button nor form found. Exiting init.');
            return;
        }

        // Add event listeners for tour actions
        setupTourActionListeners();

        // Add search functionality
        setupTourSearch();

        // Add pagination if needed
        setupTourPagination();
    }

    // Setup tour action listeners
    function setupTourActionListeners() {
        // Edit tour functionality
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-btn');
            if (editBtn) {
                e.preventDefault();
                const tourId = editBtn.closest('tr').querySelector('td:first-child').textContent;
                editTour(tourId);
            }
        });

        // Delete tour functionality
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.preventDefault();
                const tourId = deleteBtn.closest('tr').querySelector('td:first-child').textContent;
                deleteTour(tourId);
            }
        });

        // Add new tour functionality
        const addBtn = document.getElementById('add-tour-btn');
        console.log('Setting up Add Tour button listener:', !!addBtn);
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                console.log('Add Tour button clicked!');
                e.preventDefault();
                addNewTour();
            });
        } else {
            console.error('Add Tour button not found!');
        }

        // Cancel button functionality
        const cancelBtn = document.getElementById('cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                console.log('Cancel button clicked!');
                e.preventDefault();
                hideAddTourForm();
            });
        }

        // Form submission
        const tourForm = document.getElementById('tour-form');
        if (tourForm) {
            tourForm.addEventListener('submit', function(e) {
                console.log('Form submitted!');
                e.preventDefault();
                handleTourFormSubmission();
            });
        }
    }

    // Edit tour function
    function editTour(tourId) {
        console.log('Editing tour:', tourId);
        // Implement tour editing logic here
        // This would typically open a modal or redirect to an edit page
        alert('Edit tour functionality for ID: ' + tourId);
    }

    // Delete tour function
    function deleteTour(tourId) {
        if (confirm('Are you sure you want to delete this tour? This action cannot be undone.')) {
            console.log('Deleting tour:', tourId);
            // Implement tour deletion logic here
            // This would typically send an AJAX request to delete the tour
            alert('Delete tour functionality for ID: ' + tourId);
        }
    }

    // Add new tour function
    function addNewTour() {
        console.log('addNewTour() called');
        const form = document.getElementById('add-tour-form');
        console.log('Form element found:', !!form);

        if (form) {
            console.log('Showing form');
            form.style.display = 'block';
            // Scroll to the form
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            // Focus on the first input
            const firstInput = form.querySelector('input');
            if (firstInput) {
                firstInput.focus();
                console.log('Focused on first input');
            }
        } else {
            console.error('Add Tour form not found!');
            alert('Add Tour form not found!');
        }
    }

    // Hide add tour form
    function hideAddTourForm() {
        console.log('hideAddTourForm() called');
        const form = document.getElementById('add-tour-form');
        if (form) {
            form.style.display = 'none';
            // Reset form
            const tourForm = document.getElementById('tour-form');
            if (tourForm) {
                tourForm.reset();
                console.log('Form reset');
            }
        }
    }

    // Handle tour form submission
    function handleTourFormSubmission() {
        console.log('handleTourFormSubmission() called');
        const form = document.getElementById('tour-form');
        if (!form) return;

        const formData = new FormData(form);

        // Basic validation
        const tourName = formData.get('tour_name').trim();
        const destination = formData.get('destination').trim();
        const price = parseFloat(formData.get('price'));
        const duration = parseInt(formData.get('duration'));
        const image = formData.get('image');

        console.log('Form data:', { tourName, destination, price, duration, image: image ? image.name : 'none' });

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

        // Here you would typically send the data to the server
        // For now, we'll just log it and show a success message
        console.log('Tour data valid, processing...');

        // Simulate form submission
        alert('Tour added successfully! (Note: Backend processing needs to be implemented)');

        // Hide the form and reset it
        hideAddTourForm();
    }

    // Setup tour search functionality
    function setupTourSearch() {
        // You can implement search functionality here
        // This would filter the tours table based on search input
    }

    // Setup tour pagination
    function setupTourPagination() {
        // You can implement pagination here if needed
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToursManagement);
    } else {
        initToursManagement();
    }

    // Expose functions globally if needed
    window.ToursManager = {
        editTour: editTour,
        deleteTour: deleteTour,
        addNewTour: addNewTour
    };

})();// Tours Management JavaScript
(function() {
    'use strict';

    // Initialize tours management
    function initToursManagement() {
        console.log('initToursManagement() called');

        // Check if we're on the tours page (look for add tour button or form)
        const addBtn = document.getElementById('add-tour-btn');
        const form = document.getElementById('add-tour-form');

        console.log('Add Tour button found:', !!addBtn);
        console.log('Add Tour form found:', !!form);

        if (!addBtn && !form) {
            console.warn('Neither Add Tour button nor form found. Exiting init.');
            return;
        }

        // Add event listeners for tour actions
        setupTourActionListeners();

        // Add search functionality
        setupTourSearch();

        // Add pagination if needed
        setupTourPagination();
    }

    // Setup tour action listeners
    function setupTourActionListeners() {
        // Edit tour functionality
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-btn');
            if (editBtn) {
                e.preventDefault();
                const tourId = editBtn.closest('tr').querySelector('td:first-child').textContent;
                editTour(tourId);
            }
        });

        // Delete tour functionality
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.preventDefault();
                const tourId = deleteBtn.closest('tr').querySelector('td:first-child').textContent;
                deleteTour(tourId);
            }
        });

        // Add new tour functionality
        const addBtn = document.getElementById('add-tour-btn');
        console.log('Setting up Add Tour button listener:', !!addBtn);
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                console.log('Add Tour button clicked!');
                e.preventDefault();
                addNewTour();
            });
        } else {
            console.error('Add Tour button not found!');
        }

        // Cancel button functionality
        const cancelBtn = document.getElementById('cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                console.log('Cancel button clicked!');
                e.preventDefault();
                hideAddTourForm();
            });
        }

        // Form submission
        const tourForm = document.getElementById('tour-form');
        if (tourForm) {
            tourForm.addEventListener('submit', function(e) {
                console.log('Form submitted!');
                e.preventDefault();
                handleTourFormSubmission();
            });
        }
    }

    // Edit tour function
    function editTour(tourId) {
        console.log('Editing tour:', tourId);
        // Implement tour editing logic here
        // This would typically open a modal or redirect to an edit page
        alert('Edit tour functionality for ID: ' + tourId);
    }

    // Delete tour function
    function deleteTour(tourId) {
        if (confirm('Are you sure you want to delete this tour? This action cannot be undone.')) {
            console.log('Deleting tour:', tourId);
            // Implement tour deletion logic here
            // This would typically send an AJAX request to delete the tour
            alert('Delete tour functionality for ID: ' + tourId);
        }
    }

    // Add new tour function
    function addNewTour() {
        console.log('addNewTour() called');
        const form = document.getElementById('add-tour-form');
        console.log('Form element found:', !!form);

        if (form) {
            console.log('Showing form');
            form.style.display = 'block';
            // Scroll to the form
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            // Focus on the first input
            const firstInput = form.querySelector('input');
            if (firstInput) {
                firstInput.focus();
                console.log('Focused on first input');
            }
        } else {
            console.error('Add Tour form not found!');
            alert('Add Tour form not found!');
        }
    }

    // Hide add tour form
    function hideAddTourForm() {
        console.log('hideAddTourForm() called');
        const form = document.getElementById('add-tour-form');
        if (form) {
            form.style.display = 'none';
            // Reset form
            const tourForm = document.getElementById('tour-form');
            if (tourForm) {
                tourForm.reset();
                console.log('Form reset');
            }
        }
    }

    // Handle tour form submission
    function handleTourFormSubmission() {
        console.log('handleTourFormSubmission() called');
        const form = document.getElementById('tour-form');
        if (!form) return;

        const formData = new FormData(form);

        // Basic validation
        const tourName = formData.get('tour_name').trim();
        const destination = formData.get('destination').trim();
        const price = parseFloat(formData.get('price'));
        const duration = parseInt(formData.get('duration'));
        const image = formData.get('image');

        console.log('Form data:', { tourName, destination, price, duration, image: image ? image.name : 'none' });

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

        // Here you would typically send the data to the server
        // For now, we'll just log it and show a success message
        console.log('Tour data valid, processing...');

        // Simulate form submission
        alert('Tour added successfully! (Note: Backend processing needs to be implemented)');

        // Hide the form and reset it
        hideAddTourForm();
    }

    // Setup tour search functionality
    function setupTourSearch() {
        // You can implement search functionality here
        // This would filter the tours table based on search input
    }

    // Setup tour pagination
    function setupTourPagination() {
        // You can implement pagination here if needed
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToursManagement);
    } else {
        initToursManagement();
    }

    // Expose functions globally if needed
    window.ToursManager = {
        editTour: editTour,
        deleteTour: deleteTour,
        addNewTour: addNewTour
    };

})();