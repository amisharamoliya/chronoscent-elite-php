<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$msg = '';
$err = '';

// Handle Message Deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Message #$id deleted successfully.";
    } catch (\Throwable $e) {
        $err = "Failed to delete message: " . $e->getMessage();
    }
    header("Location: messages.php" . ($msg ? "?msg=" . urlencode($msg) : ""));
    exit;
}

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}

// Fetch all contact messages
$stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
$total_messages = count($messages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Messages - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?= time(); ?>">
    <style>
        .message-preview {
            max-width: 380px;
            white-space: normal;
            word-break: break-word;
            line-height: 1.45;
        }
    </style>
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

                <!-- Alert Messages -->
                <?php if ($msg): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($err): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($err) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Top Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">TOTAL INQUIRIES</div>
                                <div class="stat-value"><?= $total_messages ?></div>
                                <div class="stat-subtitle">Contact Form Messages</div>
                            </div>
                            <div class="stat-icon-wrapper icon-blue">
                                <i class="bi bi-envelope"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">CONTACT CHANNEL</div>
                                <div class="stat-value text-success" style="font-size: 1.3rem;">Boutique Portal</div>
                                <div class="stat-subtitle">contact.php Form</div>
                            </div>
                            <div class="stat-icon-wrapper icon-green">
                                <i class="bi bi-chat-left-dots"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">LATEST SUBMISSION</div>
                                <div class="stat-value" style="font-size: 1.15rem;">
                                    <?= !empty($messages) ? date('M d, Y', strtotime($messages[0]['created_at'])) : 'No Messages' ?>
                                </div>
                                <div class="stat-subtitle">Last Received Inquiry</div>
                            </div>
                            <div class="stat-icon-wrapper icon-purple">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Table Card -->
                <div class="purchases-card">
                    <div class="purchases-header">
                        <div>
                            <h5 class="purchases-title">Customer Contact Inquiries</h5>
                            <p class="purchases-count"><?= $total_messages ?> inquiries submitted via contact.php</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="orders-table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SENDER NAME</th>
                                    <th>EMAIL ADDRESS</th>
                                    <th>SUBJECT</th>
                                    <th>MESSAGE</th>
                                    <th>DATE</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($total_messages > 0): ?>
                                    <?php foreach ($messages as $m): ?>
                                    <tr>
                                        <td><span class="order-id-tag">#<?= $m['id']; ?></span></td>
                                        <td>
                                            <div class="customer-name"><?= htmlspecialchars($m['name']); ?></div>
                                        </td>
                                        <td>
                                            <a href="mailto:<?= htmlspecialchars($m['email']); ?>" class="customer-email text-decoration-none">
                                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($m['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="payment-pill"><?= htmlspecialchars($m['subject']); ?></span>
                                        </td>
                                        <td>
                                            <div class="message-preview small text-dark">
                                                <?= nl2br(htmlspecialchars($m['message'])); ?>
                                            </div>
                                        </td>
                                        <td class="text-muted small">
                                            <?= date('M d, Y', strtotime($m['created_at'])); ?><br>
                                            <span style="font-size: 0.72rem;"><?= date('h:i A', strtotime($m['created_at'])); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <a href="mailto:<?= htmlspecialchars($m['email']); ?>?subject=Re: <?= urlencode($m['subject']) ?>" class="btn btn-sm btn-outline-primary" title="Reply via Email">
                                                    <i class="bi bi-reply-fill"></i>
                                                </a>
                                                <a href="messages.php?delete=<?= $m['id']; ?>" class="btn btn-sm btn-action-delete" onclick="return confirm('Are you sure you want to delete message #<?= $m['id']; ?>?');" title="Delete Message">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                            No contact messages received yet. When a visitor submits the form on <code>contact.php</code>, it will appear here.
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
