<?php
require 'config/database.php';
$stmt = $pdo->query("SELECT id, name FROM products");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
