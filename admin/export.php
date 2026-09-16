<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/auth.php';
require_admin();

if (!isset($_GET['type'])) {
    die("Export type not specified.");
}

$type = $_GET['type'];
$output = fopen('php://output', 'w');

if ($type === 'orders') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=orders_export_' . date('Y-m-d') . '.csv');
    
    // Headers
    fputcsv($output, ['Order ID', 'Customer Name', 'Email', 'Phone', 'Address', 'City', 'State', 'Pincode', 'Total Amount', 'Discount', 'Payment Method', 'Status', 'Date']);
    
    $stmt = $pdo->query("SELECT id, name, email, phone, address, city, state, pincode, total_amount, discount, payment_method, status, created_at FROM orders ORDER BY created_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} 
elseif ($type === 'users') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=users_export_' . date('Y-m-d') . '.csv');
    
    // Headers
    fputcsv($output, ['User ID', 'Name', 'Email', 'Phone', 'Joined Date']);
    
    $stmt = $pdo->query("SELECT id, name, email, phone, created_at FROM users ORDER BY created_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
}
else {
    die("Invalid export type.");
}

fclose($output);
exit;
?>
