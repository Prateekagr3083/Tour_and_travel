<?php
include 'Database/db_connect.php';

$result = $conn->query('UPDATE tours SET destination_id = 1, package_id = 1 WHERE id = 2');
echo 'Updated tour: ' . $conn->affected_rows . PHP_EOL;

$conn->close();
?>
