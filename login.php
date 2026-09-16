<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$error = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            $redirect_url = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';
            header("Location: $redirect_url");
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 fw-bold" style="color: var(--primary-theme);">WELCOME BACK</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Password</label>
                            <a href="forgot-password.php" class="text-decoration-none small text-muted">Forgot Password?</a>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-theme w-100 py-2">LOGIN</button>
                    </form>
                    <div class="text-center mt-4">
                        <p>Don't have an account? <a href="register.php" class="text-decoration-none" style="color: var(--primary-theme);">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
