(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Add Tour JS loaded');

        const addBtn = document.getElementById('add-tour-btn');
        console.log('Add button element:', addBtn);

        if (addBtn) {
            addBtn.addEventListener('click', function(e) {
                console.log('Add Tour button clicked!');
                e.preventDefault();

                const form = document.querySelector('#add-tour-form');
                console.log('Form element:', form);

                if (form) {
                    form.style.display = 'block';
                    console.log('Form displayed');

                    form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                    const firstInput = form.querySelector('input');
                    if (firstInput) {
                        firstInput.focus();
                    }
                } else {
                    console.error('Form not found!');
                    alert('Form not found! Please check the page structure.');
                }
            });

            console.log('Event listener attached to add button');
        } else {
            console.error('Add Tour button not found!');
        }

        const cancelBtn = document.getElementById('cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('add-tour-form');
                if (form) {
                    form.style.display = 'none';
                    const tourForm = document.getElementById('tour-form');
                    if (tourForm) {
                        tourForm.reset();
                    }
                }
            });
        }

        // No form submit event listener to allow default form submission behavior
    });

    console.log('Add Tour JS initialized');
})();