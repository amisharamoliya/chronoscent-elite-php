<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$error = '';
$success_msg = '';
$prefill_email = '';
$prefill_password = '';

if (isset($_SESSION['reg_success'])) {
    $success_msg = $_SESSION['reg_success'];
    unset($_SESSION['reg_success']);
}
if (isset($_SESSION['registered_email'])) {
    $prefill_email = $_SESSION['registered_email'];
    unset($_SESSION['registered_email']);
}
if (isset($_SESSION['registered_password'])) {
    $prefill_password = $_SESSION['registered_password'];
    unset($_SESSION['registered_password']);
}

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login_input) || empty($password)) {
        $error = 'Please enter email/username and password.';
    } else {
        // 1. Check if credentials belong to an admin
        $stmtAdmin = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmtAdmin->execute([$login_input]);
        $admin = $stmtAdmin->fetch();

        if ($admin && (password_verify($password, $admin['password']) || $password === $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin/dashboard.php");
            exit;
        }

        // 2. Check if credentials belong to a customer / user
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$login_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            $redirect_url = isset($_POST['redirect']) && !empty($_POST['redirect']) ? $_POST['redirect'] : 'index.php';
            header("Location: $redirect_url");
            exit;
        } else {
            $error = 'Invalid email/username or password.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card auth-card-shadow border-0">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold" style="color: var(--primary-theme);">WELCOME BACK</h2>

                    <?php if ($success_msg): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success_msg); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" autocomplete="off">
                        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">

                        <!-- Dummy inputs to absorb browser autofill heuristics -->
                        <div style="position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; overflow: hidden; opacity: 0; pointer-events: none;" aria-hidden="true">
                            <input type="text" name="fake_email_autofill" tabindex="-1" autocomplete="off">
                            <input type="password" name="fake_password_autofill" tabindex="-1" autocomplete="new-password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address or Username</label>
                            <input type="text" name="email" id="login_email" class="form-control" placeholder="Enter email or admin username" autocomplete="off" <?php if (empty($prefill_email)): ?>readonly<?php endif; ?> onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" value="<?php echo htmlspecialchars($prefill_email); ?>" required>
                        </div>
                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Password</label>
                            <a href="forgot-password.php" class="text-decoration-none small text-muted">Forgot Password?</a>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="password" id="login_password" class="form-control" autocomplete="new-password" <?php if (empty($prefill_password)): ?>readonly<?php endif; ?> onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" value="<?php echo htmlspecialchars($prefill_password); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-outline-theme rounded-pill w-100 py-3 fw-bold shadow">LOGIN</button>
                    </form>
                    <div class="text-center mt-4">
                        <p>Don't have an account? <a href="register.php" class="text-decoration-none" style="color: var(--primary-theme);">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const emailField = document.getElementById('login_email');
    const passField = document.getElementById('login_password');
    const hasPrefill = <?php echo (!empty($prefill_email) || !empty($prefill_password)) ? 'true' : 'false'; ?>;

    function clearAutofill() {
        if (emailField && !emailField.matches(':focus')) {
            emailField.value = '';
        }
        if (passField && !passField.matches(':focus')) {
            passField.value = '';
        }
    }

    if (!hasPrefill) {
        clearAutofill();
        setTimeout(clearAutofill, 50);
        setTimeout(clearAutofill, 150);
        setTimeout(clearAutofill, 300);
        setTimeout(clearAutofill, 500);
    }

    [emailField, passField].forEach(function(field) {
        if (field) {
            ['focus', 'mousedown', 'touchstart', 'keydown'].forEach(function(evt) {
                field.addEventListener(evt, function() {
                    this.removeAttribute('readonly');
                });
            });
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
