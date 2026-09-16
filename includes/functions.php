<?php
// Utility functions

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function renderProductCard($product) {
    $id = $product['id'];
    $name = htmlspecialchars($product['name']);
    $brand = htmlspecialchars($product['brand']);
    $image = htmlspecialchars($product['image']) . '?v=' . time();
    $price = $product['price'];
    $discount_price = $product['discount_price'];
    $rating = $product['rating'];
    
    $priceHtml = '';
    if ($discount_price) {
        $percent = round((($price - $discount_price) / $price) * 100);
        $priceHtml = "<span class='original-price'>$" . number_format($price, 2) . "</span> <span class='price'>$" . number_format($discount_price, 2) . "</span>";
        $badge = "<span class='discount-badge'>-$percent%</span>";
    } else {
        $priceHtml = "<span class='price'>$" . number_format($price, 2) . "</span>";
        $badge = "";
    }

    $stars = '';
    for($i=1; $i<=5; $i++) {
        if ($i <= round($rating)) {
            $stars .= '<i class="bi bi-star-fill text-warning"></i>';
        } else {
            $stars .= '<i class="bi bi-star text-warning"></i>';
        }
    }

    return "
    <div class='col-md-4 col-sm-6 mb-4'>
        <div class='card product-card h-100 position-relative'>
            $badge
            <a href='product-details.php?id=$id'>
                <img src='$image' class='card-img-top product-img' alt='$name'>
            </a>
            <div class='card-body d-flex flex-column'>
                <p class='text-dark mb-1 small text-uppercase tracking-wide'>$brand</p>
                <h5 class='card-title product-title mb-2'>
                    <a href='product-details.php?id=$id' class='text-dark text-decoration-none fw-bold'>$name</a>
                </h5>
                <div class='mb-2'>$stars <span class='small text-muted'>($rating)</span></div>
                <div class='mt-auto d-flex justify-content-between align-items-center'>
                    <div>$priceHtml</div>
                    <form action='cart.php' method='POST' class='d-inline'>
                        <input type='hidden' name='product_id' value='$id'>
                        <input type='hidden' name='quantity' value='1'>
                        <input type='hidden' name='action' value='add'>
                        <button type='submit' class='btn btn-sm btn-theme'><i class='bi bi-cart-plus'></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ";
}
?>
