<?php
require_once __DIR__ . '/../includes/db_config.php';
$conn = getDbConnection();
$sql = file_get_contents(__DIR__ . '/init_clientes.sql');
$statements = explode(';', $sql);
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if ($stmt) {
        if ($conn->query($stmt)) {
            echo 'OK: ' . substr($stmt, 0, 70) . '...' . "\n";
        } else {
            echo 'ERR: ' . $conn->error . "\n";
        }
    }
}
$r = $conn->query("SHOW TABLES LIKE 'cliente%'");
echo "\nTables created:\n";
while ($row = $r->fetch_row()) {
    echo '  - ' . $row[0] . "\n";
}
$conn->close();

