// Tours Management JavaScript
(function() {
    'use strict';

    // Initialize tours management
    function initToursManagement() {
        if (!document.querySelector('.tours-table')) {
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
        const addBtn = document.querySelector('.add-btn');
        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addNewTour();
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
        console.log('Adding new tour');
        // Implement add new tour logic here
        // This would typically open a modal or redirect to an add page
        alert('Add new tour functionality');
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
