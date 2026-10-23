<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// Ensure site_settings table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$defaults = [
    'store_name' => 'ChronoScent Elite',
    'store_tagline' => 'Luxury Watches & Fragrances',
    'contact_email' => 'hello@chronoscentelite.com',
    'contact_phone' => '+91 98765 43210',
    'store_address' => 'Luxury Boulevard, Surat, Gujarat, India',
    'currency_symbol' => '₹',
    'currency_code' => 'INR',
    'free_shipping_threshold' => '2500',
    'low_stock_threshold' => '5',
    'maintenance_mode' => '0',
    'order_notifications' => '1',
    'allow_registration' => '1',
    'tax_rate' => '0'
];

$success = '';
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    try {
        $upsert = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        foreach ($defaults as $key => $default_val) {
            if (in_array($key, ['maintenance_mode', 'order_notifications', 'allow_registration'])) {
                $val = isset($_POST[$key]) ? '1' : '0';
            } else {
                $val = isset($_POST[$key]) ? trim($_POST[$key]) : $default_val;
            }
            $upsert->execute([$key, $val, $val]);
        }
        $success = "Store settings have been updated successfully!";
    } catch (\Throwable $e) {
        $error = "Failed to save settings: " . $e->getMessage();
    }
}

// Fetch current values
$current_settings = $defaults;
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
while ($row = $stmt->fetch()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Settings - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?= time(); ?>">
    <style>
        .settings-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .settings-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .settings-card-header i {
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
        .settings-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .settings-card-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }
        .form-label {
            font-size: 0.86rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border-color: #cbd5e1;
            font-size: 0.9rem;
            padding: 10px 14px;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1e3a5f;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        }
        .form-check-input:checked {
            background-color: #1e3a5f;
            border-color: #1e3a5f;
        }
        .sys-info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 0.88rem;
        }
        .sys-info-row:last-child {
            border-bottom: none;
        }
        .sys-info-label {
            color: #64748b;
            font-weight: 500;
        }
        .sys-info-value {
            color: #0f172a;
            font-weight: 600;
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

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div><?= htmlspecialchars($success) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Header Banner -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1" style="color: #0f172a;">Store & System Settings</h4>
                        <p class="text-muted mb-0 small">Configure luxury boutique identity, currency, shipping rules, and operational parameters.</p>
                    </div>
                    <button type="submit" form="settingsForm" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2" style="background: #1e3a5f; border-color: #1e3a5f; border-radius: 8px; font-weight: 600;">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>

                <!-- Stats Overview Row -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">STORE CURRENCY</div>
                                <div class="stat-value"><?= htmlspecialchars($current_settings['currency_symbol']) ?> <?= htmlspecialchars($current_settings['currency_code']) ?></div>
                                <div class="stat-subtitle">Indian Rupee Format</div>
                            </div>
                            <div class="stat-icon-wrapper icon-blue">
                                <i class="bi bi-currency-rupee"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">FREE SHIPPING</div>
                                <div class="stat-value">₹<?= formatIndianNumber((float)$current_settings['free_shipping_threshold']) ?></div>
                                <div class="stat-subtitle">Cart Value Eligibility</div>
                            </div>
                            <div class="stat-icon-wrapper icon-green">
                                <i class="bi bi-truck"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">LOW STOCK ALERT</div>
                                <div class="stat-value"><?= htmlspecialchars($current_settings['low_stock_threshold']) ?> units</div>
                                <div class="stat-subtitle">Warning Threshold</div>
                            </div>
                            <div class="stat-icon-wrapper icon-orange">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">STORE STATUS</div>
                                <div class="stat-value <?= $current_settings['maintenance_mode'] == '1' ? 'text-warning' : 'text-success' ?>">
                                    <?= $current_settings['maintenance_mode'] == '1' ? 'Maintenance' : 'Live / Active' ?>
                                </div>
                                <div class="stat-subtitle">E-Commerce Gateway</div>
                            </div>
                            <div class="stat-icon-wrapper icon-purple">
                                <i class="bi bi-globe2"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Form -->
                <form method="POST" id="settingsForm">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <!-- 1. Boutique Identity -->
                            <div class="settings-card">
                                <div class="settings-card-header">
                                    <i class="bi bi-shop"></i>
                                    <div>
                                        <h5 class="settings-card-title">Boutique Identity & Branding</h5>
                                        <p class="settings-card-desc">Brand name, promotional tagline, and public display info.</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Store Brand Name</label>
                                        <input type="text" name="store_name" class="form-control" value="<?= htmlspecialchars($current_settings['store_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Store Tagline / Slogan</label>
                                        <input type="text" name="store_tagline" class="form-control" value="<?= htmlspecialchars($current_settings['store_tagline']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Customer Support Email</label>
                                        <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($current_settings['contact_email']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Phone / WhatsApp</label>
                                        <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($current_settings['contact_phone']) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Showroom / Support Address</label>
                                        <input type="text" name="store_address" class="form-control" value="<?= htmlspecialchars($current_settings['store_address']) ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Commerce, Shipping & Inventory -->
                            <div class="settings-card">
                                <div class="settings-card-header">
                                    <i class="bi bi-currency-exchange"></i>
                                    <div>
                                        <h5 class="settings-card-title">Commerce, Currency & Fulfillment</h5>
                                        <p class="settings-card-desc">Currency standard, tax preferences, and free shipping conditions.</p>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Currency Symbol</label>
                                        <input type="text" name="currency_symbol" class="form-control" value="<?= htmlspecialchars($current_settings['currency_symbol']) ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Currency Code</label>
                                        <input type="text" name="currency_code" class="form-control" value="<?= htmlspecialchars($current_settings['currency_code']) ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tax / GST Rate (%)</label>
                                        <input type="number" step="0.1" name="tax_rate" class="form-control" value="<?= htmlspecialchars($current_settings['tax_rate']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Free Shipping Minimum (₹)</label>
                                        <input type="number" step="1" name="free_shipping_threshold" class="form-control" value="<?= htmlspecialchars($current_settings['free_shipping_threshold']) ?>" required>
                                        <small class="text-muted">Orders equal or above this get free delivery.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Low Stock Warning Limit</label>
                                        <input type="number" step="1" name="low_stock_threshold" class="form-control" value="<?= htmlspecialchars($current_settings['low_stock_threshold']) ?>" required>
                                        <small class="text-muted">Highlights products with inventory at or below this count.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- 3. Operations & Preferences -->
                            <div class="settings-card">
                                <div class="settings-card-header">
                                    <i class="bi bi-sliders2"></i>
                                    <div>
                                        <h5 class="settings-card-title">Store Controls</h5>
                                        <p class="settings-card-desc">Operational feature switches.</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="order_notifications" id="notifSwitch" <?= $current_settings['order_notifications'] == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold" for="notifSwitch">Order Email Alerts</label>
                                        <div class="small text-muted">Log email alerts upon customer order changes.</div>
                                    </div>
                                    <hr class="my-1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="allow_registration" id="regSwitch" <?= $current_settings['allow_registration'] == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold" for="regSwitch">Customer Registration</label>
                                        <div class="small text-muted">Allow new visitors to register accounts.</div>
                                    </div>
                                    <hr class="my-1">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintSwitch" <?= $current_settings['maintenance_mode'] == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-semibold text-danger" for="maintSwitch">Maintenance Mode</label>
                                        <div class="small text-muted">Show temporary maintenance banner to shoppers.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. System Environment Info -->
                            <div class="settings-card">
                                <div class="settings-card-header">
                                    <i class="bi bi-hdd-network"></i>
                                    <div>
                                        <h5 class="settings-card-title">Server Environment</h5>
                                        <p class="settings-card-desc">Hosting runtime specifications.</p>
                                    </div>
                                </div>
                                <div class="sys-info-row">
                                    <span class="sys-info-label">PHP Engine</span>
                                    <span class="sys-info-value"><?= phpversion() ?></span>
                                </div>
                                <div class="sys-info-row">
                                    <span class="sys-info-label">Web Server</span>
                                    <span class="sys-info-value"><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Apache') ?></span>
                                </div>
                                <div class="sys-info-row">
                                    <span class="sys-info-label">Database</span>
                                    <span class="sys-info-value">MySQL (PDO)</span>
                                </div>
                                <div class="sys-info-row">
                                    <span class="sys-info-label">Server Timezone</span>
                                    <span class="sys-info-value"><?= date_default_timezone_get() ?></span>
                                </div>
                                <div class="sys-info-row">
                                    <span class="sys-info-label">Current Time</span>
                                    <span class="sys-info-value"><?= date('H:i:s d-M-Y') ?></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="background: #1e3a5f; border-color: #1e3a5f; border-radius: 8px;">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
