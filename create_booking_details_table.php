<?php
include 'Database/db_config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully<br>";

// Create booking_details table
$sql = 








"CREATE TABLE IF NOT EXISTS booking_details (
    id BIGINT(20) AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT(20) NOT NULL,
    person_name VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    health_conditions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table booking_details created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Also add num_people and total_price columns to bookings table if they don't exist
$alter_sql = "ALTER TABLE bookings
    ADD COLUMN IF NOT EXISTS num_people INT DEFAULT 1,
    ADD COLUMN IF NOT EXISTS total_price DECIMAL(10,2) DEFAULT 0.00";

if ($conn->query($alter_sql) === TRUE) {
    echo "Bookings table updated successfully<br>";
} else {
    echo "Error updating bookings table: " . $conn->error . "<br>";
}

$conn->close();
?>
