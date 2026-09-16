<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
require_once 'includes/auth.php';
require_login();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found or unauthorized.");
}

$stmt_items = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
        <h1 class="fw-bold" style="color: var(--primary-theme);">Order Successful!</h1>
        <p class="lead text-muted">Thank you for your purchase. Your order has been placed.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg p-0" id="invoice">
                <div class="card-header bg-dark text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="m-0 fw-bold text-theme" style="color: var(--primary-theme);">ChronoScent Elite</h2>
                        <div class="text-end">
                            <h5 class="m-0">INVOICE</h5>
                            <small>#INV-<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-5">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <h6 class="text-muted fw-bold">Billed To:</h6>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($order['name']); ?></p>
                            <p class="mb-0"><?php echo htmlspecialchars($order['address']); ?></p>
                            <p class="mb-0"><?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' ' . $order['pincode']); ?></p>
                            <p class="mb-0"><?php echo htmlspecialchars($order['email']); ?></p>
                            <p class="mb-0"><?php echo htmlspecialchars($order['phone']); ?></p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h6 class="text-muted fw-bold">Order Details:</h6>
                            <p class="mb-0"><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
                            <p class="mb-0"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <p class="mb-0"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                        </div>
                    </div>

                    <table class="table table-borderless mb-4">
                        <thead class="border-bottom">
                            <tr>
                                <th class="py-3">Product</th>
                                <th class="text-center py-3">Qty</th>
                                <th class="text-end py-3">Price</th>
                                <th class="text-end py-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="py-3"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="text-center py-3"><?php echo $item['quantity']; ?></td>
                                <td class="text-end py-3">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-end py-3">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-8"></div>
                        <div class="col-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Subtotal:</span>
                                <span>$<?php echo number_format($order['total_amount'] + $order['discount'], 2); ?></span>
                            </div>
                            <?php if($order['discount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Discount Saved:</span>
                                <span class="text-success">-$<?php echo number_format($order['discount'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <hr>
                            <div class="d-flex justify-content-between fs-5 fw-bold" style="color: var(--primary-theme);">
                                <span>Total:</span>
                                <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button onclick="downloadPDF()" class="btn btn-outline-dark me-2"><i class="bi bi-file-earmark-pdf"></i> Download PDF Invoice</button>
                <a href="review.php?product_id=<?php echo $items[0]['product_id']; ?>" class="btn btn-theme">Leave a Review</a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #invoice, #invoice * { visibility: visible; }
    #invoice { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
    const element = document.getElementById('invoice');
    const opt = {
        margin:       0.5,
        filename:     'Invoice_INV-<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>

<?php require_once 'includes/footer.php'; ?>
