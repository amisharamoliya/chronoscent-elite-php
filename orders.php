<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_login();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<div class="container py-5 min-vh-100">
    <h1 class="fw-bold mb-4" style="width: 250px;">My Orders</h1>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="bi bi-box-seam display-1 text-muted mb-3 d-block"></i>
            <h4>You haven't placed any orders yet.</h4>
            <a href="index.php" class="btn btn-theme mt-3">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="fw-bold">#INV-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td class="fw-bold text-success">$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td>
                                <?php
                                $status = $order['status'];
                                $badge = 'bg-secondary';
                                if ($status == 'Pending') $badge = 'bg-warning text-dark';
                                if ($status == 'processing') $badge = 'bg-info text-dark';
                                if ($status == 'Shipped') $badge = 'bg-primary';
                                if ($status == 'Delivered') $badge = 'bg-success';
                                if ($status == 'Cancelled') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-theme" data-bs-toggle="modal" data-bs-target="#trackModal<?php echo $order['id']; ?>">
                                    Track Order
                                </button>
                                <a href="bill.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-dark">Invoice</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php foreach ($orders as $order):
            $status = $order['status'];
        ?>
        <div class="modal fade" id="trackModal<?php echo $order['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Track Order #INV-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="position-relative m-4">
                            <div class="progress" style="height: 4px;">
                                <?php
                                $progress = 0;
                                if(strtolower($status) == 'pending') $progress = 25;
                                if(strtolower($status) == 'processing') $progress = 50;
                                if(strtolower($status) == 'shipped') $progress = 75;
                                if(strtolower($status) == 'delivered') $progress = 100;
                                ?>
                                <div class="progress-bar bg-theme" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-3 position-relative">
                                <div class="text-center">
                                    <div class="rounded-circle <?php echo $progress >= 25 ? 'bg-theme text-white' : 'bg-light text-muted'; ?> d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 30px; height: 30px; margin-top: -25px;"><i class="bi bi-cart"></i></div>
                                    <small class="fw-bold">Pending</small>
                                </div>
                                <div class="text-center">
                                    <div class="rounded-circle <?php echo $progress >= 50 ? 'bg-theme text-white' : 'bg-light text-muted'; ?> d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 30px; height: 30px; margin-top: -25px;"><i class="bi bi-box"></i></div>
                                    <small class="fw-bold">Processing</small>
                                </div>
                                <div class="text-center">
                                    <div class="rounded-circle <?php echo $progress >= 75 ? 'bg-theme text-white' : 'bg-light text-muted'; ?> d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 30px; height: 30px; margin-top: -25px;"><i class="bi bi-truck"></i></div>
                                    <small class="fw-bold">Shipped</small>
                                </div>
                                <div class="text-center">
                                    <div class="rounded-circle <?php echo $progress >= 100 ? 'bg-theme text-white' : 'bg-light text-muted'; ?> d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 30px; height: 30px; margin-top: -25px;"><i class="bi bi-house"></i></div>
                                    <small class="fw-bold">Delivered</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
