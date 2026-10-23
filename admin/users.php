<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: users.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - ChronoScent Elite Admin</title>
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
                            <h5 class="purchases-title">Registered Customers</h5>
                            <p class="purchases-count"><?= count($users); ?> registered accounts</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="orders-table align-middle">
                            <thead>
                                <tr>
                                    <th>USER ID</th>
                                    <th>CUSTOMER NAME</th>
                                    <th>EMAIL ADDRESS</th>
                                    <th>PHONE NUMBER</th>
                                    <th>JOINED DATE</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><span class="order-id-tag">#<?= $u['id']; ?></span></td>
                                    <td>
                                        <div class="customer-name"><?= htmlspecialchars($u['name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="customer-email"><?= htmlspecialchars($u['email']); ?></div>
                                    </td>
                                    <td>
                                        <span class="payment-pill"><?= htmlspecialchars($u['phone'] ?: 'N/A'); ?></span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('M d, Y', strtotime($u['created_at'])); ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="users.php?delete=<?= $u['id']; ?>" class="btn btn-sm btn-action-delete" onclick="return confirm('Are you sure you want to remove this user?');" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
