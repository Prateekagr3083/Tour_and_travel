<?php
// Admin Sidebar - included in admin pages for navigation
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
        <p>Tour & Travel Management</p>
    </div>

    <ul class="nav-menu">
        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'Dashboard.php' ? 'active' : ''; ?>">
            <a href="Dashboard.php" class="nav-link">
                <i>📊</i> Dashboard
            </a>
        </li>
        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'Users.php' ? 'active' : ''; ?>">
            <a href="Users.php" class="nav-link">
                <i>👥</i> Users
            </a>
        </li>
        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'Tours.php' ? 'active' : ''; ?>">
            <a href="Tours.php" class="nav-link">
                <i>🏨</i> Tours
            </a>
        </li>

        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'Packages.php' ? 'active' : ''; ?>">
            <a href="Packages.php" class="nav-link">
                <i>📦</i> Tour Packages
            </a>
        </li>
        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'TourSchedules.php' ? 'active' : ''; ?>">
            <a href="TourSchedules.php" class="nav-link">
                <i>📅</i> Tour Schedules
            </a>
        </li>
        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'Bookings.php' ? 'active' : ''; ?>">
            <a href="Bookings.php" class="nav-link">
                <i>📋</i> Bookings
            </a>
        </li>
        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'Reviews.php' ? 'active' : ''; ?>">
            <a href="Reviews.php" class="nav-link">
                <i>⭐</i> Reviews
            </a>
        </li>
        <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) === 'Payments.php' ? 'active' : ''; ?>">
            <a href="Payments.php" class="nav-link">
                <i>💳</i> Payments
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i>⚙️</i> Settings
            </a>
        </li>
    </ul>
</aside>
