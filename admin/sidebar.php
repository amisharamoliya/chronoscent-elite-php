<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

$sidebar_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
$sidebar_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0;
$sidebar_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0;
$sidebar_brands = $pdo->query("SELECT COUNT(*) FROM brands")->fetchColumn() ?: 0;
$sidebar_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn() ?: 0;
$sidebar_reviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn() ?: 0;
$sidebar_messages = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn() ?: 0;

$admin_display_name = 'Admin';
if (!empty($_SESSION['admin_username'])) {
    $admin_display_name = $_SESSION['admin_username'];
} elseif (!empty($_SESSION['admin_id']) && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $fetched_admin = $stmt->fetchColumn();
        if ($fetched_admin) {
            $admin_display_name = $fetched_admin;
            $_SESSION['admin_username'] = $fetched_admin;
        }
    } catch (\Throwable $e) {
        // fallback to default
    }
}
$admin_display_initial = strtoupper(substr($admin_display_name, 0, 1)) ?: 'A';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="brand-logo-text text-decoration-none">
            <span class="brand-title">CHRONOSCENT</span>
            <span class="brand-subtitle">ELITE</span>
        </a>
    </div>

    <div class="sidebar-menu">
        <div class="menu-group-label">OPERATIONS</div>
        <a href="dashboard.php" class="menu-item <?= ($current_page == 'dashboard.php' || $current_page == 'orders.php') ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-box2"></i>
                <span>Orders</span>
            </span>
            <span class="badge-count"><?= $sidebar_orders ?></span>
        </a>
        <a href="users.php" class="menu-item <?= $current_page == 'users.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-people"></i>
                <span>Customers</span>
            </span>
            <span class="badge-count"><?= $sidebar_users ?></span>
        </a>
        <a href="messages.php" class="menu-item <?= $current_page == 'messages.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-envelope"></i>
                <span>Messages</span>
            </span>
            <span class="badge-count"><?= $sidebar_messages ?></span>
        </a>

        <div class="menu-group-label">CATALOG</div>
        <a href="products.php" class="menu-item <?= ($current_page == 'products.php' || $current_page == 'add-product.php' || $current_page == 'edit-product.php') ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-bag"></i>
                <span>Product</span>
            </span>
            <span class="badge-count"><?= $sidebar_products ?></span>
        </a>
        <a href="brands.php" class="menu-item <?= $current_page == 'brands.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-tag"></i>
                <span>Brand</span>
            </span>
            <span class="badge-count"><?= $sidebar_brands ?></span>
        </a>
        <a href="categories.php" class="menu-item <?= $current_page == 'categories.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-folder"></i>
                <span>Category</span>
            </span>
            <span class="badge-count"><?= $sidebar_categories ?></span>
        </a>
        <a href="reviews.php" class="menu-item <?= $current_page == 'reviews.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-star"></i>
                <span>Review</span>
            </span>
            <span class="badge-count"><?= $sidebar_reviews ?></span>
        </a>

        <div class="menu-group-label">PREFERENCES</div>
        <a href="settings.php" class="menu-item <?= $current_page == 'settings.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-gear"></i>
                <span>Setting</span>
            </span>
        </a>
        <a href="security.php" class="menu-item <?= $current_page == 'security.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-shield-check"></i>
                <span>Security</span>
            </span>
        </a>
        <a href="help.php" class="menu-item <?= $current_page == 'help.php' ? 'active' : '' ?>">
            <span class="menu-item-left">
                <i class="bi bi-question-circle"></i>
                <span>Help</span>
            </span>
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-avatar-circle"><?= $admin_display_initial ?></div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars(ucfirst($admin_display_name)) ?></div>
        </div>
        <a href="logout.php" class="logout-btn" title="Logout">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</aside>
