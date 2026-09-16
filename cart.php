<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Cart Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $product_id = (int)$_POST['product_id'];
    
    if ($_POST['action'] == 'add') {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    } elseif ($_POST['action'] == 'update') {
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    } elseif ($_POST['action'] == 'remove') {
        unset($_SESSION['cart'][$product_id]);
    }
    
    if (isset($_POST['is_ajax'])) {
        $cart_count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $qty) {
                $cart_count += $qty;
            }
        }
        echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
        exit;
    }
    
    $redirect_url = 'cart.php';
    if ($_POST['action'] != 'add' && isset($_SERVER['HTTP_REFERER'])) {
        $redirect_url = $_SERVER['HTTP_REFERER'];
    }
    
    header("Location: cart.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="container py-5 min-vh-100">
    <h1 class="fw-bold mb-4" style="color: var(--primary-theme);">Shopping Cart</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted mb-3 d-block"></i>
            <h4>Your cart is currently empty.</h4>
            <a href="index.php" class="btn btn-theme mt-3">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">Product</th>
                                    <th class="py-3">Price</th>
                                    <th class="py-3">Quantity</th>
                                    <th class="py-3">Total</th>
                                    <th class="py-3 pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $subtotal = 0;
                                $total_discount = 0;
                                foreach ($_SESSION['cart'] as $id => $quantity): 
                                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                                    $stmt->execute([$id]);
                                    $p = $stmt->fetch();
                                    if ($p):
                                        $price = ($p['discount_price'] > 0) ? $p['discount_price'] : $p['price'];
                                        if ($p['discount_price'] > 0) {
                                            $total_discount += ($p['price'] - $p['discount_price']) * $quantity;
                                        }
                                        $item_total = $price * $quantity;
                                        $subtotal += $item_total;
                                ?>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" style="width: 60px; height: 60px; object-fit: cover;" class="rounded me-3">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-white"><?php echo htmlspecialchars($p['name']); ?></h6>
                                                <small class="text-white"><?php echo htmlspecialchars($p['brand']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">$<?php echo number_format($price, 2); ?></td>
                                    <td class="py-3">
                                        <form action="cart.php" method="POST" class="d-flex align-items-center" style="max-width: 120px;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <input type="number" name="quantity" class="form-control form-control-sm text-center me-2" value="<?php echo $quantity; ?>" min="1" max="<?php echo $p['stock']; ?>" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="py-3 fw-bold">$<?php echo number_format($item_total, 2); ?></td>
                                    <td class="py-3 pe-4 text-end">
                                        <form action="cart.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endif; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <?php if($total_discount > 0): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Discount Saved</span>
                            <span class="text-success">-$<?php echo number_format($total_discount, 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Grand Total</span>
                            <span class="fw-bold fs-5 text-theme" style="color: var(--primary-theme);">$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-theme w-100 py-3 fw-bold text-uppercase tracking-wide">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
