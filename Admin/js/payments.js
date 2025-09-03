// Payments Management JavaScript
(function() {
    'use strict';

    // Initialize payments management
    function initPaymentsManagement() {
        if (!document.querySelector('.payments-table')) {
            return;
        }

        // Add event listeners for payment actions
        setupPaymentActionListeners();

        // Add filter functionality
        setupPaymentFilters();

        // Add transaction details toggle
        setupTransactionDetails();
    }

    // Setup payment action listeners
    function setupPaymentActionListeners() {
        // View payment details
        document.addEventListener('click', function(e) {
            const viewBtn = e.target.closest('.view-btn');
            if (viewBtn) {
                e.preventDefault();
                const paymentId = viewBtn.closest('tr').querySelector('td:first-child').textContent;
                viewPayment(paymentId);
            }
        });

        // Process refund
        document.addEventListener('click', function(e) {
            const refundBtn = e.target.closest('.refund-btn');
            if (refundBtn) {
                e.preventDefault();
                const paymentId = refundBtn.closest('tr').querySelector('td:first-child').textContent;
                processRefund(paymentId);
            }
        });
    }

    // View payment function
    function viewPayment(paymentId) {
        console.log('Viewing payment:', paymentId);
        // Implement payment view logic here
        alert('View payment details for ID: ' + paymentId);
    }

    // Process refund function
    function processRefund(paymentId) {
        if (confirm('Are you sure you want to process a refund for this payment?')) {
            console.log('Processing refund for payment:', paymentId);
            // Implement refund processing logic here
            alert('Refund processing for payment ID: ' + paymentId);
        }
    }

    // Setup payment filter functionality
    function setupPaymentFilters() {
        const filterForm = document.querySelector('.filter-form');
        if (!filterForm) return;

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Implement filter logic here
            alert('Filter functionality to be implemented');
        });
    }

    // Setup transaction details toggle
    function setupTransactionDetails() {
        document.addEventListener('click', function(e) {
            const transactionRow = e.target.closest('.payments-table tr');
            if (transactionRow && !e.target.closest('.payment-action-btn')) {
                const detailsRow = transactionRow.nextElementSibling;
                if (detailsRow && detailsRow.classList.contains('transaction-details-row')) {
                    detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
                }
            }
        });
    }

    // Format currency
    function formatCurrency(amount, currency = 'USD') {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency
        }).format(amount);
    }

    // Update payment status
    function updatePaymentStatus(paymentId, newStatus) {
        // This would typically send an AJAX request to update the payment status
        console.log('Updating payment', paymentId, 'to status:', newStatus);
        // Implement AJAX call here
    }

    // Calculate total payments
    function calculateTotalPayments() {
        const paymentRows = document.querySelectorAll('.payments-table tbody tr');
        let total = 0;

        paymentRows.forEach(row => {
            const amountCell = row.querySelector('td:nth-child(4)'); // Assuming amount is in 4th column
            if (amountCell) {
                const amount = parseFloat(amountCell.textContent.replace(/[^0-9.-]+/g, ''));
                if (!isNaN(amount)) {
                    total += amount;
                }
            }
        });

        return total;
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPaymentsManagement);
    } else {
        initPaymentsManagement();
    }

    // Expose functions globally if needed
    window.PaymentsManager = {
        viewPayment: viewPayment,
        processRefund: processRefund,
        updatePaymentStatus: updatePaymentStatus,
        calculateTotalPayments: calculateTotalPayments,
        formatCurrency: formatCurrency
    };

})();
