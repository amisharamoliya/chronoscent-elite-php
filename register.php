<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
                $_SESSION['registered_email'] = $email;
                $_SESSION['registered_password'] = $password;
                $_SESSION['reg_success'] = 'Registration successful! You can now login with your details.';
                header("Location: login.php");
                exit;
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card auth-card-shadow border-0">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold" style="color: var(--primary-theme);">CREATE ACCOUNT</h2>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" id="regForm" autocomplete="off">
                        <!-- Dummy inputs to absorb browser autofill heuristics -->
                        <div style="position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; overflow: hidden; opacity: 0; pointer-events: none;" aria-hidden="true">
                            <input type="text" name="fake_reg_name" tabindex="-1" autocomplete="off">
                            <input type="email" name="fake_reg_email" tabindex="-1" autocomplete="off">
                            <input type="password" name="fake_reg_pass" tabindex="-1" autocomplete="new-password">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" id="reg_name" class="form-control" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="reg_email" class="form-control" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="reg_phone" class="form-control" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="reg_password" class="form-control" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="reg_confirm_password" class="form-control" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" onmousedown="this.removeAttribute('readonly');" ontouchstart="this.removeAttribute('readonly');" required>
                        </div>
                        <button type="submit" class="btn btn-outline-theme rounded-pill w-100 py-3 fw-bold shadow">REGISTER</button>
                    </form>
                    <div class="text-center mt-4">
                        <p>Already have an account? <a href="login.php" class="text-decoration-none" style="color: var(--primary-theme);">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const regInputs = document.querySelectorAll('#regForm input:not([type="hidden"])');

    function clearAutofill() {
        regInputs.forEach(function (input) {
            if (!input.matches(':focus') && input.getAttribute('tabindex') !== '-1') {
                input.value = '';
            }
        });
    }

    clearAutofill();
    setTimeout(clearAutofill, 50);
    setTimeout(clearAutofill, 150);
    setTimeout(clearAutofill, 300);
    setTimeout(clearAutofill, 500);

    regInputs.forEach(function(field) {
        ['focus', 'mousedown', 'touchstart', 'keydown'].forEach(function(evt) {
            field.addEventListener(evt, function() {
                this.removeAttribute('readonly');
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
