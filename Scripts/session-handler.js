// Session handling logic for automatic logout on tab/window close
(function() {
    'use strict';
    
    // Configuration
    const SESSION_TIMEOUT = 30 * 60 * 1000; // 30 minutes in milliseconds
    const HEARTBEAT_INTERVAL = 60000; // Check every minute
    const SESSION_KEY = 'lastActivityTime';
    const LOGOUT_ENDPOINT = 'Database/logout.php'; // PHP endpoint for server-side logout
    
    // Initialize session handling
    function initSessionHandling() {
        // Track user activity
        trackUserActivity();
        
        // Handle tab/window close
        handleWindowClose();
        
        // Check for session timeout
        checkSessionTimeout();
        
        // Set up periodic checks
        setInterval(checkSessionTimeout, HEARTBEAT_INTERVAL);
    }
    
    // Track user interactions to update last activity
    function trackUserActivity() {
        const events = ['click', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        
        events.forEach(event => {
            window.addEventListener(event, updateLastActivity, { passive: true });
        });
        
        // Initial activity update
        updateLastActivity();
    }
    
    // Update last activity timestamp
    function updateLastActivity() {
        const now = Date.now();
        localStorage.setItem(SESSION_KEY, now);
        
        // Also update server-side session via AJAX
        if (navigator.sendBeacon) {
            navigator.sendBeacon('Database/heartbeat.php', JSON.stringify({ action: 'heartbeat', timestamp: now }));
        }
    }
    
    // Handle tab/window close
    function handleWindowClose() {
        window.addEventListener('beforeunload', function(e) {
            // Perform logout actions
            performLogout();
            
            // Optional: Show confirmation dialog (uncomment if needed)
            // e.preventDefault();
            // e.returnValue = '';
        });
        
        // Handle visibility change (tab switching)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                // Tab is hidden, could implement additional checks
                checkSessionTimeout();
            }
        });
    }
    
    // Check if session has expired
    function checkSessionTimeout() {
        const lastActivity = localStorage.getItem(SESSION_KEY);
        const now = Date.now();
        
        if (lastActivity && (now - parseInt(lastActivity) > SESSION_TIMEOUT)) {
            performLogout();
        }
    }
    
    // Perform logout actions
    function performLogout() {
        // Clear client-side session data
        clearClientSession();
        
        // Send logout request to server
        sendServerLogout();
        
        // Redirect to login page
        redirectToLogin();
    }
    
    // Clear client-side session data
    function clearClientSession() {
        // Clear all session-related localStorage items
        localStorage.removeItem(SESSION_KEY);
        
        // Clear any other auth-related data
        sessionStorage.clear();
        
        // Clear cookies if needed
        document.cookie.split(";").forEach(function(c) {
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });
    }
    
    // Send logout request to server
    function sendServerLogout() {
        // Use sendBeacon for reliable async request on page unload
        if (navigator.sendBeacon) {
            navigator.sendBeacon(LOGOUT_ENDPOINT, JSON.stringify({ action: 'logout' }));
        } else {
            // Fallback for older browsers
            const xhr = new XMLHttpRequest();
            xhr.open('POST', LOGOUT_ENDPOINT, false); // synchronous
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(JSON.stringify({ action: 'logout' }));
        }
    }
    
    // Redirect to login page
    function redirectToLogin() {
        // Only redirect if not already on login page
        if (!window.location.pathname.includes('login') && !window.location.pathname.includes('Login')) {
            window.location.href = '/Login.php';
        }
    }
    
    // Check if user is logged in
    function isUserLoggedIn() {
        // Check PHP session (server-side)
        return document.cookie.includes('PHPSESSID=');
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSessionHandling);
    } else {
        initSessionHandling();
    }
    
    // Expose functions globally if needed
    window.SessionHandler = {
        logout: performLogout,
        updateActivity: updateLastActivity,
        checkTimeout: checkSessionTimeout
    };
})();
