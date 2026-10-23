<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $stmt = $pdo->prepare("SELECT product_id FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    $pid = $stmt->fetchColumn();

    $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);

    if ($pid) {
        $avg = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE product_id = ?");
        $avg->execute([$pid]);
        $new_avg = $avg->fetchColumn() ?: 0;
        $pdo->prepare("UPDATE products SET rating = ? WHERE id = ?")->execute([$new_avg, $pid]);
    }

    header("Location: reviews.php");
    exit;
}

$stmt = $pdo->query("SELECT r.*, u.name as user_name, p.name as product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Wrapper -->
        <div class="admin-main-wrapper">
            <!-- Topbar -->
            <?php include 'topbar.php'; ?>

            <main class="admin-content">
                <div class="purchases-card">
                    <div class="purchases-header">
                        <div>
                            <h5 class="purchases-title">Customer Reviews</h5>
                            <p class="purchases-count"><?= count($reviews); ?> reviews submitted</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="orders-table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>CUSTOMER</th>
                                    <th>PRODUCT</th>
                                    <th>RATING</th>
                                    <th>COMMENT</th>
                                    <th>DATE</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($reviews) > 0): ?>
                                    <?php foreach ($reviews as $r): ?>
                                    <tr>
                                        <td><span class="order-id-tag">#<?= $r['id']; ?></span></td>
                                        <td><div class="customer-name"><?= htmlspecialchars($r['user_name']); ?></div></td>
                                        <td><span class="fw-semibold text-dark"><?= htmlspecialchars($r['product_name']); ?></span></td>
                                        <td>
                                            <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">
                                                <i class="bi bi-star-fill text-dark me-1"></i><?= $r['rating']; ?>/5
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($r['title'])): ?>
                                                <div class="fw-semibold text-dark small mb-1"><?= htmlspecialchars($r['title']); ?></div>
                                            <?php endif; ?>
                                            <small class="text-muted"><?= htmlspecialchars($r['message'] ?? $r['comment'] ?? ''); ?></small>
                                        </td>
                                        <td class="text-muted small"><?= date('M d, Y', strtotime($r['created_at'])); ?></td>
                                        <td class="text-end">
                                            <a href="reviews.php?delete=<?= $r['id']; ?>" class="btn btn-sm btn-action-delete" onclick="return confirm('Are you sure you want to delete this review?');" title="Delete Review">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-star fs-1 d-block mb-2 text-secondary"></i>
                                            No reviews submitted yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
