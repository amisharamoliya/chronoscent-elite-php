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

if (!$product) die("Product not found");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $category = $_POST['category'];
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $discount_price = empty($_POST['discount_price']) ? NULL : (float)$_POST['discount_price'];
    $stock = (int)$_POST['stock'];
    
    $image_url = $product['image'];
    if (!empty($_POST['image_url'])) {
        $image_url = $_POST['image_url'];
    }

    $update_stmt = $pdo->prepare("UPDATE products SET name=?, brand=?, category=?, description=?, price=?, discount_price=?, stock=?, image=? WHERE id=?");
    if ($update_stmt->execute([$name, $brand, $category, $description, $price, $discount_price, $stock, $image_url, $id])) {
        header("Location: products.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-edit-product.css">
</head>
<body>
    <div class="container shadow-sm">
        <h2 class="mb-4">Edit Product</h2>
        <form method="POST">
            <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required></div>
            <div class="mb-3"><label>Brand</label><input type="text" name="brand" class="form-control" value="<?php echo htmlspecialchars($product['brand']); ?>" required></div>
            <div class="mb-3"><label>Category</label>
                <select name="category" class="form-select">
                    <option value="Watch" <?php if($product['category']=='Watch') echo 'selected'; ?>>Watch</option>
                    <option value="Perfume" <?php if($product['category']=='Perfume') echo 'selected'; ?>>Perfume</option>
                </select>
            </div>
            <div class="mb-3"><label>Description</label><textarea name="description" class="form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea></div>
            <div class="row mb-3">
                <div class="col"><label>Price</label><input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required></div>
                <div class="col"><label>Discount Price</label><input type="number" step="0.01" name="discount_price" class="form-control" value="<?php echo $product['discount_price']; ?>"></div>
            </div>
            <div class="mb-3"><label>Stock</label><input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required></div>
            <div class="mb-3"><label>Image URL</label><input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($product['image']); ?>"></div>
            <button type="submit" class="btn btn-primary w-100">Update Product</button>
            <a href="products.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>
