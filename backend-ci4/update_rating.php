<?php
// Update test data dengan rating

$host = 'localhost';
$db = 'db11';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update rating untuk laporan test
    $stmt = $pdo->prepare('UPDATE tb_laporan SET rating_pelapor = 4 WHERE nomor_laporan = ?');
    $stmt->execute(['20260610004']);
    
    echo "✓ Update rating_pelapor untuk laporan 20260610004 ke nilai 4\n";
    echo "✓ Rows Affected: " . $stmt->rowCount() . "\n\n";
    
    // Verify
    $verify = $pdo->prepare('SELECT nomor_laporan, rating_pelapor, kerusakan FROM tb_laporan WHERE nomor_laporan = ?');
    $verify->execute(['20260610004']);
    $data = $verify->fetch(PDO::FETCH_ASSOC);
    
    echo "Data Setelah Update:\n";
    echo "- Nomor Laporan: " . $data['nomor_laporan'] . "\n";
    echo "- Rating: " . $data['rating_pelapor'] . "\n";
    echo "- Keluhan: " . $data['kerusakan'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
