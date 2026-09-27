<?php
require 'config/database.php';

// Replace Perfume with Parfum in product names
$pdo->exec("UPDATE products SET name = REPLACE(name, 'Perfume', 'Parfum')");

// Fix any double 'Parfum Parfum' 
$pdo->exec("UPDATE products SET name = REPLACE(name, 'Parfum Parfum', 'Parfum')");

echo "Database updated successfully!\n";
?>
