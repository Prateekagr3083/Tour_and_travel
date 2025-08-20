// Session handling logic for automatic logout on tab/window close only
(function() {
    'use strict';
    
    // Configuration
    const LOGOUT_ENDPOINT = 'Database/logout.php'; // PHP endpoint for server-side logout
    let isLeaving = false; // Flag to track if user is actually leaving
    
    // Initialize session handling
    function initSessionHandling() {
        // Only initialize if user is logged in
        if (!isUserLoggedIn()) {
            return;
        }
        
        // Set flag when user is leaving the page
        handleWindowClose();
    }
    
    // Handle tab/window close with flag system
    function handleWindowClose() {
        // Set flag when user is leaving the page
        window.addEventListener('beforeunload', function(e) {
            isLeaving = true;
        });
        
        // Handle tab/window close - only logout if actually leaving
        window.addEventListener('unload', function() {
            if (isLeaving) {
                performLogout();
            }
        });
        
        // Handle pagehide for mobile browsers
        window.addEventListener('pagehide', function(e) {
            isLeaving = true;
            performLogout();
        });
        
        // Handle visibility change for tab switching
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                // Small delay to distinguish between refresh and actual close
                setTimeout(() => {
                    if (document.visibilityState === 'hidden') {
                        isLeaving = true;
                        performLogout();
                    }
                }, 100);
            }
        });
    }
    
    // Perform logout actions
    function performLogout() {
        // Send logout request to server
        sendServerLogout();
        
        // Clear client-side session data
        clearClientSession();
    }
    
    // Send logout request to server
    function sendServerLogout() {
        // Use sendBeacon for reliable async request on page unload
        if (navigator.sendBeacon) {
            navigator.sendBeacon(LOGOUT_ENDPOINT, JSON.stringify({ action: 'logout' }));
        } else {
            // Fallback for older browsers
            const xhr = new XMLHttpRequest();
            xhr.open('POST', LOGOUT_ENDPOINT, false);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(JSON.stringify({ action: 'logout' }));
        }
    }
    
    // Clear client-side session data
    function clearClientSession() {
        // Clear all session-related data
        sessionStorage.clear();
        
        // Clear cookies if needed
        document.cookie.split(";").forEach(function(c) {
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });
    }
    
    // Check if user is logged in
    function isUserLoggedIn() {
        // Check PHP session via cookie
        const hasSessionCookie = document.cookie.includes('PHPSESSID=');
        
        // Check if we have any indication of logged in user
        const navHasUser = document.querySelector('.user-avatar') !== null;
        
        return hasSessionCookie && navHasUser;
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSessionHandling);
    } else {
        initSessionHandling();
    }
    
    // Expose functions globally if needed
    window.SessionHandler = {
        logout: performLogout
    };
})();
