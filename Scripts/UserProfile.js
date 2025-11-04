// User Profile Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Handle edit mode toggle
    window.toggleEditMode = function() {
        const form = document.getElementById('edit-profile-form');
        const displayMode = document.querySelector('.profile-details');
        const editBtn = document.querySelector('.edit-btn');

        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            displayMode.style.display = 'none';
            editBtn.textContent = 'Cancel Edit';
        } else {
            form.style.display = 'none';
            displayMode.style.display = 'block';
            editBtn.textContent = 'Edit Profile';
        }
    };

    // Handle form submission with loading state
    const form = document.querySelector('.edit-profile-form form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('.save-btn');
            if (submitBtn) {
                submitBtn.textContent = 'Saving...';
                submitBtn.disabled = true;
            }
        });
    }

    // Add smooth animations
    const messages = document.querySelectorAll('.message');
    messages.forEach(message => {
        message.style.animation = 'fadeIn 0.5s ease-out';
    });

    // Add input validation
    const inputs = document.querySelectorAll('#edit-profile-form input, #edit-profile-form select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
});

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;

    // Remove existing error messages
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }

    // Basic validation
    if (field.hasAttribute('required') && value === '') {
        showFieldError(field, `${fieldName.replace('_', ' ')} is required.`);
        return false;
    }

    // Contact number validation
    if (fieldName === 'contact_number') {
        const phoneRegex = /^[0-9+\-\s()]+$/;
        if (!phoneRegex.test(value)) {
            showFieldError(field, 'Please enter a valid contact number.');
            return false;
        }
    }

    // Name validation
    if (fieldName === 'first_name' || fieldName === 'last_name') {
        const nameRegex = /^[a-zA-Z\s]+$/;
        if (!nameRegex.test(value)) {
            showFieldError(field, 'Name can only contain letters and spaces.');
            return false;
        }
    }

    return true;
}

function showFieldError(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = 'red';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    field.parentNode.appendChild(errorDiv);
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

    .save-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .edit-profile-form {
        margin-top: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 0.25rem;
        font-size: 1rem;
    }

    .form-actions {
        margin-top: 1.5rem;
        display: flex;
        gap: 1rem;
    }

    .save-btn,
    .cancel-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    .save-btn {
        background: #04543a;
        color: white;
    }

    .save-btn:hover {
        background: #094b35;
    }

    .cancel-btn {
        background: #6c757d;
        color: white;
    }

    .cancel-btn:hover {
        background: #545b62;
    }
`;
document.head.appendChild(style);
