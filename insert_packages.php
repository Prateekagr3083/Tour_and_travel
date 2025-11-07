<?php
include 'Database/db_connect.php';

$result = $conn->query('INSERT INTO tour_packages (name, description, price, duration) VALUES ("Adventure Package", "Full adventure experience", 5000.00, "7 days"), ("Luxury Package", "Premium luxury tour", 15000.00, "10 days")');
echo 'Inserted packages: ' . $conn->affected_rows . PHP_EOL;

$conn->close();
?>
