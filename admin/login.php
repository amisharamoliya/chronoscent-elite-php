<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

require_once '../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && (password_verify($password, $admin['password']) || $password === $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - ChronoScent Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin-login.css?v=2">
</head>
<body>
    <div class="login-box">
        <h3 class="text-center fw-bold mb-4">Admin Login</h3>
        <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST" autocomplete="off">
            <!-- Dummy inputs to absorb browser autofill heuristics -->
            <div style="position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; overflow: hidden; opacity: 0; pointer-events: none;" aria-hidden="true">
                <input type="text" name="fake_admin_user" tabindex="-1" autocomplete="off">
                <input type="password" name="fake_admin_pass" tabindex="-1" autocomplete="new-password">
            </div>

            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" id="admin_username" class="form-control" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" required>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" id="admin_password" class="form-control" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Login</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const userInput = document.getElementById('admin_username');
        const passInput = document.getElementById('admin_password');

        function clearAutofill() {
            if (userInput && !userInput.matches(':focus')) {
                userInput.value = '';
            }
            if (passInput && !passInput.matches(':focus')) {
                passInput.value = '';
            }
        }

        clearAutofill();
        setTimeout(clearAutofill, 50);
        setTimeout(clearAutofill, 150);
        setTimeout(clearAutofill, 300);
        setTimeout(clearAutofill, 500);

        [userInput, passInput].forEach(function(field) {
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
</body>
</html>
