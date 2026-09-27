<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'includes/auth.php';
require_login();

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$subtotal = 0;
$total_discount = 0;
foreach ($_SESSION['cart'] as $id => $quantity) {
    $stmt = $pdo->prepare("SELECT price, discount_price FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if ($p) {
        $price = ($p['discount_price'] > 0) ? $p['discount_price'] : $p['price'];
        $subtotal += $price * $quantity;
        if ($p['discount_price'] > 0) {
            $total_discount += ($p['price'] - $p['discount_price']) * $quantity;
        }
    }
}

// Handle Coupon
$coupon_discount_percent = 0;
$coupon_code = '';
if (isset($_POST['apply_coupon'])) {
    $coupon_code = trim($_POST['coupon_code']);
    $stmt = $pdo->prepare("SELECT discount_percent FROM coupons WHERE code = ? AND is_active = 1 AND valid_until >= CURDATE()");
    $stmt->execute([$coupon_code]);
    $coupon = $stmt->fetch();
    if ($coupon) {
        $coupon_discount_percent = $coupon['discount_percent'];
        $discount_amt = ($subtotal * ($coupon_discount_percent / 100));
        $total_discount += $discount_amt;
        $subtotal = $subtotal - $discount_amt;
    }
}

if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $pincode = trim($_POST['pincode']);
    $payment_method = $_POST['payment_method'];
    
    // Check if Online Payment is selected
    if ($payment_method == 'Online Payment (Demo)') {
        // Here we would typically redirect to a payment gateway (e.g., Stripe, Razorpay)
        // For demo purposes, we will simulate a successful payment and just set status to 'processing'
        $status = 'processing';
    } else {
        $status = 'pending'; // COD
    }
    
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, email, phone, address, city, state, pincode, total_amount, discount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $name, $email, $phone, $address, $city, $state, $pincode, $subtotal, $total_discount, $payment_method, $status])) {
        $order_id = $pdo->lastInsertId();
        
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $p_stmt = $pdo->prepare("SELECT price, discount_price FROM products WHERE id = ?");
            $p_stmt->execute([$id]);
            $p = $p_stmt->fetch();
            $price = ($p['discount_price'] > 0) ? $p['discount_price'] : $p['price'];
            
            $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)")->execute([$order_id, $id, $quantity, $price]);
        }
        
        // Send Email Confirmation
        require_once 'includes/mailer.php';
        send_email($email, "Order Confirmation #INV-" . str_pad($order_id, 6, '0', STR_PAD_LEFT), "Thank you for your purchase, $name! Your order has been placed successfully.");
        
        unset($_SESSION['cart']);
        header("Location: bill.php?id=$order_id");
        exit;
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="fw-bold mb-5" style="color: var(--primary-theme);">Checkout</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Customer Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" id="checkoutForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" required>
                            </div>
                        </div>
                        
                        <h5 class="fw-bold mb-3 mt-4">Payment Method</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="Cash on Delivery" checked>
                            <label class="form-check-label">Cash on Delivery (COD)</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="payment_method" value="Online Payment (Demo)">
                            <label class="form-check-label">Online Payment (Demo)</label>
                        </div>
                        <input type="hidden" name="place_order" value="1">
                        <!-- We can also carry over the coupon code so the order gets the discount -->
                        <?php if($coupon_discount_percent > 0): ?>
                        <input type="hidden" name="applied_coupon" value="<?php echo htmlspecialchars($coupon_code); ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <?php 
                    foreach ($_SESSION['cart'] as $id => $quantity): 
                        $stmt = $pdo->prepare("SELECT name, price, discount_price FROM products WHERE id = ?");
                        $stmt->execute([$id]);
                        $p = $stmt->fetch();
                        $price = ($p['discount_price'] > 0) ? $p['discount_price'] : $p['price'];
                    ?>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span><?php echo htmlspecialchars($p['name']); ?> x <?php echo $quantity; ?></span>
                            <span>$<?php echo number_format($price * $quantity, 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if($total_discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 small text-success">
                        <span>Discount Saved</span>
                        <span>-$<?php echo number_format($total_discount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    <!-- Coupon Form -->
                    <form method="POST" class="d-flex mb-3">
                        <input type="text" name="coupon_code" class="form-control me-2" placeholder="Coupon Code" value="<?php echo htmlspecialchars($coupon_code); ?>">
                        <button type="submit" name="apply_coupon" class="btn btn-outline-secondary">Apply</button>
                    </form>
                    <?php if($coupon_discount_percent > 0): ?>
                        <div class="alert alert-success py-2 small">Coupon Applied! <?php echo $coupon_discount_percent; ?>% Off</div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-4 fw-bold fs-5">
                        <span>Total</span>
                        <span style="color: var(--primary-theme);">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <button type="submit" form="checkoutForm" class="btn btn-theme btn-hover-transparent w-100 py-3 fw-bold text-uppercase">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
