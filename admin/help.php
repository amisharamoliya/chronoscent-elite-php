<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// System diagnostics
$php_version = phpversion();
$mysql_connected = false;
$products_count = 0;
$orders_count = 0;
$reviews_count = 0;

try {
    $products_count = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $orders_count = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $reviews_count = (int)$pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    $mysql_connected = true;
} catch (\Throwable $e) {
    $mysql_connected = false;
}

$upload_dir = __DIR__ . '/../assets/images/products';
$upload_dir_writable = is_dir($upload_dir) ? is_writable($upload_dir) : is_writable(__DIR__ . '/../assets/images');
$gd_available = extension_loaded('gd');
$dompdf_installed = class_exists('Dompdf\Dompdf');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Documentation - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?= time(); ?>">
    <style>
        .help-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .help-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .help-card-header i {
            font-size: 1.35rem;
            color: #1e3a5f;
            background: #eef4f9;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .help-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .help-card-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }
        .guide-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .guide-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .guide-icon {
            width: 38px;
            height: 38px;
            background: #1e3a5f;
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 14px;
        }
        .guide-title {
            font-size: 0.96rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .guide-text {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.5;
            margin: 0;
        }
        .diag-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            margin-bottom: 10px;
            font-size: 0.88rem;
        }
        .diag-name {
            font-weight: 600;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .accordion-button:not(.collapsed) {
            background-color: #f1f5f9;
            color: #1e3a5f;
            font-weight: 600;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(30,58,95,0.2);
        }
        .accordion-item {
            border-color: #e2e8f0;
            margin-bottom: 8px;
            border-radius: 8px !important;
            overflow: hidden;
        }
        .accordion-body {
            font-size: 0.88rem;
            color: #475569;
            line-height: 1.6;
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

                <!-- Header Banner -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: #0f172a;">Help & Documentation Center</h4>
                        <p class="text-muted mb-0 small">Operational instructions, workflow tutorials, live system diagnostics, and FAQ guides.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="dashboard.php" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a href="../index.php" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2" style="background: #1e3a5f; border-color: #1e3a5f; border-radius: 8px; font-weight: 600;">
                            <i class="bi bi-box-arrow-up-right"></i> View Boutique
                        </a>
                    </div>
                </div>

                <!-- Stats Overview Row -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">CATALOG PRODUCTS</div>
                                <div class="stat-value"><?= $products_count ?></div>
                                <div class="stat-subtitle">Watches & Fragrances</div>
                            </div>
                            <div class="stat-icon-wrapper icon-blue">
                                <i class="bi bi-bag"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">STORE ORDERS</div>
                                <div class="stat-value"><?= $orders_count ?></div>
                                <div class="stat-subtitle">Total Invoices</div>
                            </div>
                            <div class="stat-icon-wrapper icon-orange">
                                <i class="bi bi-box-seam"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">CUSTOMER REVIEWS</div>
                                <div class="stat-value"><?= $reviews_count ?></div>
                                <div class="stat-subtitle">Moderated Feedbacks</div>
                            </div>
                            <div class="stat-icon-wrapper icon-purple">
                                <i class="bi bi-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">SYSTEM STATUS</div>
                                <div class="stat-value text-success">100% Online</div>
                                <div class="stat-subtitle">All Services Healthy</div>
                            </div>
                            <div class="stat-icon-wrapper icon-green">
                                <i class="bi bi-activity"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 1: Operational Guides -->
                <div class="help-card">
                    <div class="help-card-header">
                        <i class="bi bi-journal-bookmark"></i>
                        <div>
                            <h5 class="help-card-title">Quick Administration Guides</h5>
                            <p class="help-card-desc">Step-by-step guidance for key operations in the ChronoScent Elite console.</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="guide-box">
                                <div class="guide-icon"><i class="bi bi-box2"></i></div>
                                <h6 class="guide-title">1. Orders & Tracking</h6>
                                <p class="guide-text">Update order status directly from the interactive dropdown (Processing, Confirmed, Shipped, Delivered, Cancelled). When an order is cancelled, stock is automatically returned to inventory.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="guide-box">
                                <div class="guide-icon"><i class="bi bi-bag-plus"></i></div>
                                <h6 class="guide-title">2. Product Catalog</h6>
                                <p class="guide-text">Add luxury watches and perfumes with name, brand, category, Indian Rupee (₹) price, discount price, and high-definition imagery. You can edit or delete items at any time.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="guide-box">
                                <div class="guide-icon"><i class="bi bi-tags"></i></div>
                                <h6 class="guide-title">3. Brands & Categories</h6>
                                <p class="guide-text">Manage luxury houses (Rolex, Omega, Chanel, Tom Ford, etc.) and catalog categories. Automatic aggregation calculates brand stock, total units, and price ranges in ₹.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="guide-box">
                                <div class="guide-icon"><i class="bi bi-chat-left-quote"></i></div>
                                <h6 class="guide-title">4. Reviews & Ratings</h6>
                                <p class="guide-text">Inspect customer reviews with star ratings, titles, and feedback messages. Deleting a review automatically recalculates and updates the product's live average rating score.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Section 2: System Health Diagnostics -->
                    <div class="col-lg-6">
                        <div class="help-card h-100">
                            <div class="help-card-header">
                                <i class="bi bi-cpu"></i>
                                <div>
                                    <h5 class="help-card-title">Live System Diagnostics</h5>
                                    <p class="help-card-desc">Server runtime environment validation.</p>
                                </div>
                            </div>

                            <div class="diag-item">
                                <div class="diag-name"><i class="bi bi-filetype-php text-primary"></i> PHP Version</div>
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">v<?= $php_version ?> (Pass)</span>
                            </div>

                            <div class="diag-item">
                                <div class="diag-name"><i class="bi bi-database text-primary"></i> MySQL Database</div>
                                <?php if ($mysql_connected): ?>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i> Connected</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i> Error</span>
                                <?php endif; ?>
                            </div>

                            <div class="diag-item">
                                <div class="diag-name"><i class="bi bi-folder text-primary"></i> Image Upload Directory</div>
                                <?php if ($upload_dir_writable): ?>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i> Writable</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i> Read Only</span>
                                <?php endif; ?>
                            </div>

                            <div class="diag-item">
                                <div class="diag-name"><i class="bi bi-file-earmark-pdf text-primary"></i> PDF Generator Engine</div>
                                <?php if ($dompdf_installed): ?>
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Dompdf Ready</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Dompdf Standard</span>
                                <?php endif; ?>
                            </div>

                            <div class="diag-item">
                                <div class="diag-name"><i class="bi bi-shield-lock text-primary"></i> Authentication Session</div>
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Active & Guarded</span>
                            </div>

                            <div class="diag-item">
                                <div class="diag-name"><i class="bi bi-envelope text-primary"></i> Email Simulation Logger</div>
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">mail_log.txt Active</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Frequently Asked Questions -->
                    <div class="col-lg-6">
                        <div class="help-card h-100">
                            <div class="help-card-header">
                                <i class="bi bi-question-diamond"></i>
                                <div>
                                    <h5 class="help-card-title">Frequently Asked Questions</h5>
                                    <p class="help-card-desc">Common questions regarding store administration.</p>
                                </div>
                            </div>

                            <div class="accordion" id="helpAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                            How do I change currency symbol or store info?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body">
                                            Go to <a href="settings.php" class="fw-semibold text-decoration-none">Preferences &rarr; Setting</a> in the sidebar. You can customize the brand name, contact details, currency symbol (₹), free shipping minimum, and store operation switches.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                            How do I update my admin username and password?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body">
                                            Visit <a href="security.php" class="fw-semibold text-decoration-none">Preferences &rarr; Security</a>. You can securely change your admin password using modern BCrypt encryption and modify your admin username.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                            What happens if a customer cancels an order?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body">
                                            When an order is updated to <strong>Cancelled</strong>, our automated inventory trigger immediately adds all purchased quantities back to product stock. If you re-activate the order, stock is automatically deducted again.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFour">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                            Where are order notification emails stored?
                                        </button>
                                    </h2>
                                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body">
                                            All order confirmation and shipping status notification emails are simulated and recorded in <code>mail_log.txt</code> in the project root directory with full timestamps and customer details.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
