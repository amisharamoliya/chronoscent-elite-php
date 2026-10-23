<?php

/**
 * Formats a number according to the Indian numbering system (Lakhs and Thousands).
 * e.g. 2,000, 12,000, 1,20,000, 12,00,000
 */
function formatIndianNumber($num, $decimals = 0) {
    if ($num === null || $num === '') {
        return '0';
    }
    if (!is_numeric($num)) {
        return (string)$num;
    }

    $isNeg = $num < 0;
    $num = abs((float)$num);

    if ($decimals === null) {
        if (floor($num) == $num) {
            $int = (string)round($num);
            $dec = '';
        } else {
            $formatted = sprintf('%.2f', $num);
            list($int, $decPart) = explode('.', $formatted);
            $decPart = rtrim($decPart, '0');
            $dec = $decPart !== '' ? '.' . $decPart : '';
        }
    } elseif ($decimals > 0) {
        $formatted = sprintf('%.' . $decimals . 'f', $num);
        list($int, $decPart) = explode('.', $formatted);
        $dec = '.' . $decPart;
    } else {
        $int = (string)round($num);
        $dec = '';
    }

    if (strlen($int) <= 3) {
        return ($isNeg ? '-' : '') . $int . $dec;
    }

    $last3 = substr($int, -3);
    $rest = substr($int, 0, -3);
    $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);

    return ($isNeg ? '-' : '') . $rest . ',' . $last3 . $dec;
}

function formatPrice($price, $decimals = 0) {
    return '₹' . formatIndianNumber($price, $decimals);
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
    if (!empty($discount_price) && $discount_price > 0 && $discount_price < $price) {
        $priceHtml = "<span class='original-price'>₹" . formatIndianNumber($price) . "</span> <span class='price text-danger'>₹" . formatIndianNumber($discount_price) . "</span>";
    } else {
        $priceHtml = "<span class='price'>₹" . formatIndianNumber($price) . "</span>";
    }
    $badge = "";

    $ratingHtml = '';
    if ($rating > 0) {
        $stars = '';
        for($i=1; $i<=5; $i++) {
            if ($i <= round($rating)) {
                $stars .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $stars .= '<i class="bi bi-star text-warning"></i>';
            }
        }
        $ratingHtml = "<div class='mb-2'>$stars <span class='small text-muted'>(" . number_format($rating, 1) . ")</span></div>";
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
                $ratingHtml
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
