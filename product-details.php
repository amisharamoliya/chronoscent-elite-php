<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    require_once 'includes/header.php';
    echo "<div class='container py-5 text-center'><h1>Product not found.</h1></div>";
    require_once 'includes/footer.php';
    exit;
}

// Fetch Reviews
$rev_stmt = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$rev_stmt->execute([$id]);
$reviews = $rev_stmt->fetchAll();

// Check if in wishlist
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $wl_stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $wl_stmt->execute([$_SESSION['user_id'], $id]);
    $in_wishlist = $wl_stmt->fetch() ? true : false;
}
$heart_icon = $in_wishlist ? 'bi-heart-fill' : 'bi-heart';
$wishlist_action = $in_wishlist ? 'remove' : 'add';
$btn_class = $in_wishlist ? 'btn-danger' : 'btn-outline-danger';

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="mb-4">
        <?php 
        $backLink = strtolower($product['category']) == 'watch' ? 'watches.php' : 'perfumes.php';
        $backText = $product['category'] == 'Watch' ? 'Watches' : 'Perfumes';
        ?>
        <a href="<?php echo $backLink; ?>" class="btn btn-outline-theme btn-sm rounded-pill px-3 py-2" style="font-size: 0.9rem; letter-spacing: 0.5px;">
            <i class="bi bi-arrow-left me-1"></i> Back to <?php echo $backText; ?>
        </a>
    </div>



    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <img src="<?php echo htmlspecialchars($product['image']) . '?v=' . time(); ?>" class="img-fluid rounded shadow-sm w-100" style="max-height: 600px; object-fit: cover;" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-6">
            <p class="text-uppercase tracking-wide text-muted mb-1" style="letter-spacing: 2px; font-size: 0.9rem;"><?php echo htmlspecialchars($product['brand']); ?></p>
            <h1 class="display-5 fw-bold mb-3" style="color: #2c3e50; font-family: 'Playfair Display', serif;"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="mb-3 d-flex align-items-center">
                <div class="me-2">
                    <?php 
                    for($i=1; $i<=5; $i++) {
                        echo $i <= round($product['rating']) ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-warning"></i>';
                    }
                    ?>
                </div>
                <span class="text-muted">(<?php echo count($reviews); ?> Reviews)</span>
            </div>

            <div class="mb-4">
                <?php if ($product['discount_price']): ?>
                    <h2 class="text-danger mb-0">$<?php echo number_format($product['discount_price'], 2); ?></h2>
                    <p class="text-muted text-decoration-line-through fs-5">$<?php echo number_format($product['price'], 2); ?></p>
                <?php else: ?>
                    <h2 class="text-theme mb-0">$<?php echo number_format($product['price'], 2); ?></h2>
                <?php endif; ?>
            </div>

            <p class="lead text-muted mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p class="mb-4"><strong>Availability:</strong> <?php echo $product['stock'] > 0 ? "<span class='text-success'>In Stock ({$product['stock']})</span>" : "<span class='text-danger'>Out of Stock</span>"; ?></p>

            <div class="d-flex gap-3 mb-4">
                <form action="cart.php" method="POST" class="d-flex gap-3 flex-grow-1">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width: 80px;" <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                    <button type="submit" class="btn btn-theme btn-hover-transparent flex-grow-1" <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>Add to Cart</button>
                </form>
                <form action="wishlist.php" method="POST">
                    <input type="hidden" name="action" value="<?php echo $wishlist_action; ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="btn <?php echo $btn_class; ?>"><i class="bi <?php echo $heart_icon; ?>"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-5 border-top pt-5">
        <h3 class="fw-bold mb-4" style="color: var(--primary-theme);">Customer Reviews</h3>
        <?php if(count($reviews) > 0): ?>
            <div class="row">
                <?php foreach($reviews as $rev): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <h5 class="card-title fw-bold m-0"><?php echo htmlspecialchars($rev['title']); ?></h5>
                                    <div>
                                        <?php 
                                        for($i=1; $i<=5; $i++) echo $i <= $rev['rating'] ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-warning"></i>';
                                        ?>
                                    </div>
                                </div>
                                <h6 class="card-subtitle mb-2 text-muted">By <?php echo htmlspecialchars($rev['name']); ?> on <?php echo date('M d, Y', strtotime($rev['created_at'])); ?></h6>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($rev['message'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted">No reviews yet for this product.</p>
        <?php endif; ?>
    </div>
    </div>

    <!-- Related Products -->
    <div class="mt-5 border-top pt-5">
        <h3 class="fw-bold mb-4" style="color: var(--primary-theme);">You May Also Like</h3>
        <div class="row">
            <?php
            $rel_stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
            $rel_stmt->execute([$product['category'], $id]);
            $related = $rel_stmt->fetchAll();
            
            if(count($related) > 0):
                foreach($related as $rel):
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="<?php echo htmlspecialchars($rel['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($rel['name']); ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold fs-6 mb-1"><?php echo htmlspecialchars($rel['name']); ?></h5>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($rel['brand']); ?></p>
                            <div class="mt-auto">
                                <span class="fw-bold">$<?php echo number_format($rel['discount_price'] > 0 ? $rel['discount_price'] : $rel['price'], 2); ?></span>
                                <a href="product-details.php?id=<?php echo $rel['id']; ?>" class="btn btn-sm btn-outline-theme float-end">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
            else:
                echo "<p class='text-muted'>No related products found.</p>";
            endif;
            ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
