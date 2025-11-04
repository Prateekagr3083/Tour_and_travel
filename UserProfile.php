// Include session configuration
include 'session_config.php';

session_start();

// User Profile Page

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

// Include database connection
include 'Database/db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT first_name, last_name, contact_number, email, gender, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Tour and Travel</title>
    <link rel="stylesheet" href="CSS/Home.css">
    <link rel="stylesheet" href="CSS/Nave.css">
    <link rel="stylesheet" href="CSS/UserProfile.css">
    <script src="Scripts/UserProfile.js"></script>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'Navbar/Nave.php'; ?>

    <main>
        <div class="profile-container">
            <h1>My Profile</h1>

            <?php if (isset($_SESSION['profile_success'])): ?>
                <div class="message success"><?php echo $_SESSION['profile_success']; unset($_SESSION['profile_success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['profile_error'])): ?>
                <div class="message error"><?php echo $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?></div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar">
                        <img src="Icons/user.png" alt="Profile Picture" onerror="this.src='https://via.placeholder.com/100/ffcc00/000000?text=U'">
                    </div>
                    <div class="user-info">
                        <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                        <p class="member-since">Member since: <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>

                <div class="profile-details">
                    <div class="detail-section">
                        <h3>Personal Information</h3>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <label>First Name:</label>
                                <span><?php echo htmlspecialchars($user['first_name']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Last Name:</label>
                                <span><?php echo htmlspecialchars($user['last_name']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Email:</label>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Contact Number:</label>
                                <span><?php echo htmlspecialchars($user['contact_number']); ?></span>
                            </div>
                            <div class="detail-item">
                                <label>Gender:</label>
                                <span><?php echo htmlspecialchars($user['gender']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button class="edit-btn" onclick="toggleEditMode()">Edit Profile</button>
                        <a href="MyBookings.php" class="bookings-btn">View My Bookings</a>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form (Hidden by default) -->
            <div id="edit-profile-form" class="edit-profile-form" style="display: none;">
                <h3>Edit Profile</h3>
                <form method="POST" action="process_profile_update.php">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number:</label>
                        <input type="tel" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $user['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <button type="button" class="cancel-btn" onclick="toggleEditMode()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
