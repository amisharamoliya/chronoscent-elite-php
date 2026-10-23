<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$category_filter = trim($_GET['category'] ?? '');
$search_query = trim($_GET['search'] ?? ($_GET['q'] ?? ''));

$where = [];
$params = [];

if ($category_filter !== '') {
    if ($category_filter === 'Classic') {
        $where[] = "(category = 'Classic' OR description LIKE '%Classic watch.%' OR name LIKE '%Classic%')";
    } elseif ($category_filter === 'Sports') {
        $where[] = "(category = 'Sports' OR description LIKE '%Sports watch.%' OR name LIKE '%Seamaster%' OR name LIKE '%Big Bang%' OR name LIKE '%Chronograph%')";
    } elseif ($category_filter === 'Modern') {
        $where[] = "(category = 'Modern' OR description LIKE '%Modern%' OR description LIKE '%Contemporary%')";
    } elseif ($category_filter === 'Signature') {
        $where[] = "(category = 'Signature' OR (category = 'Perfume' AND name LIKE '%Aventus%'))";
    } elseif ($category_filter === 'Watch') {
        $where[] = "(category = 'Watch' OR category IN (SELECT name FROM categories WHERE type = 'Watch'))";
    } elseif ($category_filter === 'Perfume') {
        $where[] = "(category = 'Perfume' OR category IN (SELECT name FROM categories WHERE type = 'Perfume'))";
    } else {
        $where[] = "category = ?";
        $params[] = $category_filter;
    }
}

if ($search_query !== '') {
    $where[] = "(name LIKE ? OR brand LIKE ? OR category LIKE ? OR description LIKE ?)";
    $like = "%{$search_query}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
$stmt = $pdo->prepare("SELECT * FROM products {$where_sql} ORDER BY id DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();

// All categories for the filter dropdown
$all_categories = $pdo->query("SELECT name, type FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$total_all_products = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?php echo time(); ?>">
    <style>
        .category-pill-watch {
            background-color: #eff6ff;
            color: #1e3a5f;
            border: 1px solid #bfdbfe;
        }
        .category-pill-perfume {
            background-color: #ecfeff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .category-pill-classic {
            background-color: #fefce8;
            color: #854d0e;
            border: 1px solid #fef08a;
        }
        .category-pill-sports {
            background-color: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Wrapper -->
        <div class="admin-main-wrapper">
            <!-- Topbar -->
            <?php include 'topbar.php'; ?>

            <main class="admin-content">
                <div class="purchases-card">
                    <div class="purchases-header flex-wrap gap-2">
                        <div>
                            <h5 class="purchases-title">Product Catalog</h5>
                            <p class="purchases-count">
                                Showing <?= count($products) ?> of <?= $total_all_products ?> products
                                <?php if ($category_filter): ?>
                                    &bull; Filtered by Category: <span class="badge bg-primary"><?= htmlspecialchars($category_filter) ?></span>
                                <?php endif; ?>
                                <?php if ($search_query): ?>
                                    &bull; Search: <span class="badge bg-secondary"><?= htmlspecialchars($search_query) ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="categories.php" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-folder me-1"></i> Manage Categories
                            </a>
                            <a href="add-product.php" class="btn btn-sm btn-primary" style="background-color: #1e3a5f; border-color: #1e3a5f;">
                                <i class="bi bi-plus-lg me-1"></i> Add New Product
                            </a>
                        </div>
                    </div>

                    <!-- Search and Filter Bar -->
                    <div class="p-3 border-bottom bg-light">
                        <form method="GET" class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Search by name, brand, description..." value="<?= htmlspecialchars($search_query) ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All Categories (<?= $total_all_products ?>)</option>
                                    <?php foreach ($all_categories as $ac): 
                                        $selected = ($category_filter === $ac['name']) ? 'selected' : '';
                                    ?>
                                        <option value="<?= htmlspecialchars($ac['name']) ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($ac['name']) ?> (<?= htmlspecialchars($ac['type']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-dark px-3">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                                <?php if ($category_filter !== '' || $search_query !== ''): ?>
                                    <a href="products.php" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle me-1"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="orders-table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>IMAGE</th>
                                    <th>NAME</th>
                                    <th>CATEGORY</th>
                                    <th>PRICE</th>
                                    <th>STOCK</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($products) > 0): ?>
                                    <?php foreach ($products as $p): 
                                        $cat_name = $p['category'];
                                        $pill_class = 'payment-pill';
                                        if (stripos($cat_name, 'watch') !== false) {
                                            $pill_class = 'payment-pill category-pill-watch';
                                        } elseif (stripos($cat_name, 'perfume') !== false) {
                                            $pill_class = 'payment-pill category-pill-perfume';
                                        } elseif (stripos($cat_name, 'classic') !== false) {
                                            $pill_class = 'payment-pill category-pill-classic';
                                        } elseif (stripos($cat_name, 'sport') !== false) {
                                            $pill_class = 'payment-pill category-pill-sports';
                                        }
                                    ?>
                                    <tr>
                                        <td><span class="order-id-tag">#<?= $p['id']; ?></span></td>
                                        <td>
                                            <img src="../<?= htmlspecialchars($p['image']); ?>" width="48" height="48" style="object-fit:cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($p['name']); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($p['brand']); ?></small>
                                        </td>
                                        <td>
                                            <a href="products.php?category=<?= urlencode($p['category']); ?>" class="text-decoration-none">
                                                <span class="<?= $pill_class ?>"><?= htmlspecialchars($p['category']); ?></span>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="amount-text">₹<?= formatIndianNumber($p['price']); ?></span>
                                            <?php if (!empty($p['discount_price']) && $p['discount_price'] > 0): ?>
                                                <div class="small text-danger fw-bold">₹<?= formatIndianNumber($p['discount_price']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($p['stock'] > 0): ?>
                                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill"><?= $p['stock']; ?> in stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill">Out of Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="edit-product.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-outline-secondary me-1" title="Edit Product"><i class="bi bi-pencil"></i></a>
                                            <a href="delete-product.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-action-delete" onclick="return confirm('Are you sure you want to delete this product?');" title="Delete Product"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No products found matching your filter criteria.
                                            <br>
                                            <a href="products.php" class="btn btn-sm btn-outline-primary mt-2">Clear Filter</a>
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
