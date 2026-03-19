// BookTour Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize image slider
    initImageSlider();

    // Initialize dynamic form fields
    initDynamicFields();

    // Handle form submission
    const form = document.getElementById('booking-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }

            const btn = document.querySelector('.book-btn');
            if (btn) {
                btn.textContent = 'Processing...';
                btn.disabled = true;
            }
        });
    }

    // Add smooth scrolling for messages
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        message.style.animation = 'fadeIn 0.5s ease-out';
    });
});

// Initialize image slider functionality
function initImageSlider() {
    const images = document.querySelectorAll('.gallery-image');
    const dots = document.querySelectorAll('.dot');

    if (images.length === 0) return;

    let currentIndex = 0;

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
}

// Initialize dynamic form fields based on number of people
function initDynamicFields() {
    const numPeopleInput = document.getElementById('num_people');
    const personFieldsContainer = document.getElementById('person-fields');

    if (!numPeopleInput || !personFieldsContainer) return;

    numPeopleInput.addEventListener('input', function() {
        const numPeople = parseInt(this.value);
        if (numPeople > 0) {
            generatePersonFields(numPeople);
        } else {
            // Clear fields if invalid input
            personFieldsContainer.innerHTML = '';
            document.getElementById('total-price').textContent = '₹0.00';
        }
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
    const numPeopleInput = document.getElementById('num_people');
    const totalPriceElement = document.getElementById('total-price');

    if (!numPeopleInput || !totalPriceElement) return;

    const numPeople = parseInt(numPeopleInput.value) || 0;

    // Get price from the price-per-person element
    const pricePerPersonElement = document.getElementById('price-per-person');
    if (!pricePerPersonElement) return;

    const priceText = pricePerPersonElement.textContent;
    const pricePerPerson = parseFloat(priceText.replace('₹', '').replace(',', '')) || 0;

    const totalPrice = numPeople * pricePerPerson;
    totalPriceElement.textContent = '₹' + totalPrice.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Form validation
function validateForm() {
    const numPeople = document.getElementById('num_people').value;
    if (!numPeople || numPeople === '' || parseInt(numPeople) < 1) {
        alert('Please enter a valid number of people (minimum 1).');
        return false;
    }

    const numPeopleInt = parseInt(numPeople);
    let isValid = true;
    let errorMessage = '';

    for (let i = 1; i <= numPeopleInt; i++) {
        const name = document.getElementById(`name_${i}`).value.trim();
        const age = document.getElementById(`age_${i}`).value;
        const gender = document.getElementById(`gender_${i}`).value;

        if (!name) {
            errorMessage += `Please enter name for Person ${i}.\n`;
            isValid = false;
        }

        if (!age || age < 1 || age > 120) {
            errorMessage += `Please enter a valid age for Person ${i}.\n`;
            isValid = false;
        }

        if (!gender) {
            errorMessage += `Please select gender for Person ${i}.\n`;
            isValid = false;
        }
    }

    if (!isValid) {
        alert(errorMessage);
    }

    return isValid;
}

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
`;
document.head.appendChild(style);
