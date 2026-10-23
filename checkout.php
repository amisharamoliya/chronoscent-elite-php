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


$error = '';
if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'Cash on Delivery';

    if ($payment_method == 'Online Payment (Demo)') {
        $status = 'Confirmed';
    } else {
        $status = 'Pending';
    }

    // Verify stock availability for all items in cart before proceeding
    $stock_error = '';
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $p_check = $pdo->prepare("SELECT name, stock FROM products WHERE id = ?");
        $p_check->execute([$id]);
        $prod = $p_check->fetch();
        if (!$prod || $prod['stock'] < $quantity) {
            $avail = $prod ? (int)$prod['stock'] : 0;
            $stock_error = "Sorry, '" . ($prod ? $prod['name'] : 'Selected item') . "' does not have enough stock (Only {$avail} available).";
            break;
        }
    }

    if (!empty($stock_error)) {
        $error = $stock_error;
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, email, phone, address, city, state, pincode, total_amount, discount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $name, $email, $phone, $address, $city, $state, $pincode, $subtotal, $total_discount, $payment_method, $status]);
            $order_id = $pdo->lastInsertId();

            foreach ($_SESSION['cart'] as $id => $quantity) {
                $p_stmt = $pdo->prepare("SELECT price, discount_price FROM products WHERE id = ?");
                $p_stmt->execute([$id]);
                $p = $p_stmt->fetch();
                $price = ($p['discount_price'] > 0) ? $p['discount_price'] : $p['price'];

                $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)")->execute([$order_id, $id, $quantity, $price]);

                // Decrement stock for the purchased product
                $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?")->execute([$quantity, $id]);
            }

            $pdo->commit();

            require_once 'includes/mailer.php';
            send_email($email, "Order Confirmation #INV-" . str_pad($order_id, 6, '0', STR_PAD_LEFT), "Thank you for your purchase, $name! Your order has been placed successfully.");

            unset($_SESSION['cart']);
            header("Location: bill.php?id=$order_id");
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Failed to place order: " . $e->getMessage();
        }
    }
}

// Fetch current logged-in user info for autofill
$logged_user_id = $_SESSION['user_id'];
$u_stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$u_stmt->execute([$logged_user_id]);
$currentUser = $u_stmt->fetch(PDO::FETCH_ASSOC);

$user_name = $currentUser['name'] ?? ($_SESSION['user_name'] ?? '');
$user_email = $currentUser['email'] ?? '';
$user_phone = $currentUser['phone'] ?? '';

// Check previous order address for autofill if available
$prev_order_stmt = $pdo->prepare("SELECT address, city, state, pincode FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$prev_order_stmt->execute([$logged_user_id]);
$prevOrder = $prev_order_stmt->fetch(PDO::FETCH_ASSOC);

$val_name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($user_name);
$val_email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user_email);
$val_phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : htmlspecialchars($user_phone);
$val_address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : htmlspecialchars($prevOrder['address'] ?? '');
$val_city = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : htmlspecialchars($prevOrder['city'] ?? '');
$val_state = isset($_POST['state']) ? htmlspecialchars($_POST['state']) : htmlspecialchars($prevOrder['state'] ?? '');
$val_pincode = isset($_POST['pincode']) ? htmlspecialchars($_POST['pincode']) : htmlspecialchars($prevOrder['pincode'] ?? '');

require_once 'includes/header.php';
?>

<div class="container py-5">
    <h1 class="fw-bold mb-5" style="color: var(--primary-theme);">Checkout</h1>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

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
                                <input type="text" name="name" class="form-control" value="<?php echo $val_name; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $val_email; ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo $val_phone; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2" required><?php echo $val_address; ?></textarea>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" value="<?php echo $val_city; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" value="<?php echo $val_state; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" value="<?php echo $val_pincode; ?>" required>
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
                            <span>₹<?php echo formatIndianNumber($price * $quantity); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if($total_discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2 small text-success">
                        <span>Discount Saved</span>
                        <span>-₹<?php echo formatIndianNumber($total_discount); ?></span>
                    </div>
                    <?php endif; ?>

                    <hr>


                    <div class="d-flex justify-content-between mb-4 fw-bold fs-5">
                        <span>Total</span>
                        <span style="color: var(--primary-theme);">₹<?php echo formatIndianNumber($subtotal); ?></span>
                    </div>
                    <button type="submit" form="checkoutForm" class="btn btn-outline-theme rounded-pill w-100 py-3 fw-bold text-uppercase shadow">Place Order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
