<?php
include 'Database/db_connect.php';

echo 'Total tours: ';
$result = $conn->query('SELECT COUNT(*) as count FROM tours');
$row = $result->fetch_assoc();
echo $row['count'] . PHP_EOL;

echo 'Total destinations: ';
$result = $conn->query('SELECT COUNT(*) as count FROM destinations');
$row = $result->fetch_assoc();
echo $row['count'] . PHP_EOL;

echo 'Total packages: ';
$result = $conn->query('SELECT COUNT(*) as count FROM tour_packages');
$row = $result->fetch_assoc();
echo $row['count'] . PHP_EOL;

echo 'Sample tours:' . PHP_EOL;
$result = $conn->query('SELECT t.id, t.title, d.name as dest_name, p.name as pkg_name FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id LEFT JOIN tour_packages p ON t.package_id = p.id LIMIT 3');
while ($row = $result->fetch_assoc()) {
    echo 'ID: ' . $row['id'] . ', Title: ' . $row['title'] . ', Dest: ' . $row['dest_name'] . ', Pkg: ' . $row['pkg_name'] . PHP_EOL;
}

echo 'Destinations:' . PHP_EOL;
$result = $conn->query('SELECT * FROM destinations');
while ($row = $result->fetch_assoc()) {
    echo 'ID: ' . $row['id'] . ', Name: ' . $row['name'] . PHP_EOL;
}

echo 'Packages:' . PHP_EOL;
$result = $conn->query('SELECT * FROM tour_packages');
while ($row = $result->fetch_assoc()) {
    echo 'ID: ' . $row['id'] . ', Name: ' . $row['name'] . PHP_EOL;
}

$conn->close();
?>
