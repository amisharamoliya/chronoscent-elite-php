<link rel="stylesheet" href="../assets/css/orders.css?v=2">
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/auth.php';
require_admin();

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    // Fetch order details before update
    $stmt = $pdo->prepare("SELECT status, email, name FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $orderData = $stmt->fetch();

    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $order_id]);
    
    // Trigger email if status changed to Shipped or Delivered
    if ($orderData && $orderData['status'] !== $status) {
        if ($status == 'Shipped' || $status == 'Delivered') {
            require_once '../includes/mailer.php';
            $subject = "Your Order is $status!";
            $body = "Hello " . $orderData['name'] . ",\n\nYour order #INV-" . str_pad($order_id, 6, '0', STR_PAD_LEFT) . " has been marked as $status.\nThank you for shopping with ChronoScent Elite!";
            send_email($orderData['email'], $subject, $body);
        }
    }
    
    header("Location: orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Orders - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-orders.css?v=2">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar orders-style-1">
            <h4 class="text-center mb-4 text-white fw-bold">Admin Panel</h4>
            <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="products.php"><i class="bi bi-box me-2"></i> Products</a>
            <a href="orders.php" class="active"><i class="bi bi-cart me-2"></i> Orders</a>
            <a href="users.php"><i class="bi bi-people me-2"></i> Users</a>
            <a href="reviews.php"><i class="bi bi-star me-2"></i> Reviews</a>
            <a href="../index.php" target="_blank"><i class="bi bi-globe me-2"></i> View Site</a>
            <a href="logout.php" class="text-danger mt-5"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </div>
        
        <div class="main-content flex-grow-1 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Order Management</h2>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                </td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <form method="POST" class="d-flex align-items-center">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="form-select form-select-sm w-auto me-2" onchange="this.form.submit()">
                                            <option value="Pending" <?php if($order['status']=='Pending') echo 'selected';?>>Pending</option>
                                            <option value="Confirmed" <?php if($order['status']=='Confirmed') echo 'selected';?>>Confirmed</option>
                                            <option value="Shipped" <?php if($order['status']=='Shipped') echo 'selected';?>>Shipped</option>
                                            <option value="Delivered" <?php if($order['status']=='Delivered') echo 'selected';?>>Delivered</option>
                                            <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected';?>>Cancelled</option>
                                        </select>
                                    </form>
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
