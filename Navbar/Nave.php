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
                // User is logged in, show avatar
                echo '<li class="nav-item">
                        <div class="user-avatar">
                            <img src="Icons/user.png" alt="User Avatar" class="avatar-img">
                            <i class="bxr bx-user"></i>
                        </div>
                      </li>';
            } else {
                // User is not logged in, show login button
                echo '<li class="nav-item">
                        <a href="Login.php" class="nav-link custom-btn btn-1">Login</a>
                      </li>';
            }
            ?>
        </ul>
    </div>
</nav>
