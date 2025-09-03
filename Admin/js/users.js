// Users Management JavaScript
(function() {
    'use strict';

    // Initialize users management
    function initUsersManagement() {
        if (!document.querySelector('.users-table')) {
            return;
        }

        // Add event listeners for user actions
        setupUserActionListeners();
    }

    // Setup user action listeners
    function setupUserActionListeners() {
        // Edit user functionality
        document.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-btn');
            if (editBtn) {
                e.preventDefault();
                const userId = editBtn.closest('tr').querySelector('td:first-child').textContent;
                editUser(userId);
            }
        });

        // Delete user functionality
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.delete-btn');
            if (deleteBtn) {
                e.preventDefault();
                const userId = deleteBtn.closest('tr').querySelector('td:first-child').textContent;
                deleteUser(userId);
            }
        });
    }

    // Edit user function
    function editUser(userId) {
        console.log('Editing user:', userId);
        // Implement user editing logic here
        alert('Edit user functionality for ID: ' + userId);
    }

    // Delete user function
    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            console.log('Deleting user:', userId);
            // Implement user deletion logic here
            alert('Delete user functionality for ID: ' + userId);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUsersManagement);
    } else {
        initUsersManagement();
    }

    // Expose functions globally if needed
    window.UsersManager = {
        editUser: editUser,
        deleteUser: deleteUser
    };

})();
