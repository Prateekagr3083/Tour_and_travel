// Tours Management JavaScript
(function() {
    'use strict';

    // Initialize tours management
    function initToursManagement() {
        console.log('initToursManagement() called');
        const addBtn = document.getElementById('add-tour-btn');
        const modal = document.getElementById('addTourModal');
        console.log('Add Tour button found:', addBtn);
        console.log('Add Tour modal found:', modal);

        // Check if add tour button or modal exists to initialize
        if (!addBtn && !modal) {
            console.warn('Neither Add Tour button nor modal found. Exiting init.');
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
        console.log('Add Tour button:', addBtn);
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Add Tour button clicked');
                addNewTour();
            });
        } else {
            console.error('Add Tour button not found');
        }
    }

    // Edit tour function
    function editTour(tourId) {
        console.log('Editing tour:', tourId);
        // Redirect to edit tour page
        window.location.href = 'edit_tour.php?id=' + tourId;
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
        const modal = document.getElementById('addTourModal');
        if (modal) {
            console.log('Modal found, displaying it');
            modal.style.display = 'block';
        } else {
            console.error('Add Tour modal not found!');
            alert('Add Tour modal not found!');
        }
    }

    // Hide add tour modal
    function hideAddTourModal() {
        const modal = document.getElementById('addTourModal');
        if (modal) {
            modal.style.display = 'none';
            // Reset form inside modal
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
            }
        }
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
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addNewTour();
            });
        }

        // Cancel add tour modal button
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'cancelAddTourBtn') {
                e.preventDefault();
                hideAddTourModal();
            }
        });
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
