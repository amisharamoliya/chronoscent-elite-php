<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'includes/auth.php';
require_login();

$user_id = $_SESSION['user_id'];

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $product_id = (int)$_POST['product_id'];
    
    if ($_POST['action'] == 'add') {
        $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$user_id, $product_id]);
        }
    } elseif ($_POST['action'] == 'remove') {
        $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$user_id, $product_id]);
    }
    
    if (isset($_POST['is_ajax'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $wishlist_count = $stmt->fetchColumn();
        echo json_encode(['status' => 'success', 'wishlist_count' => $wishlist_count]);
        exit;
    }

    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'wishlist.php';
    header("Location: " . $redirect_url);
    exit;
}

// Fetch Wishlist Items
$stmt = $pdo->prepare("SELECT p.* FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
$stmt->execute([$user_id]);
$wishlist = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5 min-vh-100">
    <h1 class="fw-bold mb-4" style="color: var(--primary-theme);">My Wishlist</h1>
    
    <?php if (empty($wishlist)): ?>
        <div class="text-center py-5">
            <i class="bi bi-heart display-1 text-muted mb-3 d-block"></i>
            <h4>Your wishlist is empty.</h4>
            <a href="index.php" class="btn btn-theme mt-3">Explore Products</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($wishlist as $p): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" class="card-img-top product-img" alt="">
                        <div class="card-body d-flex flex-column text-center">
                            <h6 class="card-title fw-bold mb-1 text-white"><?php echo htmlspecialchars($p['name']); ?></h6>
                            <p class="text-white small mb-2"><?php echo htmlspecialchars($p['brand']); ?></p>
                            <h6 class="text-theme mb-3">$<?php echo number_format($p['discount_price'] ? $p['discount_price'] : $p['price'], 2); ?></h6>
                            
                            <div class="mt-auto d-flex gap-2 justify-content-center">
                                <form action="cart.php" method="POST" class="flex-grow-1">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-theme w-100">Add to Cart</button>
                                </form>
                                <form action="wishlist.php" method="POST">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
