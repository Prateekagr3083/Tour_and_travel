// Tour Details Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add smooth scrolling to reviews section
    const reviewsSection = document.querySelector('.reviews-section');
    if (reviewsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        reviewsSection.style.opacity = '0';
        reviewsSection.style.transform = 'translateY(20px)';
        reviewsSection.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(reviewsSection);
    }

    // Optional: Add rating stars display
    const ratings = document.querySelectorAll('.review-rating');
    ratings.forEach(rating => {
        const ratingValue = parseInt(rating.textContent.split('/')[0]);
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= ratingValue ? '★' : '☆';
        }
        rating.innerHTML = `Rating: ${stars}`;
    });

    // Back to tours button functionality removed - using HTML button instead
});

// Add styles for the back button
const style = document.createElement('style');
style.textContent = `
    .back-button {
        background-color: #04543a;
        color: #ffffff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 1rem;
        margin-top: 1rem;
        transition: background-color 0.3s ease;
    }
    .back-button:hover {
        background-color: #094b35;
    }
`;
document.head.appendChild(style);
