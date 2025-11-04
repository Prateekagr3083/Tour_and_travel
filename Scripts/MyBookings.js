// MyBookings Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Add status filter functionality
    const statusFilters = document.querySelectorAll('.status-filter');
    statusFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            const status = this.dataset.status;

            // Remove active class from all filters
            statusFilters.forEach(f => f.classList.remove('active'));
            this.classList.add('active');

            // Filter bookings
            filterBookings(status);
        });
    });

    // Add search functionality
    const searchInput = document.getElementById('booking-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterBookingsBySearch(searchTerm);
        });
    }

    // Add animation to booking cards
    const bookingCards = document.querySelectorAll('.booking-card');
    bookingCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'slideInUp 0.5s ease-out forwards';
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
    });

    // Add hover effects
    bookingCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

function filterBookings(status) {
    const cards = document.querySelectorAll('.booking-card');

    cards.forEach(card => {
        const cardStatus = card.querySelector('.booking-status').textContent.toLowerCase().trim();

        if (status === 'all' || cardStatus.includes(status)) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease-out';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterBookingsBySearch(searchTerm) {
    const cards = document.querySelectorAll('.booking-card');

    cards.forEach(card => {
        const title = card.querySelector('.booking-title').textContent.toLowerCase();
        const destination = card.querySelector('.detail-value').textContent.toLowerCase();

        if (title.includes(searchTerm) || destination.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .status-filter {
        cursor: pointer;
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border-radius: 20px;
        background: #f8f9fa;
        color: #666;
        transition: all 0.3s ease;
    }

    .status-filter.active {
        background: #04543a;
        color: white;
    }

    .status-filter:hover {
        background: #04543a;
        color: white;
    }

    .booking-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .booking-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
`;
document.head.appendChild(style);
