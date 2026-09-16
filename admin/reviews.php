<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/auth.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // get product id to update rating
    $stmt = $pdo->prepare("SELECT product_id FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    $pid = $stmt->fetchColumn();

    $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
    
    // Update product rating
    if ($pid) {
        $avg = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE product_id = ?");
        $avg->execute([$pid]);
        $new_avg = $avg->fetchColumn() ?: 0;
        $pdo->prepare("UPDATE products SET rating = ? WHERE id = ?")->execute([$new_avg, $pid]);
    }
    
    header("Location: reviews.php");
    exit;
}

$stmt = $pdo->query("SELECT r.*, u.name as user_name, p.name as product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Reviews - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin-reviews.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar" style="width: 250px;">
            <h4 class="text-center mb-4 text-warning fw-bold">Admin Panel</h4>
            <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="products.php"><i class="bi bi-box me-2"></i> Products</a>
            <a href="orders.php"><i class="bi bi-cart me-2"></i> Orders</a>
            <a href="users.php"><i class="bi bi-people me-2"></i> Users</a>
            <a href="reviews.php" class="active"><i class="bi bi-star me-2"></i> Reviews</a>
            <a href="../index.php" target="_blank"><i class="bi bi-globe me-2"></i> View Site</a>
            <a href="logout.php" class="text-danger mt-5"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </div>
        
        <div class="main-content flex-grow-1 bg-light">
            <h2 class="mb-4">Review Management</h2>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $rev): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rev['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($rev['user_name']); ?></td>
                                <td>
                                    <?php 
                                    for($i=1; $i<=5; $i++) echo $i <= $rev['rating'] ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-warning"></i>';
                                    ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($rev['title']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($rev['message']); ?></small>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></td>
                                <td>
                                    <a href="reviews.php?delete=<?php echo $rev['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this review?');"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

