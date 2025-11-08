// Tour Details Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Image Slider Functionality
    const images = document.querySelectorAll('.gallery-image');
    const dots = document.querySelectorAll('.dot');
    let currentIndex = 0;
    let autoSlideInterval;

    // Function to show slide
    function showSlide(index) {
        // Hide all images
        images.forEach(img => img.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Show current image
        images[index].classList.add('active');
        dots[index].classList.add('active');
        currentIndex = index;
    }

    // Function to next slide
    function nextSlide() {
        currentIndex = (currentIndex + 1) % images.length;
        showSlide(currentIndex);
    }

    // Function to previous slide
    function prevSlide() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        showSlide(currentIndex);
    }

    // Auto slide functionality
    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 3000); // Change image every 3 seconds
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    // Add click event to dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            stopAutoSlide();
            startAutoSlide(); // Restart auto slide after manual interaction
        });
    });

    // Pause auto slide on hover
    const slider = document.querySelector('.image-slider');
    if (slider) {
        slider.addEventListener('mouseenter', stopAutoSlide);
        slider.addEventListener('mouseleave', startAutoSlide);
    }

    // Start auto slide if there are multiple images
    if (images.length > 1) {
        startAutoSlide();
    }

    // Initialize dynamic form fields if booking section exists
    if (document.getElementById('booking-section')) {
        initDynamicFields();
    }

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

// Toggle booking form visibility
function toggleBookingForm() {
    const bookingSection = document.getElementById('booking-section');
    if (bookingSection.style.display === 'none' || bookingSection.style.display === '') {
        bookingSection.style.display = 'block';
        bookingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        bookingSection.style.display = 'none';
    }
}

// Initialize dynamic form fields based on number of people
function initDynamicFields() {
    const numPeopleSelect = document.getElementById('num_people');
    const personFieldsContainer = document.getElementById('person-fields');

    if (!numPeopleSelect || !personFieldsContainer) return;

    numPeopleSelect.addEventListener('change', function() {
        const numPeople = parseInt(this.value);
        generatePersonFields(numPeople);
        updateTotalPrice();
    });
}

// Generate dynamic fields for each person
function generatePersonFields(numPeople) {
    const container = document.getElementById('person-fields');
    if (!container) return;

    container.innerHTML = ''; // Clear existing fields

    for (let i = 1; i <= numPeople; i++) {
        const personCard = document.createElement('div');
        personCard.className = 'person-card';
        personCard.innerHTML = `
            <h4>Person ${i}</h4>
            <div class="form-group">
                <label for="name_${i}">Full Name:</label>
                <input type="text" id="name_${i}" name="name_${i}" required placeholder="Enter full name">
            </div>
            <div class="form-group">
                <label for="age_${i}">Age:</label>
                <input type="number" id="age_${i}" name="age_${i}" required min="1" max="120" placeholder="Enter age">
            </div>
            <div class="form-group">
                <label for="gender_${i}">Gender:</label>
                <select id="gender_${i}" name="gender_${i}" required>
                    <option value="">Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="health_${i}">Health Conditions (Optional):</label>
                <textarea id="health_${i}" name="health_${i}" rows="2" placeholder="Any medical conditions or special requirements..."></textarea>
            </div>
        `;
        container.appendChild(personCard);
    }
}

// Update total price based on number of people
function updateTotalPrice() {
    const numPeopleSelect = document.getElementById('num_people');
    const totalPriceElement = document.getElementById('total-price');

    if (!numPeopleSelect || !totalPriceElement) return;

    const numPeople = parseInt(numPeopleSelect.value) || 0;

    // Get price from the summary (assuming it's in the format "₹X.XX")
    const priceText = document.querySelector('.summary-item span:last-child').textContent;
    const pricePerPerson = parseFloat(priceText.replace('₹', '').replace(',', '')) || 0;

    const totalPrice = numPeople * pricePerPerson;
    totalPriceElement.textContent = '₹' + totalPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Add styles for the back button and booking form
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

    .person-card {
        animation: slideInUp 0.3s ease-out;
    }

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

    .booking-section {
        animation: fadeIn 0.5s ease-out;
    }

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
`;
document.head.appendChild(style);
