<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/auth.php';
require_admin();

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
$total_reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - ChronoScent Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">

    <link rel="stylesheet" href="../assets/css/admin-dashboard.css?v=2">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar" style="width: 250px;">
            <h4 class="text-center mb-4 text-white fw-bold">Admin Panel</h4>
            <a href="dashboard.php" class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="products.php"><i class="bi bi-box me-2"></i> Products</a>
            <a href="orders.php"><i class="bi bi-cart me-2"></i> Orders</a>
            <a href="users.php"><i class="bi bi-people me-2"></i> Users</a>
            <a href="reviews.php"><i class="bi bi-star me-2"></i> Reviews</a>
            <a href="../index.php" target="_blank"><i class="bi bi-globe me-2"></i> View Site</a>
            <a href="logout.php" class="text-danger mt-5"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </div>

        <div class="main-content flex-grow-1 bg-light">
            <h2 class="mb-4">Dashboard Overview</h2>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card bg-primary text-white border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <h2 class="display-5 fw-bold"><?php echo $total_users; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-success text-white border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <h2 class="display-5 fw-bold"><?php echo $total_orders; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-warning text-dark border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Revenue</h5>
                            <h2 class="display-5 fw-bold">$<?php echo number_format($total_revenue ?: 0, 2); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card bg-info text-dark border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">Products</h5>
                            <h2 class="display-5 fw-bold"><?php echo $total_products; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
