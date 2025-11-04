<?php
include 'Database/db_config.php';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully<br>";

// Show tables
$result = $conn->query("SHOW TABLES");
echo "<h3>Tables:</h3>";
while ($row = $result->fetch_array()) {
    echo $row[0] . "<br>";
}

// Check tours table structure
echo "<h3>Tours table structure:</h3>";
$result = $conn->query("DESCRIBE tours");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}

// Check if tours table has data
echo "<h3>Tours data:</h3>";
$result = $conn->query("SELECT id, title, location, price, duration, description FROM tours LIMIT 5");
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . ", Title: " . $row['title'] . ", Location: " . $row['location'] . ", Price: " . $row['price'] . "<br>";
}

$conn->close();
?>
