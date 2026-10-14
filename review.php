<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'includes/auth.php';
require_login();

if (!isset($_GET['product_id'])) {
    header("Location: index.php");
    exit;
}

$product_id = (int)$_GET['product_id'];
$stmt = $pdo->prepare("SELECT name, image FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}

$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = (int)$_POST['rating'];
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, title, message) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$_SESSION['user_id'], $product_id, $rating, $title, $message])) {

        $avg_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ?");
        $avg_stmt->execute([$product_id]);
        $avg = $avg_stmt->fetchColumn();

        $update_stmt = $pdo->prepare("UPDATE products SET rating = ? WHERE id = ?");
        $update_stmt->execute([$avg, $product_id]);

        $success = 'Thank you! Your review has been submitted.';
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5 min-vh-100">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <h2 class="fw-bold mb-3" style="color: var(--primary-theme);">Thank you for shopping with ChronoScent Elite!</h2>
                    <p class="text-white mb-4">Please share your experience about the <strong><?php echo htmlspecialchars($product['name']); ?></strong>.</p>

                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 30px;">

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <a href="product-details.php?id=<?php echo $product_id; ?>" class="btn btn-theme btn-hover-transparent w-100">View Product</a>
                    <?php else: ?>
                        <form method="POST" class="text-start">
                            <div class="mb-3 text-center">
                                <label class="form-label fw-bold d-block">Rating</label>
                                <style>
.rating-stars input[type="radio"] { display: none; }
                                    .rating-stars label { font-size: 2.5rem; color: #ddd; cursor: pointer; transition: color 0.2s; margin: 0 5px; }
                                    .rating-stars input[type="radio"]:checked ~ label,
                                    .rating-stars label:hover,
                                    .rating-stars label:hover ~ label { color: #ffc107; }
</style>
                                <div class="rating-stars d-flex flex-row-reverse justify-content-center">
                                    <input type="radio" id="star5" name="rating" value="5" required checked />
                                    <label for="star5" title="5 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4" />
                                    <label for="star4" title="4 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3" />
                                    <label for="star3" title="3 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2" />
                                    <label for="star2" title="2 stars"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1" />
                                    <label for="star1" title="1 star"><i class="bi bi-star-fill"></i></label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Review Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Excellent product!" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Review Message</label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Share details of your own experience at this place" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-theme btn-hover-transparent w-100 py-3 fw-bold">Submit Review</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
