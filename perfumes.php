<?php
require_once 'includes/header.php';

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';
$gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';

$order_by = 'created_at DESC';
if ($sort == 'price_asc') $order_by = 'price ASC';
if ($sort == 'price_desc') $order_by = 'price DESC';
if ($sort == 'rating') $order_by = 'id DESC';

$where_clauses = ["(category = 'Perfume' OR category IN (SELECT name FROM categories WHERE type = 'Perfume') OR category = 'Modern' OR category = 'Signature')"];
$params = [];

if ($brand_filter) {
    if ($brand_filter === 'YSL' || $brand_filter === 'Yves Saint Laurent') {
        $where_clauses[] = "(brand = 'YSL' OR brand = 'Yves Saint Laurent')";
    } else {
        $where_clauses[] = "brand = ?";
        $params[] = $brand_filter;
    }
}

if ($gender_filter == 'Men') {
    $where_clauses[] = "(name LIKE '%Men%' OR description LIKE '%Men%') AND name NOT LIKE '%Women%' AND description NOT LIKE '%Women%'";
} elseif ($gender_filter == 'Women') {
    $where_clauses[] = "(name LIKE '%Women%' OR description LIKE '%Women%' OR name LIKE '%Ladies%')";
} elseif ($gender_filter == 'Unisex') {
    $where_clauses[] = "(name LIKE '%Unisex%' OR description LIKE '%Unisex%')";
}

$where_sql = implode(' AND ', $where_clauses);
$stmt = $pdo->prepare("SELECT * FROM products WHERE $where_sql ORDER BY $order_by");
$stmt->execute($params);
$products = $stmt->fetchAll();

