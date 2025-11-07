<?php
include 'Database/db_connect.php';

echo "Tours table structure:\n";
$result = $conn->query('DESCRIBE tours');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\nDestinations table structure:\n";
$result = $conn->query('DESCRIBE destinations');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\nTour packages table structure:\n";
$result = $conn->query('DESCRIBE tour_packages');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

$conn->close();
?>
