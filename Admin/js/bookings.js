// Bookings Management JavaScript
(function() {
    'use strict';

    // Initialize bookings management
    function initBookingsManagement() {
        if (!document.querySelector('.bookings-table')) {
            return;
        }

        // Add event listeners for booking actions
        setupBookingActionListeners();
        
        // Add filter functionality
        setupBookingFilters();
    }

    // Setup booking action listeners
    function setupBookingActionListeners() {
        // View booking details
        document.addEventListener('click', function(e) {
            const viewBtn = e.target.closest('.view-btn');
            if (viewBtn) {
                e.preventDefault();
                const bookingId = viewBtn.closest('tr').querySelector('td:first-child').textContent;
                viewBooking(bookingId);
            }
        });

        // Confirm booking
        document.addEventListener('click', function(e) {
            const confirmBtn = e.target.closest('.confirm-btn');
            if (confirmBtn) {
                e.preventDefault();
                const bookingId = confirmBtn.closest('tr').querySelector('td:first-child').textContent;
                confirmBooking(bookingId);
            }
        });

        // Cancel booking
        document.addEventListener('click', function(e) {
            const cancelBtn = e.target.closest('.cancel-btn');
            if (cancelBtn) {
                e.preventDefault();
                const bookingId = cancelBtn.closest('tr').querySelector('td:first-child').textContent;
                cancelBooking(bookingId);
            }
        });
    }

    // View booking function
    function viewBooking(bookingId) {
        console.log('Viewing booking:', bookingId);
        // Implement booking view logic here
        alert('View booking functionality for ID: ' + bookingId);
    }

    // Confirm booking function
    function confirmBooking(bookingId) {
        if (confirm('Are you sure you want to confirm this booking?')) {
            console.log('Confirming booking:', bookingId);
            // Implement booking confirmation logic here
            alert('Confirm booking functionality for ID: ' + bookingId);
        }
    }

    // Cancel booking function
    function cancelBooking(bookingId) {
        if (confirm('Are you sure you want to cancel this booking?')) {
            console.log('Cancelling booking:', bookingId);
            // Implement booking cancellation logic here
            alert('Cancel booking functionality for ID: ' + bookingId);
        }
    }

    // Setup booking filter functionality
    function setupBookingFilters() {
        const filterForm = document.querySelector('.filter-form');
        if (!filterForm) return;

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Implement filter logic here
            alert('Filter functionality to be implemented');
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBookingsManagement);
    } else {
        initBookingsManagement();
    }

    // Expose functions globally if needed
    window.BookingsManager = {
        viewBooking: viewBooking,
        confirmBooking: confirmBooking,
        cancelBooking: cancelBooking
    };

})();
