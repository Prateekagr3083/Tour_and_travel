// BookTour Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Handle booking confirmation
    const bookBtn = document.querySelector('.book-btn');
    if (bookBtn) {
        bookBtn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to book this tour? Your booking will be confirmed after admin approval.')) {
                e.preventDefault();
            }
        });
    }

    // Add smooth scrolling for messages
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        message.style.animation = 'fadeIn 0.5s ease-out';
    });

    // Add loading state to button
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const btn = document.querySelector('.book-btn');
            if (btn) {
                btn.textContent = 'Processing...';
                btn.disabled = true;
            }
        });
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .book-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
`;
document.head.appendChild(style);
