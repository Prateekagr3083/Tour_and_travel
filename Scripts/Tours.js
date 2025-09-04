// Tours Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to tour cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            const tourId = this.getAttribute('data-tour-id');
            if (tourId) {
                window.location.href = `TourDetails.php?id=${tourId}`;
            }
        });
    });

    // Add click event listeners to "View Details" buttons
    const viewButtons = document.querySelectorAll('.cart-button');
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent card click from firing
            const card = this.closest('.card');
            const tourId = card.getAttribute('data-tour-id');
            if (tourId) {
                window.location.href = `TourDetails.php?id=${tourId}`;
            }
        });
    });

    // Optional: Add smooth scrolling or animations
    // For example, animate cards on load
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