$brand_stmt = $pdo->query("
    SELECT DISTINCT bname FROM (
        SELECT name AS bname FROM brands WHERE category IN ('Perfume', 'Both')
        UNION
        SELECT brand AS bname FROM products WHERE (category = 'Perfume' OR category IN (SELECT name FROM categories WHERE type = 'Perfume') OR category = 'Modern' OR category = 'Signature') AND brand IS NOT NULL AND brand != ''
    ) AS combined_brands 
    ORDER BY bname ASC
");
$brands = $brand_stmt->fetchAll(PDO::FETCH_COLUMN);
if (empty($brands)) {
    $brands = ['Chanel', 'Creed', 'Dior', 'Versace', 'Yves Saint Laurent'];
}

$wishlist_product_ids = [];
if (isset($_SESSION['user_id'])) {
    $wl_stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $wl_stmt->execute([$_SESSION['user_id']]);
    $wishlist_product_ids = $wl_stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    background-color: #ffffff;
    font-family: 'Inter', sans-serif;
}
.perfumes-header {
    font-family: 'Playfair Display', serif;
    font-size: 2.8rem;
    font-weight: 700;
    color: #111;
    letter-spacing: 1px;
}
.filter-bar {
    background: transparent;
    border-bottom: 1px solid #eaeaea;
    padding-bottom: 1.5rem;
    margin-bottom: 3rem;
}
.filter-bar select {
    border-radius: 4px;
    border: 1px solid #ccc;
    padding: 0.6rem 1rem;
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    background-color: #fff;
    cursor: pointer;
    transition: border-color 0.2s ease;
    width: 130px;
}
.filter-bar select:hover, .filter-bar select:focus {
    border-color: #333;
    outline: none;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 2rem;
}
@media (min-width: 576px) {
    .product-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 992px) {
    .product-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (min-width: 1200px) {
    .product-grid { grid-template-columns: repeat(4, 1fr); }
}

.product-card {
    background: #fff;
    border: 1px solid #eaeaea;
    border-radius: 12px;
    padding: 1rem;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.product-card:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    transform: translateY(-5px);
    border-color: #ddd;
}
.product-image-wrapper {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background-color: #ffffff;
    margin-bottom: 1rem;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
}
.product-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.product-card:hover .product-img {
    transform: scale(1.05);
}
.product-details {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.product-brand {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #888;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.product-title {
    font-size: 1.05rem;
    font-weight: 500;
    color: #222;
    margin-bottom: 0.5rem;
    line-height: 1.4;
    flex-grow: 1;
}
.product-price {
    font-size: 1.15rem;
    font-weight: 600;
    color: #111;
    margin-top: auto;
    display: flex;
    align-items: baseline;
    gap: 8px;
    flex-wrap: wrap;
}
.product-price .original-price {
    font-size: 0.95rem;
    font-weight: 400;
    color: #888;
    text-decoration: line-through;
}
.product-price .discounted-price {
    font-size: 1.15rem;
    font-weight: 700;
    color: #e63946;
}
.discount-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 10;
    background: #e63946;
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 6px;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 6px rgba(230, 57, 70, 0.35);
    line-height: 1.2;
    pointer-events: none;
}
</style>

<div class="container py-5">

    <div class="filter-bar d-flex justify-content-between align-items-end flex-wrap gap-3">
        <h1 class="perfumes-header m-0">Perfumes</h1>
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">

            <select name="gender" onchange="this.form.submit()">
                <option value="">Gender</option>
                <option value="Men" <?php echo $gender_filter == 'Men' ? 'selected' : ''; ?>>Men</option>
                <option value="Women" <?php echo $gender_filter == 'Women' ? 'selected' : ''; ?>>Women</option>
                <option value="Unisex" <?php echo $gender_filter == 'Unisex' ? 'selected' : ''; ?>>Unisex</option>
            </select>

            <select name="brand" onchange="this.form.submit()">
                <option value="">Brand</option>
                <?php foreach($brands as $b): ?>
                    <option value="<?php echo htmlspecialchars($b); ?>" <?php echo ($brand_filter == $b || (($b == 'Yves Saint Laurent' || $b == 'YSL') && ($brand_filter == 'YSL' || $brand_filter == 'Yves Saint Laurent'))) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($b); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="sort" onchange="this.form.submit()">
                <option value="default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Sort By</option>
                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
            </select>
        </form>
    </div>

    <div class="product-grid">
        <?php
        if (count($products) > 0) {
            foreach($products as $p) {
                $img = htmlspecialchars($p['image']);
                $name = htmlspecialchars($p['name']);
                $brand = htmlspecialchars($p['brand']);
                $price = formatIndianNumber($p['price']);
                $id = $p['id'];
                $in_wishlist = in_array($id, $wishlist_product_ids);
                $heart_icon = $in_wishlist ? 'bi-heart-fill' : 'bi-heart';
                $heart_color = $in_wishlist ? '#dc3545' : '#666';
                $wishlist_action = $in_wishlist ? 'remove' : 'add';

                $has_discount = (!empty($p['discount_price']) && $p['discount_price'] > 0 && $p['discount_price'] < $p['price']);
                $discount_badge = "";
                if ($has_discount) {
                    $price_html = "<span class='original-price'>₹" . formatIndianNumber($p['price']) . "</span><span class='discounted-price'>₹" . formatIndianNumber($p['discount_price']) . "</span>";
                } else {
                    $price_html = "<span>₹" . $price . "</span>";
                }

                echo "
                <div class='product-card'>
                    <div class='product-image-wrapper'>
                        {$discount_badge}
                        <a href='product-details.php?id={$id}' class='d-block w-100 h-100'>
                            <img src='{$img}' alt='{$name}' class='product-img'>
                        </a>
                        <form action='wishlist.php' method='POST' class='position-absolute m-0 p-0' style='top: 12px; right: 12px; z-index: 10;'>
                            <input type='hidden' name='action' value='{$wishlist_action}'>
                            <input type='hidden' name='product_id' value='{$id}'>
                            <button type='submit' onmouseover='this.style.transform=\"scale(1.1)\"' onmouseout='this.style.transform=\"scale(1)\"' style='background: white; border: none; border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.2s ease; cursor: pointer;'>
                                <i class='bi {$heart_icon}' style='color: {$heart_color}; font-size: 15px; margin-top: 2px;'></i>
                            </button>
                        </form>
                    </div>
                    <div class='product-details'>
                        <div class='product-brand'>{$brand}</div>
                        <a href='product-details.php?id={$id}' class='text-decoration-none'>
                            <h3 class='product-title'>{$name}</h3>
                        </a>
                        <div class='product-price'>{$price_html}</div>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='w-100' style='grid-column: 1 / -1;'><p class='text-muted text-center py-5'>No perfumes found matching your criteria.</p></div>";
          }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
