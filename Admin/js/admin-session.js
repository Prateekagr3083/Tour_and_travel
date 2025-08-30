// Admin Session Handling JavaScript
(function() {
    'use strict';
    
    // Configuration
    const ADMIN_LOGOUT_ENDPOINT = 'logout.php';
    const ADMIN_SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes in milliseconds
    let sessionTimeout;
    let isLeaving = false;
    
    // Initialize admin session handling
    function initAdminSession() {
        if (!isAdminLoggedIn()) {
            return;
        }
        
        // Reset session timeout on any activity
        resetSessionTimeout();
        
        // Set up activity listeners
        setupActivityListeners();
        
        // Handle window close events
        handleWindowClose();
        
        // Check session status periodically
        setInterval(checkSessionStatus, 60000); // Check every minute
    }
    
    // Check if admin is logged in
    function isAdminLoggedIn() {
        // Check for admin session indicators
        const hasAdminElements = document.querySelector('.admin-container') !== null;
        const hasLogoutButton = document.querySelector('.logout-btn') !== null;
        
        return hasAdminElements && hasLogoutButton;
    }
    
    // Reset session timeout
    function resetSessionTimeout() {
        clearTimeout(sessionTimeout);
        sessionTimeout = setTimeout(logoutDueToInactivity, ADMIN_SESSION_TIMEOUT);
    }
    
    // Set up activity listeners
    function setupActivityListeners() {
        const events = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'];
        
        events.forEach(event => {
            document.addEventListener(event, resetSessionTimeout, { passive: true });
        });
    }
    
    // Handle window close events
    function handleWindowClose() {
        window.addEventListener('beforeunload', function(e) {
            isLeaving = true;
        });
        
        window.addEventListener('unload', function() {
            if (isLeaving) {
                performAdminLogout();
            }
        });
        
        window.addEventListener('pagehide', function(e) {
            isLeaving = true;
            performAdminLogout();
        });
    }
    
    // Logout due to inactivity
    function logoutDueToInactivity() {
        if (isAdminLoggedIn()) {
            alert('Your session has expired due to inactivity. You will be logged out.');
            performAdminLogout();
        }
    }
    
    // Perform admin logout
    function performAdminLogout() {
        // Send logout request to server
        sendAdminLogoutRequest();
        
        // Clear client-side data
        clearAdminSessionData();
        
        // Redirect to login page
        window.location.href = 'Login.php';
    }
    
    // Send logout request to server
    function sendAdminLogoutRequest() {
        if (navigator.sendBeacon) {
            navigator.sendBeacon(ADMIN_LOGOUT_ENDPOINT);
        } else {
            // Fallback for older browsers
            const xhr = new XMLHttpRequest();
            xhr.open('GET', ADMIN_LOGOUT_ENDPOINT, false);
            xhr.send();
        }
    }
    
    // Clear admin session data
    function clearAdminSessionData() {
        // Clear session storage
        sessionStorage.clear();
        
        // Clear admin-specific cookies
        document.cookie.split(";").forEach(function(c) {
            const cookie = c.trim();
            if (cookie.startsWith('admin_') || cookie.includes('PHPSESSID')) {
                document.cookie = cookie.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
            }
        });
    }
    
    // Check session status with server
    function checkSessionStatus() {
        if (!isAdminLoggedIn()) {
            return;
        }
        
        // You can implement a heartbeat check here if needed
        // This would ping the server to verify the session is still valid
    }
    
    // Manual logout function
    function manualLogout() {
        if (confirm('Are you sure you want to logout?')) {
            performAdminLogout();
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminSession);
    } else {
        initAdminSession();
    }
    
    // Expose functions globally
    window.AdminSession = {
        logout: manualLogout,
        resetTimeout: resetSessionTimeout
    };
    
    // Add logout confirmation to all logout buttons/links
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.logout-btn') || e.target.closest('[href*="logout"]');
        if (target && target.href && target.href.includes('logout')) {
            e.preventDefault();
            manualLogout();
        }
    });
    
})();
