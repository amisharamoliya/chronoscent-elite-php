<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// Handle Order Status Update
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("SELECT status, email, name FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $orderData = $stmt->fetch();

    if ($orderData) {
        $old_status = $orderData['status'];
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $order_id]);

        // Stock adjustment logic
        if (strcasecmp($status, 'Cancelled') === 0 && strcasecmp($old_status, 'Cancelled') !== 0) {
            $items_stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $items_stmt->execute([$order_id]);
            foreach ($items_stmt->fetchAll() as $item) {
                $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?")->execute([$item['quantity'], $item['product_id']]);
            }
        } elseif (strcasecmp($old_status, 'Cancelled') === 0 && strcasecmp($status, 'Cancelled') !== 0) {
            $items_stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $items_stmt->execute([$order_id]);
            foreach ($items_stmt->fetchAll() as $item) {
                $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?")->execute([$item['quantity'], $item['product_id']]);
            }
        }

        if ($old_status !== $status && ($status == 'Shipped' || $status == 'Delivered')) {
            require_once '../includes/mailer.php';
            $subject = "Your Order is $status!";
            $body = "Hello " . $orderData['name'] . ",\n\nYour order #INV-" . str_pad($order_id, 6, '0', STR_PAD_LEFT) . " has been marked as $status.\nThank you for shopping with ChronoScent Elite!";
            send_email($orderData['email'], $subject, $body);
        }
    }
    header("Location: orders.php" . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Handle Order Deletion
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['delete_order']) && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);
    header("Location: orders.php" . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Search & Status Filters for Orders
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

$query_sql = "SELECT * FROM orders WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query_sql .= " AND (name LIKE ? OR email LIKE ? OR city LIKE ? OR id LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like, $like]);
}

if (!empty($status_filter) && $status_filter !== 'All Orders') {
    $query_sql .= " AND status = ?";
    $params[] = $status_filter;
}

$query_sql .= " ORDER BY created_at DESC";
$orders_stmt = $pdo->prepare($query_sql);
$orders_stmt->execute($params);
$orders = $orders_stmt->fetchAll();
$orders_found_count = count($orders);

// Fetch order items pre-indexed by order_id
$order_items_map = [];
$items_stmt = $pdo->query("SELECT oi.order_id, oi.quantity, p.name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id");
while ($row = $items_stmt->fetch()) {
    $order_items_map[$row['order_id']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Wrapper -->
        <div class="admin-main-wrapper">
            <!-- Topbar -->
            <?php include 'topbar.php'; ?>

            <!-- Main Page Content -->
            <main class="admin-content">

                <!-- Purchases / Orders Card -->
                <div class="purchases-card">
                    <div class="purchases-header">
                        <div>
                            <h5 class="purchases-title">Customer Purchases</h5>
                            <p class="purchases-count"><?= $orders_found_count ?> orders found</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="small text-muted fw-semibold">Status:</label>
                            <form method="GET" class="d-inline">
                                <?php if (!empty($search)): ?>
                                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                <?php endif; ?>
                                <select name="status" class="status-filter-select" onchange="this.form.submit()">
                                    <option value="All Orders" <?= ($status_filter == '' || $status_filter == 'All Orders') ? 'selected' : '' ?>>All Orders</option>
                                    <option value="Pending" <?= $status_filter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Confirmed" <?= $status_filter == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Shipped" <?= $status_filter == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="Delivered" <?= $status_filter == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="Cancelled" <?= $status_filter == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="orders-table align-middle">
                            <thead>
                                <tr>
                                    <th>ORDER ID</th>
                                    <th>DATE</th>
                                    <th>CUSTOMER</th>
                                    <th>ITEMS ORDERED</th>
                                    <th>PAYMENT</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>ORDER STATUS</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($orders) > 0): ?>
                                    <?php foreach ($orders as $order): 
                                        $order_id_formatted = '#' . strtoupper(substr(md5($order['id'] . 'chrono'), 0, 6));
                                        $items = $order_items_map[$order['id']] ?? [];
                                        $current_status = $order['status'] ?: 'Pending';

                                        // Status class mapping
                                        $status_class = 'status-pending';
                                        if ($current_status == 'Delivered') $status_class = 'status-delivered';
                                        elseif ($current_status == 'Shipped') $status_class = 'status-shipped';
                                        elseif ($current_status == 'Cancelled') $status_class = 'status-cancelled';
                                    ?>
                                    <tr>
                                        <!-- ORDER ID -->
                                        <td>
                                            <span class="order-id-tag"><?= $order_id_formatted ?></span>
                                        </td>

                                        <!-- DATE -->
                                        <td class="text-muted small">
                                            <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                        </td>

                                        <!-- CUSTOMER -->
                                        <td>
                                            <div class="customer-name"><?= htmlspecialchars($order['name']) ?></div>
                                            <div class="customer-email"><?= htmlspecialchars($order['email']) ?></div>
                                            <div class="customer-city"><?= htmlspecialchars($order['city'] ?: 'Surat') ?></div>
                                        </td>

                                        <!-- ITEMS ORDERED -->
                                        <td>
                                            <?php if (!empty($items)): ?>
                                                <?php foreach ($items as $it): ?>
                                                    <div class="item-row">
                                                        <span class="item-qty-badge"><?= $it['quantity'] ?>x</span>
                                                        <span class="item-name"><?= htmlspecialchars($it['name'] ?: 'Luxury Item') ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="item-row">
                                                    <span class="item-qty-badge">1x</span>
                                                    <span class="item-name text-muted">ChronoScent Selection</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- PAYMENT -->
                                        <td>
                                            <span class="payment-pill"><?= htmlspecialchars($order['payment_method'] ?: 'Cash on Delivery') ?></span>
                                        </td>

                                        <!-- TOTAL AMOUNT -->
                                        <td>
                                            <span class="amount-text">₹<?= formatIndianNumber($order['total_amount']) ?></span>
                                        </td>

                                        <!-- ORDER STATUS (INTERACTIVE DROPDOWN) -->
                                        <td>
                                            <form method="POST" class="d-inline m-0 p-0">
                                                <input type="hidden" name="update_status" value="1">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <select name="status" class="status-pill-select <?= $status_class ?>" onchange="this.form.submit()">
                                                    <option value="Pending" <?= $current_status == 'Pending' ? 'selected' : '' ?>>Processing &or;</option>
                                                    <option value="Confirmed" <?= $current_status == 'Confirmed' ? 'selected' : '' ?>>Confirmed &or;</option>
                                                    <option value="Shipped" <?= $current_status == 'Shipped' ? 'selected' : '' ?>>Shipped &or;</option>
                                                    <option value="Delivered" <?= $current_status == 'Delivered' ? 'selected' : '' ?>>Delivered &or;</option>
                                                    <option value="Cancelled" <?= $current_status == 'Cancelled' ? 'selected' : '' ?>>Cancelled &or;</option>
                                                </select>
                                            </form>
                                        </td>

                                        <!-- ACTIONS -->
                                        <td class="text-end">
                                            <form method="POST" class="d-inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete order <?= $order_id_formatted ?>?');">
                                                <input type="hidden" name="delete_order" value="1">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <button type="submit" class="btn-action-delete" title="Delete Order">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                            No customer orders found matching your search criteria.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>
