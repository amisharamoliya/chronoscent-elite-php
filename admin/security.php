<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$admin_id = $_SESSION['admin_id'] ?? 1;

// Fetch current admin info
$stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

$success = '';
$error = '';

// Handle Password Change
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $error = "All password fields are required.";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "New password and confirmation password do not match.";
    } elseif (strlen($new_pass) < 6) {
        $error = "New password must be at least 6 characters long.";
    } elseif (!($admin && (password_verify($current_pass, $admin['password']) || $current_pass === $admin['password']))) {
        $error = "Incorrect current password. Please try again.";
    } else {
        $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
        $update = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        if ($update->execute([$hashed, $admin_id])) {
            $success = "Admin password has been updated successfully!";
            $admin['password'] = $hashed;
        } else {
            $error = "Failed to update password. Please try again.";
        }
    }
}

// Handle Username Change
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_username') {
    $new_username = trim($_POST['username'] ?? '');

    if (empty($new_username)) {
        $error = "Username cannot be empty.";
    } elseif ($new_username === $admin['username']) {
        $error = "The new username is the same as current username.";
    } else {
        // Check uniqueness
        $check = $pdo->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
        $check->execute([$new_username, $admin_id]);
        if ($check->fetch()) {
            $error = "This username is already taken. Please choose another.";
        } else {
            $update = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
            if ($update->execute([$new_username, $admin_id])) {
                $_SESSION['admin_username'] = $new_username;
                $admin['username'] = $new_username;
                $success = "Admin username changed to '" . htmlspecialchars($new_username) . "' successfully!";
            } else {
                $error = "Failed to update username.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Security - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?= time(); ?>">
    <style>
        .security-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .security-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .security-card-header i {
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
        .security-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .security-card-desc {
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
        .form-control {
            border-color: #cbd5e1;
            font-size: 0.9rem;
            padding: 10px 14px;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: #1e3a5f;
            box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        }
        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .checklist-icon {
            font-size: 1.15rem;
            line-height: 1;
            margin-top: 2px;
        }
        .checklist-title {
            font-size: 0.88rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .checklist-sub {
            font-size: 0.78rem;
            color: #64748b;
            margin: 0;
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
                        <h4 class="fw-bold mb-1" style="color: #0f172a;">Admin Security & Access Controls</h4>
                        <p class="text-muted mb-0 small">Manage admin login credentials, verify password protection, and view session telemetry.</p>
                    </div>
                    <a href="logout.php" class="btn btn-outline-danger d-inline-flex align-items-center gap-2 px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                        <i class="bi bi-box-arrow-right"></i> Terminate Session
                    </a>
                </div>

                <!-- Security Cards Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">ACCOUNT STATUS</div>
                                <div class="stat-value text-success">Active</div>
                                <div class="stat-subtitle">Role: Administrator</div>
                            </div>
                            <div class="stat-icon-wrapper icon-green">
                                <i class="bi bi-shield-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">CURRENT USER</div>
                                <div class="stat-value"><?= htmlspecialchars($admin['username'] ?? 'admin') ?></div>
                                <div class="stat-subtitle">ID: #<?= (int)$admin_id ?></div>
                            </div>
                            <div class="stat-icon-wrapper icon-blue">
                                <i class="bi bi-person-badge"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">PASSWORD SECURITY</div>
                                <div class="stat-value text-primary">BCrypt</div>
                                <div class="stat-subtitle">10-Rounds Hash</div>
                            </div>
                            <div class="stat-icon-wrapper icon-purple">
                                <i class="bi bi-key"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">CLIENT IP</div>
                                <div class="stat-value" style="font-size: 1.15rem;"><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') ?></div>
                                <div class="stat-subtitle">Active Origin</div>
                            </div>
                            <div class="stat-icon-wrapper icon-orange">
                                <i class="bi bi-router"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left: Password & Username Forms -->
                    <div class="col-lg-7">
                        <!-- 1. Change Password -->
                        <div class="security-card">
                            <div class="security-card-header">
                                <i class="bi bi-shield-lock"></i>
                                <div>
                                    <h5 class="security-card-title">Update Master Password</h5>
                                    <p class="security-card-desc">Keep your boutique portal secure by using a strong, unique password.</p>
                                </div>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="change_password">
                                <div class="mb-3">
                                    <label class="form-label">Current Password *</label>
                                    <input type="password" name="current_password" class="form-control" placeholder="Enter current admin password" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">New Password *</label>
                                        <input type="password" name="new_password" class="form-control" placeholder="Min 6 characters" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Confirm New Password *</label>
                                        <input type="password" name="confirm_password" class="form-control" placeholder="Re-type new password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary px-4 py-2" style="background: #1e3a5f; border-color: #1e3a5f; border-radius: 8px; font-weight: 600;">
                                    <i class="bi bi-check-lg me-1"></i> Save New Password
                                </button>
                            </form>
                        </div>

                        <!-- 2. Change Username -->
                        <div class="security-card">
                            <div class="security-card-header">
                                <i class="bi bi-person-gear"></i>
                                <div>
                                    <h5 class="security-card-title">Admin Account Username</h5>
                                    <p class="security-card-desc">Change the login username for this administrator account.</p>
                                </div>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="change_username">
                                <div class="mb-3">
                                    <label class="form-label">Current Username: <strong class="text-dark"><?= htmlspecialchars($admin['username'] ?? 'admin') ?></strong></label>
                                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username'] ?? 'admin') ?>" placeholder="Enter new username" required>
                                </div>
                                <button type="submit" class="btn btn-outline-dark px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                                    <i class="bi bi-arrow-repeat me-1"></i> Update Username
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Security Checklist & Session Info -->
                    <div class="col-lg-5">
                        <!-- Security Health Checklist -->
                        <div class="security-card">
                            <div class="security-card-header">
                                <i class="bi bi-check2-circle"></i>
                                <div>
                                    <h5 class="security-card-title">Security Health</h5>
                                    <p class="security-card-desc">Current protective safeguards status.</p>
                                </div>
                            </div>

                            <div class="checklist-item">
                                <div class="checklist-icon text-success"><i class="bi bi-check-circle-fill"></i></div>
                                <div>
                                    <div class="checklist-title">SQL Injection Defense</div>
                                    <div class="checklist-sub">All database access uses parametrized PDO prepared statements.</div>
                                </div>
                            </div>

                            <div class="checklist-item">
                                <div class="checklist-icon text-success"><i class="bi bi-check-circle-fill"></i></div>
                                <div>
                                    <div class="checklist-title">XSS Output Sanitization</div>
                                    <div class="checklist-sub">All user input renders through htmlspecialchars UTF-8 escaping.</div>
                                </div>
                            </div>

                            <div class="checklist-item">
                                <div class="checklist-icon text-success"><i class="bi bi-check-circle-fill"></i></div>
                                <div>
                                    <div class="checklist-title">Session Hijacking Protection</div>
                                    <div class="checklist-sub">session_regenerate_id() is triggered immediately upon admin login.</div>
                                </div>
                            </div>

                            <div class="checklist-item">
                                <div class="checklist-icon text-success"><i class="bi bi-check-circle-fill"></i></div>
                                <div>
                                    <div class="checklist-title">Role-Based Access Enforcement</div>
                                    <div class="checklist-sub">All admin pages strictly guarded with require_admin() authentication.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Session Details -->
                        <div class="security-card">
                            <div class="security-card-header">
                                <i class="bi bi-display"></i>
                                <div>
                                    <h5 class="security-card-title">Active Session Info</h5>
                                    <p class="security-card-desc">Current connection telemetry.</p>
                                </div>
                            </div>
                            <div class="sys-info-row">
                                <span class="sys-info-label">Session ID</span>
                                <span class="sys-info-value" style="font-family: monospace; font-size: 0.8rem;"><?= substr(session_id(), 0, 10) . '...' ?></span>
                            </div>
                            <div class="sys-info-row">
                                <span class="sys-info-label">IP Address</span>
                                <span class="sys-info-value"><?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') ?></span>
                            </div>
                            <div class="sys-info-row">
                                <span class="sys-info-label">Server Port</span>
                                <span class="sys-info-value"><?= htmlspecialchars($_SERVER['SERVER_PORT'] ?? '80') ?></span>
                            </div>
                            <div class="sys-info-row">
                                <span class="sys-info-label">HTTP Protocol</span>
                                <span class="sys-info-value"><?= htmlspecialchars($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1') ?></span>
                            </div>
                            <div class="sys-info-row">
                                <span class="sys-info-label">Session Time</span>
                                <span class="sys-info-value"><?= date('h:i A') ?></span>
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
