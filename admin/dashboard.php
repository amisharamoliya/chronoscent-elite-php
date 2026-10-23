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
    header("Location: dashboard.php" . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Handle Order Deletion
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['delete_order']) && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$order_id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);
    header("Location: dashboard.php" . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Fetch Live Statistics
$total_products = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$watch_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE category = 'Watch' OR category IN (SELECT name FROM categories WHERE type = 'Watch') OR category = 'Classic' OR category = 'Sports'")->fetchColumn();
$scent_count = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE category = 'Perfume' OR category IN (SELECT name FROM categories WHERE type = 'Perfume') OR category = 'Modern' OR category = 'Signature'")->fetchColumn();

$total_orders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_users = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_revenue = (float)$pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?: 0.0;

$total_records = $total_products + $total_orders + $total_users;
$pct_products = $total_records > 0 ? ($total_products / $total_records) * 100 : 0;
$pct_orders = $total_records > 0 ? ($total_orders / $total_records) * 100 : 0;
$pct_users = $total_records > 0 ? ($total_users / $total_records) * 100 : 0;

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
    <title>ChronoScent Elite - Admin Dashboard</title>
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

                <!-- 1. Top Stat Cards Row -->
                <div class="row g-4 mb-4">
                    <!-- Total Products -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">TOTAL PRODUCTS</div>
                                <div class="stat-value"><?= $total_products ?></div>
                                <div class="stat-subtitle"><?= $watch_count ?> Watches &bull; <?= $scent_count ?> Scents</div>
                            </div>
                            <div class="stat-icon-wrapper icon-blue">
                                <i class="bi bi-bag"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Orders -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">TOTAL ORDERS</div>
                                <div class="stat-value"><?= $total_orders ?></div>
                                <div class="stat-subtitle text-success fw-semibold"><i class="bi bi-circle-fill small" style="font-size: 7px; vertical-align: middle;"></i> Live from database</div>
                            </div>
                            <div class="stat-icon-wrapper icon-orange">
                                <i class="bi bi-box-seam"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">TOTAL USERS</div>
                                <div class="stat-value"><?= $total_users ?></div>
                                <div class="stat-subtitle">0 Blocked &bull; <?= $total_users ?> Active</div>
                            </div>
                            <div class="stat-icon-wrapper icon-cyan">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue -->
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">REVENUE</div>
                                <div class="stat-value">₹<?= formatIndianNumber($total_revenue) ?></div>
                                <div class="stat-subtitle">Gross Sales Volume</div>
                            </div>
                            <div class="stat-icon-wrapper icon-green">
                                <i class="bi bi-currency-rupee"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Store Activity & Proportions Section -->
                <div class="proportions-card mb-4">
                    <div class="proportions-header">
                        <div>
                            <h5 class="proportions-title">Store Activity &amp; Proportions</h5>
                            <p class="proportions-subtitle">Breakdown of Products, Customer Orders, and Users</p>
                        </div>
                        <div class="proportions-legend">
                            <div><span class="legend-dot" style="background: #2563eb;"></span>Products (<?= $total_products ?>)</div>
                            <div><span class="legend-dot" style="background: #f97316;"></span>Orders (<?= $total_orders ?>)</div>
                            <div><span class="legend-dot" style="background: #06b6d4;"></span>Users (<?= $total_users ?>)</div>
                        </div>
                    </div>

                    <div class="row align-items-center g-4">
                        <!-- Semi-circle Gauge -->
                        <div class="col-lg-5 text-center">
                            <div class="d-inline-block position-relative" style="width: 260px; height: 145px;">
                                <svg viewBox="0 0 200 115" width="260" height="145" style="overflow: visible;">
                                    <!-- Background semi-circle track -->
                                    <path d="M 25,100 A 75,75 0 0,1 175,100" fill="none" stroke="#f1f5f9" stroke-width="22" stroke-linecap="round"/>
                                    
                                    <?php
                                    // Total semi-circle arc length ~ 235.6
                                    $arc_length = 235.6;
                                    $len_prod = round(($pct_products / 100) * $arc_length);
                                    $len_ord = round(($pct_orders / 100) * $arc_length);
                                    $len_usr = max(2, $arc_length - $len_prod - $len_ord);
                                    ?>
                                    <!-- Blue Segment (Products) -->
                                    <path d="M 25,100 A 75,75 0 0,1 175,100" fill="none" stroke="#2563eb" stroke-width="20"
                                          stroke-dasharray="<?= $len_prod ?> <?= $arc_length ?>" stroke-dashoffset="0" stroke-linecap="round"/>
                                    <!-- Orange Segment (Orders) -->
                                    <path d="M 25,100 A 75,75 0 0,1 175,100" fill="none" stroke="#f97316" stroke-width="20"
                                          stroke-dasharray="<?= $len_ord ?> <?= $arc_length ?>" stroke-dashoffset="-<?= $len_prod ?>" stroke-linecap="round"/>
                                    <!-- Cyan Segment (Users) -->
                                    <path d="M 25,100 A 75,75 0 0,1 175,100" fill="none" stroke="#06b6d4" stroke-width="20"
                                          stroke-dasharray="<?= $len_usr ?> <?= $arc_length ?>" stroke-dashoffset="-<?= ($len_prod + $len_ord) ?>" stroke-linecap="round"/>
                                </svg>
                                <div class="position-absolute start-50 translate-middle-x" style="bottom: 8px;">
                                    <div class="fw-bold fs-2 text-dark lh-1"><?= $total_records ?></div>
                                    <div class="text-muted fw-semibold small text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px;">TOTAL RECORDS</div>
                                </div>
                            </div>
                        </div>

                        <!-- 3 Right Metrics -->
                        <div class="col-lg-7">
                            <div class="metric-row">
                                <div>
                                    <div class="metric-label">CATALOG ASSETS</div>
                                    <div class="metric-value"><?= $watch_count ?> Luxury Watches + <?= $scent_count ?> Rare Scents</div>
                                </div>
                                <span class="badge-pill-custom badge-active">Active</span>
                            </div>

                            <div class="metric-row">
                                <div>
                                    <div class="metric-label">SALES PERFORMANCE</div>
                                    <div class="metric-value"><?= $total_orders ?> Fulfilled / In Process</div>
                                </div>
                                <span class="badge-pill-custom badge-healthy">Healthy</span>
                            </div>

                            <div class="metric-row">
                                <div>
                                    <div class="metric-label">CUSTOMER BASE</div>
                                    <div class="metric-value"><?= $total_users ?> Registered Accounts in Database</div>
                                </div>
                                <span class="badge-pill-custom badge-verified">Verified</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Customer Purchases (Orders Section) -->
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
