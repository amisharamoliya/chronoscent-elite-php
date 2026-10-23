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

$rev_stmt = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$rev_stmt->execute([$id]);
$reviews = $rev_stmt->fetchAll();

$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $wl_stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $wl_stmt->execute([$_SESSION['user_id'], $id]);
    $in_wishlist = $wl_stmt->fetch() ? true : false;
}
$heart_icon = $in_wishlist ? 'bi-heart-fill' : 'bi-heart';
$wishlist_action = $in_wishlist ? 'remove' : 'add';

// ==========================================
// DYNAMIC ATTRIBUTE EXTRACTION FOR ALL PRODUCTS
// ==========================================
$desc = $product['description'] ?? '';
$name = $product['name'] ?? '';
$cat = $product['category'] ?? '';
$brand = $product['brand'] ?? '';

$cat_type_stmt = $pdo->prepare("SELECT type FROM categories WHERE name = ?");
$cat_type_stmt->execute([$cat]);
$cat_type = $cat_type_stmt->fetchColumn() ?: '';
$is_perfume_cat = ($cat === 'Perfume' || $cat_type === 'Perfume');

// 1. Gender Detection
if (preg_match('/\b(Women|Woman|Ladies|Her)\b/i', $desc) || preg_match('/\b(Women|Woman|Ladies|Her)\b/i', $name)) {
    $gender = 'Women';
} elseif (preg_match('/\b(Unisex)\b/i', $desc) || preg_match('/\b(Unisex)\b/i', $name)) {
    $gender = 'Unisex';
} elseif (preg_match('/\b(Men|Man|Him|Masculine|Gentleman|Monsieur|Homme)\b/i', $desc) || preg_match('/\b(Men|Man|Him|Masculine|Gentleman|Monsieur|Homme)\b/i', $name)) {
    $gender = 'Men';
} else {
    $gender = 'Unisex';
}

// 2. Type Detection
if ($is_perfume_cat) {
    if (stripos($name, 'Pour Monsieur') !== false) {
        $type = 'Eau de Toilette';
    } elseif (stripos($name, 'Eau de Toilette') !== false || stripos($desc, 'Eau de Toilette') !== false) {
        $type = 'Eau de Toilette';
    } elseif (stripos($name, 'Eau de Parfum') !== false || stripos($name, 'Eau de Perfume') !== false || stripos($desc, 'Eau de Parfum') !== false) {
        $type = 'Eau de Parfum';
    } elseif (stripos($name, 'Parfum') !== false || stripos($desc, 'Parfum') !== false) {
        $type = 'Parfum Extrait';
    } else {
        $type = 'Eau de Parfum';
    }
} else {
    // Watches
    if (stripos($name, 'Chronograph') !== false || stripos($desc, 'chronograph') !== false) {
        $type = 'Chronograph Horology';
    } elseif (stripos($name, 'Seamaster') !== false || stripos($name, 'Diver') !== false || stripos($desc, 'diving') !== false) {
        $type = 'Professional Diver';
    } elseif (stripos($name, 'Day-Date') !== false || stripos($name, 'Datejust') !== false || stripos($name, 'Oyster') !== false) {
        $type = 'Luxury Automatic';
    } elseif (stripos($name, 'Tank') !== false || stripos($name, 'Baignoire') !== false || stripos($name, 'Rectangular') !== false) {
        $type = 'Haute Horlogerie Quartz';
    } elseif (stripos($name, 'Fusion') !== false || stripos($name, 'Big Bang') !== false) {
        $type = 'Automatic Skeleton';
    } else {
        $type = 'Swiss Precision Timepiece';
    }
}

