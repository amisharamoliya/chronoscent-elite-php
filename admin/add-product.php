<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/auth.php';
require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $category = $_POST['category'];
    $profile = !empty($_POST['profile']) ? trim($_POST['profile']) : null;
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)$_POST['stock'];

    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $type_check = $pdo->prepare("SELECT type FROM categories WHERE name = ?");
        $type_check->execute([$category]);
        $cat_type = $type_check->fetchColumn();
        $dir_name = ($cat_type === 'Perfume' || $category === 'Perfume') ? 'perfumes' : 'watches';
        $upload_dir = '../assets/images/' . $dir_name . '/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'assets/images/' . $dir_name . '/' . $file_name;
        }
    }

    if(empty($name) || empty($brand) || empty($category) || empty($price)) {
        $error = "Please fill all required fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (name, brand, category, profile, description, price, discount_price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $brand, $category, $profile, $description, $price, $discount_price, $stock, $image_path])) {
            $success = "Product added successfully.";
        } else {
            $error = "Error adding product.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - ChronoScent Elite Admin</title>
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

            <main class="admin-content">
                <div class="purchases-card">
                    <div class="purchases-header">
                        <div>
                            <h5 class="purchases-title">Add New Product</h5>
                            <p class="purchases-count">Create and add new product to catalog</p>
                        </div>
                        <a href="products.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Products</a>
                    </div>

                    <div class="p-3">
                    <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                    <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand *</label>
                                <?php
                                $registered_brands = $pdo->query("SELECT name FROM brands ORDER BY name ASC")->fetchAll(PDO::FETCH_COLUMN);
                                ?>
                                <input type="text" name="brand" list="brandList" class="form-control" placeholder="Select or type brand..." required>
                                <datalist id="brandList">
                                    <?php foreach ($registered_brands as $rb): ?>
                                        <option value="<?= htmlspecialchars($rb) ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Category *</label>
                                <?php
                                $registered_categories = $pdo->query("SELECT name, type FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
                                if (empty($registered_categories)) {
                                    $registered_categories = [
                                        ['name' => 'Classic', 'type' => 'Watch'],
                                        ['name' => 'Sports', 'type' => 'Watch'],
                                        ['name' => 'Diver', 'type' => 'Watch'],
                                        ['name' => 'Dress', 'type' => 'Watch'],
                                        ['name' => 'Watch', 'type' => 'Watch'],
                                        ['name' => 'Perfume', 'type' => 'Perfume']
                                    ];
                                }
                                ?>
                                <select name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($registered_categories as $rc): ?>
                                        <option value="<?= htmlspecialchars($rc['name']) ?>"><?= htmlspecialchars($rc['name']) ?> (<?= htmlspecialchars($rc['type']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fragrance Profile (Perfumes)</label>
                                <select name="profile" class="form-select">
                                    <option value="">None / Not Applicable</option>
                                    <option value="Floral">Floral</option>
                                    <option value="Woody">Woody</option>
                                    <option value="Fresh">Fresh</option>
                                    <option value="Oriental">Oriental</option>
                                    <option value="Fruity">Fruity</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Stock *</label>
                                <input type="number" name="stock" class="form-control" value="0" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Discount Price (₹) (Optional)</label>
                                <input type="number" step="0.01" name="discount_price" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-primary" style="background-color: #1e3a5f; border-color: #1e3a5f;"><i class="bi bi-save me-1"></i> Save Product</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
