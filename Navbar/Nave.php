<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="Home.php">Travel & Tours</a>
        </div>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="Home.php" class="nav-link">Home</a>
            </li>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (isset($_SESSION['user_id'])) {
                // User is logged in, show avatar with dropdown
                $fullName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
                
                // Extract first name from full name or email
                if (isset($_SESSION['first_name'])) {
                    $userName = $_SESSION['first_name'];
                } else {
                    // Extract first name from full name or email
                    $nameParts = explode(' ', $fullName);
                    $userName = $nameParts[0];
                    
                    // If it's an email, extract the part before @
                    if (strpos($userName, '@') !== false) {
                        $userName = explode('@', $userName)[0];
                    }
                }
                ?>
                <li class="nav-item auth-container">
                    <div class="user-avatar" onclick="toggleDropdown()">
                        <img src="Icons/user.png" alt="User Avatar" class="avatar-img">
                        <span class="user-name hidden md:inline"><?php echo htmlspecialchars($userName); ?></span>
                        <i class="dropdown-arrow">▼</i>
                    </div>
                    <div class="auth-dropdown" id="userDropdown">
                        <a href="profile.php" class="dropdown-item">
                            <i class="icon">👤</i> My Profile
                        </a>
                        <a href="bookings.php" class="dropdown-item">
                            <i class="icon">📋</i> My Bookings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="?logout=1" class="dropdown-item logout">
                            <i class="icon">🚪</i> Logout
                        </a>
                    </div>
                </li>
                <?php
            } else {
                // User is not logged in, show login/register buttons
                ?>
                <li class="nav-item">
                    <a href="Login.php" class="nav-link custom-btn btn-1">Login</a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
</nav>

<?php
// Handle logout
if (isset($_GET['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: Home.php");
    exit();
}
?>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const avatar = document.querySelector('.user-avatar');
    
    if (!avatar.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