// 3. Profile Detection
if ($is_perfume_cat) {
    if (stripos($name, 'Pour Monsieur') !== false) {
        $profile = 'Woody';
    } elseif (stripos($desc, 'woody') !== false || stripos($name, 'Wood') !== false || stripos($name, 'Bleu de Chanel') !== false) {
        $profile = 'Woody';
    } elseif (stripos($desc, 'floral-fruity') !== false || stripos($desc, 'floral') !== false || stripos($name, 'Bouquet') !== false || stripos($name, 'Crystal') !== false) {
        $profile = 'Floral';
    } elseif (stripos($desc, 'aquatic') !== false || stripos($desc, 'fresh') !== false || stripos($name, 'Water') !== false) {
        $profile = 'Fresh Aquatic';
    } elseif (stripos($desc, 'spicy') !== false || stripos($desc, 'oriental') !== false) {
        $profile = 'Spicy Oriental';
    } elseif (stripos($desc, 'citrus') !== false) {
        $profile = 'Citrus Fresh';
    } elseif (stripos($desc, 'sensual') !== false || stripos($desc, 'passionate') !== false) {
        $profile = 'Sensual Amber';
    } else {
        $profile = 'Woody Aromatic';
    }
} else {
    // Watches
    if (stripos($desc, 'Sports watch') !== false || stripos($name, 'Seamaster') !== false || stripos($name, 'Big Bang') !== false || stripos($name, 'Chronograph') !== false) {
        $profile = 'Sports';
    } elseif (stripos($desc, 'Classic watch') !== false || stripos($name, 'Classic') !== false || stripos($name, 'Tank') !== false || stripos($name, 'Ballon') !== false) {
        $profile = 'Classic';
    } else {
        $profile = 'Classic';
    }
}

// 4. Category Detection
if (!empty($cat) && !in_array($cat, ['Watch', 'Perfume'])) {
    $category_style = $cat;
} elseif (!$is_perfume_cat) {
    if (stripos($desc, 'Sports') !== false || stripos($name, 'Chronograph') !== false || stripos($name, 'Seamaster') !== false || stripos($name, 'Big Bang') !== false) {
        $category_style = 'Sports';
    } else {
        $category_style = 'Classic';
    }
} else {
    if (stripos($name, 'Pour Monsieur') !== false) {
        $category_style = 'Classic';
    } elseif (stripos($desc, 'Classic') !== false || in_array($brand, ['Chanel', 'Dior', 'Creed', 'Hermes', 'Guerlain'])) {
        $category_style = 'Classic';
    } elseif (stripos($desc, 'Modern') !== false || stripos($desc, 'Contemporary') !== false) {
        $category_style = 'Modern';
    } else {
        $category_style = 'Signature';
    }
}

// 5. Clean Description Display
$clean_desc = preg_replace('/\s*(Perfect for (Men|Women)\.|A perfect Unisex (watch|scent|fragrance|choice)\.|Classic watch\.|Sports watch\.)\s*/i', ' ', $desc);
$clean_desc = trim(preg_replace('/\s+/', ' ', $clean_desc));
$display_desc = !empty($clean_desc) ? $clean_desc : $desc;

// 6. Rating Detection (Computed strictly from real customer reviews)
$rev_count = count($reviews);
$product_rating = 0.0;
if ($rev_count > 0) {
    $total_stars = 0;
    foreach($reviews as $r) {
        $total_stars += (float)$r['rating'];
    }
    $product_rating = round($total_stars / $rev_count, 1);
}

require_once 'includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --luxury-heading-font: 'Playfair Display', Georgia, serif;
    --luxury-body-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --navy-btn-bg: #0d1e38;
    --navy-btn-hover: #19335a;
    --navy-border-color: #f1f5f9;
}

body {
    font-family: var(--luxury-body-font);
    background-color: #ffffff;
    color: #111827;
}

.watch-details-page {
    background-color: #ffffff;
    min-height: 80vh;
}

/* Breadcrumb / Back Link */
.detail-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s ease, transform 0.2s ease;
    margin-bottom: 2rem;
}
.detail-back-link:hover {
    color: #0d1e38;
    transform: translateX(-3px);
}

