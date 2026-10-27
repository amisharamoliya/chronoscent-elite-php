<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode([]);
    exit;
}

$query = "%" . trim($_GET['q']) . "%";

try {
    $stmt = $pdo->prepare("SELECT id, name, price, discount_price, image, category, profile FROM products WHERE name LIKE ? OR brand LIKE ? OR category LIKE ? OR profile LIKE ? LIMIT 5");
    $stmt->execute([$query, $query, $query, $query]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
