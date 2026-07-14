<?php
// Initialize CodeIgniter
define('ROOTPATH', __DIR__ . '/');
require_once 'vendor/autoload.php';
require_once 'app/Config/Paths.php';

$paths = new \Config\Paths();
$dotenv = \Dotenv\Dotenv::createImmutable(ROOTPATH);
@$dotenv->load();

$db = \Config\Database::connect();

// Check total records
$total = $db->table('tb_perbaikan')->countAll();
echo "Total records in tb_perbaikan: " . $total . "\n";

// Check by year
$query = "SELECT YEAR(created_at) as year, COUNT(*) as count, GROUP_CONCAT(DISTINCT status_kerusakan) as statuses
          FROM tb_perbaikan 
          WHERE created_at IS NOT NULL 
          GROUP BY YEAR(created_at) 
          ORDER BY year DESC";
$result = $db->query($query)->getResultArray();

echo "\nData by year:\n";
foreach ($result as $row) {
    echo "  {$row['year']}: {$row['count']} records - Status: {$row['statuses']}\n";
}

// Check current year (2026) data
echo "\n\nYear 2026 breakdown:\n";
$query2 = "SELECT status_kerusakan, COUNT(*) as count 
           FROM tb_perbaikan 
           WHERE YEAR(created_at) = 2026 
           GROUP BY status_kerusakan";
$result2 = $db->query($query2)->getResultArray();
foreach ($result2 as $row) {
    echo "  {$row['status_kerusakan']}: {$row['count']}\n";
}
?>
