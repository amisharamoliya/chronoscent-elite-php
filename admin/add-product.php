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
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)$_POST['stock'];
    
    // Default image
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $dir_name = ($category === 'Watch') ? 'watches' : 'perfumes';
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
        $stmt = $pdo->prepare("INSERT INTO products (name, brand, category, description, price, discount_price, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $brand, $category, $description, $price, $discount_price, $stock, $image_path])) {
            $success = "Product added successfully.";
        } else {
            $error = "Error adding product.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Add Product - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin-add-product.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar" style="width: 250px;">
            <h4 class="text-center mb-4 text-warning fw-bold">Admin Panel</h4>
            <a href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="products.php" class="active"><i class="bi bi-box me-2"></i> Products</a>
            <a href="orders.php"><i class="bi bi-cart me-2"></i> Orders</a>
            <a href="users.php"><i class="bi bi-people me-2"></i> Users</a>
            <a href="reviews.php"><i class="bi bi-star me-2"></i> Reviews</a>
            <a href="../index.php" target="_blank"><i class="bi bi-globe me-2"></i> View Site</a>
            <a href="logout.php" class="text-danger mt-5"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </div>
        
        <div class="main-content flex-grow-1 bg-light text-dark">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-dark">Add New Product</h2>
                <a href="products.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Products</a>
            </div>
            
            <div class="card border-0 shadow-sm" data-bs-theme="light">
                <div class="card-body">
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
                                <input type="text" name="brand" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category *</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="Watch">Watch</option>
                                    <option value="Perfume">Perfume</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock *</label>
                                <input type="number" name="stock" class="form-control" value="0" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Price ($) *</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Discount Price ($) (Optional)</label>
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
                        
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

