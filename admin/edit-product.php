<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/auth.php';
require_admin();

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $discount_price = empty($_POST['discount_price']) ? NULL : (float)$_POST['discount_price'];
    $stock = (int)$_POST['stock'];
    $image_url = trim($_POST['image_url']);

    if (empty($name) || empty($brand) || empty($category) || empty($description) || $price <= 0) {
        $error = "Please fill in all required fields properly.";
    } else {
        $update_stmt = $pdo->prepare("UPDATE products SET name=?, brand=?, category=?, description=?, price=?, discount_price=?, stock=?, image=? WHERE id=?");
        if ($update_stmt->execute([$name, $brand, $category, $description, $price, $discount_price, $stock, $image_url, $id])) {
            $success = "Product updated successfully!";
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
        } else {
            $error = "Error updating product.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/admin-edit-product.css?v=<?php echo time(); ?>">
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
                    <div class="purchases-header">
                        <div>
                            <h5 class="purchases-title">Edit Product: <?= htmlspecialchars($product['name']); ?></h5>
                            <p class="purchases-count">Update product catalog details</p>
                        </div>
                        <a href="products.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Products</a>
                    </div>

                    <div class="p-3">
                        <?php if($error): ?><div class="alert alert-danger"><?= $error; ?></div><?php endif; ?>
                        <?php if($success): ?><div class="alert alert-success"><?= $success; ?></div><?php endif; ?>

                        <form method="POST">
                            <div class="mb-3"><label class="form-label fw-semibold">Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required></div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Brand</label>
                                <?php
                                $registered_brands = $pdo->query("SELECT name FROM brands ORDER BY name ASC")->fetchAll(PDO::FETCH_COLUMN);
                                ?>
                                <input type="text" name="brand" list="brandList" class="form-control" value="<?= htmlspecialchars($product['brand']); ?>" required>
                                <datalist id="brandList">
                                    <?php foreach ($registered_brands as $rb): ?>
                                        <option value="<?= htmlspecialchars($rb) ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="mb-3"><label class="form-label fw-semibold">Category</label>
                                <?php
                                $registered_categories = $pdo->query("SELECT name, type FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
                                if (empty($registered_categories)) {
                                    $registered_categories = [
                                        ['name' => 'Classic', 'type' => 'Watch'],
                                        ['name' => 'Sports', 'type' => 'Watch'],
                                        ['name' => 'Watch', 'type' => 'Watch'],
                                        ['name' => 'Perfume', 'type' => 'Perfume']
                                    ];
                                }
                                ?>
                                <select name="category" class="form-select">
                                    <?php foreach ($registered_categories as $rc): ?>
                                        <option value="<?= htmlspecialchars($rc['name']) ?>" <?php if($product['category'] == $rc['name']) echo 'selected'; ?>><?= htmlspecialchars($rc['name']) ?> (<?= htmlspecialchars($rc['type']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label fw-semibold">Description</label><textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($product['description']); ?></textarea></div>
                            <div class="row mb-3">
                                <div class="col"><label class="form-label fw-semibold">Price (₹)</label><input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price']; ?>" required></div>
                                <div class="col"><label class="form-label fw-semibold">Discount Price (₹)</label><input type="number" step="0.01" name="discount_price" class="form-control" value="<?= $product['discount_price']; ?>"></div>
                            </div>
                            <div class="mb-3"><label class="form-label fw-semibold">Stock</label><input type="number" name="stock" class="form-control" value="<?= $product['stock']; ?>" required></div>
                            <div class="mb-4"><label class="form-label fw-semibold">Image URL</label><input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($product['image']); ?>"></div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" style="background-color: #1e3a5f; border-color: #1e3a5f;"><i class="bi bi-save me-1"></i> Update Product</button>
                                <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