/* Image Card Showcase */
.product-showcase-box {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #edf2f7;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}
.product-showcase-img {
    width: 100%;
    max-height: 560px;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}
.product-showcase-box:hover .product-showcase-img {
    transform: scale(1.02);
}

/* Right Details Column */
.product-info-column {
    padding-left: 2rem;
}
@media (max-width: 991px) {
    .product-info-column {
        padding-left: 0;
        margin-top: 2.5rem;
    }
}

.product-brand-tag {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #9ca3af;
    margin-bottom: 0.5rem;
}

.product-title-luxury {
    font-family: var(--luxury-heading-font);
    font-size: 2.6rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.15;
    margin-bottom: 0.4rem;
    letter-spacing: -0.5px;
}
@media (max-width: 768px) {
    .product-title-luxury {
        font-size: 2.1rem;
    }
}

.product-type-subtitle {
    font-size: 0.98rem;
    color: #6b7280;
    font-weight: 400;
    margin-bottom: 0.85rem;
}

/* Rating */
.product-rating-row {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #0284c7;
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
}
.product-rating-row i {
    font-size: 0.92rem;
    color: #0284c7;
}
.product-rating-row .reviews-count-text {
    color: #9ca3af;
    font-size: 0.88rem;
    font-weight: 400;
    margin-left: 4px;
}

/* Price */
.product-price-row {
    display: flex;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.product-price-current {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.5px;
}
.product-price-original {
    font-size: 1.25rem;
    color: #9ca3af;
    text-decoration: line-through;
    font-weight: 400;
}
.product-discount-pill {
    background: #e63946;
    color: #ffffff;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    letter-spacing: 0.5px;
}

/* Description */
.product-desc-luxury {
    color: #4b5563;
    font-size: 0.96rem;
    line-height: 1.68;
    margin-bottom: 1.75rem;
}

/* Spec Key-Value Table */
.product-specs-list {
    border-top: 1px solid #f1f5f9;
    margin-bottom: 1.75rem;
}
.spec-row-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.95rem;
}
.spec-key {
    color: #374151;
    font-weight: 500;
}
.spec-val {
    color: #6b7280;
    font-weight: 400;
}
.spec-val.in-stock {
    color: #16a34a;
    font-weight: 500;
}
.spec-val.out-of-stock {
    color: #dc2626;
    font-weight: 500;
}

/* Action Buttons */
.product-actions-bar {
    display: flex;
    align-items: center;
    gap: 12px;
}
.product-actions-bar .btn-outline-theme {
    border: 2px solid var(--primary-theme);
    color: var(--primary-theme);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    transition: all 0.3s ease;
}
.product-actions-bar .btn-outline-theme:hover {
    background-color: var(--primary-theme);
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(114, 155, 168, 0.35);
}
.product-actions-bar .btn-outline-theme.active {
    color: #ef4444 !important;
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

/* Reviews and Related Products */
.section-subheading-luxury {
    font-family: var(--luxury-heading-font);
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
}
.review-card-luxury {
    background: #ffffff;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.review-card-luxury:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
}

.related-product-card {
    background: #ffffff;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.related-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    border-color: #cbd5e1;
}
.related-img-wrap {
    height: 220px;
    background-color: #ffffff;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.related-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.related-product-card:hover .related-img-wrap img {
    transform: scale(1.05);
}
</style>

<section class="watch-details-page py-5">
    <div class="container">
        <!-- Back Link -->
        <?php
        $backLink = strtolower($product['category']) == 'watch' ? 'watches.php' : 'perfumes.php';
        $backText = $product['category'] == 'Watch' ? 'Watches' : 'Perfumes';
        ?>
        <div class="mb-4">
            <a href="<?php echo $backLink; ?>" class="btn btn-outline-theme btn-sm rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to <?php echo $backText; ?>
            </a>
        </div>

        <!-- Main Product Section -->
        <div class="row align-items-start mb-5">
            <!-- Left: Product Image Showcase -->
            <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                <div class="product-showcase-box">
                    <img src="<?php echo htmlspecialchars($product['image']) . '?v=' . time(); ?>" class="product-showcase-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>

            <!-- Right: Product Information Matching Exact Photo Reference -->
            <div class="col-lg-7 col-md-6 product-info-column">
                <!-- Product Title -->
                <h1 class="product-title-luxury"><?php echo htmlspecialchars($product['name']); ?></h1>

                <!-- Type Subtitle -->
                <div class="product-type-subtitle">Type: <?php echo htmlspecialchars($type); ?></div>

                <!-- Star Rating (Shown ONLY when actual customer reviews exist) -->
                <?php if ($rev_count > 0): ?>
                    <div class="product-rating-row">
                        <i class="bi bi-star-fill"></i>
                        <span><?php echo number_format($product_rating, 1); ?></span>
                        <span class="reviews-count-text">(<?php echo $rev_count; ?> <?php echo $rev_count == 1 ? 'review' : 'reviews'; ?>)</span>
                    </div>
                <?php endif; ?>

                <!-- Price Row -->
                <div class="product-price-row">
                    <?php if (!empty($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price']): ?>
                        <span class="product-price-current">₹<?php echo formatIndianNumber($product['discount_price']); ?></span>
                        <span class="product-price-original">₹<?php echo formatIndianNumber($product['price']); ?></span>
                    <?php else: ?>
                        <span class="product-price-current">₹<?php echo formatIndianNumber($product['price']); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <p class="product-desc-luxury">
                    <?php echo nl2br(htmlspecialchars($display_desc)); ?>
                </p>

                <!-- Product Specifications Table (Gender, Profile, Category, Availability) -->
                <div class="product-specs-list">
                    <div class="spec-row-item">
                        <span class="spec-key">Gender</span>
                        <span class="spec-val"><?php echo htmlspecialchars($gender); ?></span>
                    </div>
                    <div class="spec-row-item">
                        <span class="spec-key">Profile</span>
                        <span class="spec-val"><?php echo htmlspecialchars($profile); ?></span>
                    </div>
                    <div class="spec-row-item">
                        <span class="spec-key">Category</span>
                        <span class="spec-val"><?php echo htmlspecialchars($category_style); ?></span>
                    </div>
                    <div class="spec-row-item">
                        <span class="spec-key">Availability</span>
                        <span class="spec-val <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                            <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                        </span>
                    </div>
                </div>

                <!-- Action Buttons: Add to Cart + Wishlist -->
                <div class="product-actions-bar">
                    <form action="cart.php" method="POST" class="flex-grow-1 m-0">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-outline-theme rounded-pill w-100 py-3 shadow" <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?> style="letter-spacing: 1.5px; font-weight: 600; font-size: 1rem;">
                            <i class="bi bi-cart3 me-2"></i> Add to Cart
                        </button>
                    </form>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="wishlist.php" method="POST" class="m-0">
                            <input type="hidden" name="action" value="<?php echo $wishlist_action; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-outline-theme rounded-circle shadow d-inline-flex align-items-center justify-content-center <?php echo $in_wishlist ? 'active' : ''; ?>" style="width: 54px; height: 54px; font-size: 1.3rem;" title="<?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>">
                                <i class="bi <?php echo $heart_icon; ?>"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php?redirect=product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-theme rounded-circle shadow d-inline-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 1.3rem;" title="Login to save to Wishlist">
                            <i class="bi bi-heart"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Customer Reviews Section -->
        <div class="mt-5 border-top pt-5">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <h3 class="section-subheading-luxury m-0">Customer Reviews</h3>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="review.php?product_id=<?php echo $id; ?>" class="btn btn-sm btn-outline-theme rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Write a Review
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=product-details.php?id=<?php echo $id; ?>" class="btn btn-sm btn-outline-theme rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login to Review
                    </a>
                <?php endif; ?>
            </div>

            <?php if(count($reviews) > 0): ?>
                <div class="row">
                    <?php foreach($reviews as $rev): ?>
                        <div class="col-md-6 mb-4">
                            <div class="review-card-luxury">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold m-0" style="color: #111827;"><?php echo htmlspecialchars($rev['title']); ?></h5>
                                    <div>
                                        <?php
                                        for($i=1; $i<=5; $i++) {
                                            if ($i <= $rev['rating']) {
                                                echo '<i class="bi bi-star-fill text-warning me-1"></i>';
                                            } else {
                                                echo '<i class="bi bi-star text-secondary opacity-50 me-1"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <h6 class="text-muted small mb-2">By <?php echo htmlspecialchars($rev['name']); ?> on <?php echo date('M d, Y', strtotime($rev['created_at'])); ?></h6>
                                <p class="mb-0 text-secondary" style="font-size: 0.94rem; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($rev['message'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded-3">
                    <i class="bi bi-chat-square-heart text-muted display-6 mb-2 d-block"></i>
                    <p class="text-muted mb-3">No reviews yet for this product. Be the first to share your experience!</p>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="review.php?product_id=<?php echo $id; ?>" class="btn btn-outline-theme btn-sm rounded-pill px-4 py-2 shadow-sm">
                            <i class="bi bi-star me-1"></i> Write the First Review
                        </a>
                    <?php else: ?>
                        <a href="login.php?redirect=product-details.php?id=<?php echo $id; ?>" class="btn btn-outline-theme btn-sm rounded-pill px-4 py-2 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login to Leave a Review
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- You May Also Like Section -->
        <div class="mt-5 border-top pt-5">
            <h3 class="section-subheading-luxury mb-4">You May Also Like</h3>
            <div class="row">
                <?php
                if ($is_perfume_cat) {
                    $rel_stmt = $pdo->prepare("SELECT * FROM products WHERE (category = ? OR category = 'Perfume' OR category IN (SELECT name FROM categories WHERE type = 'Perfume')) AND id != ? ORDER BY RAND() LIMIT 4");
                } else {
                    $rel_stmt = $pdo->prepare("SELECT * FROM products WHERE (category = ? OR category = 'Watch' OR category IN (SELECT name FROM categories WHERE type = 'Watch')) AND id != ? ORDER BY RAND() LIMIT 4");
                }
                $rel_stmt->execute([$product['category'], $id]);
                $related = $rel_stmt->fetchAll();

                if(count($related) > 0):
                    foreach($related as $rel):
                ?>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <div class="related-product-card">
                            <div class="related-img-wrap">
                                <a href="product-details.php?id=<?php echo $rel['id']; ?>" class="d-block w-100 h-100">
                                    <img src="<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>">
                                </a>
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <span class="text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 1px; font-weight: 600;"><?php echo htmlspecialchars($rel['brand']); ?></span>
                                <h6 class="fw-bold my-1 text-truncate">
                                    <a href="product-details.php?id=<?php echo $rel['id']; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($rel['name']); ?></a>
                                </h6>
                                <div class="mt-auto pt-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (!empty($rel['discount_price']) && $rel['discount_price'] > 0 && $rel['discount_price'] < $rel['price']): ?>
                                            <span class="text-danger fw-bold me-1">₹<?php echo formatIndianNumber($rel['discount_price']); ?></span>
                                            <span class="text-muted text-decoration-line-through small">₹<?php echo formatIndianNumber($rel['price']); ?></span>
                                        <?php else: ?>
                                            <span class="fw-bold text-dark">₹<?php echo formatIndianNumber($rel['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="product-details.php?id=<?php echo $rel['id']; ?>" class="btn btn-sm btn-outline-theme rounded-pill px-3 shadow-sm">View</a>
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
</section>

<?php require_once 'includes/footer.php'; ?>
