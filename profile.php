<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_login();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="container py-5 my-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center p-4">
                <i class="bi bi-person-circle display-1 text-muted mb-3"></i>
                <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                <p class="text-muted"><?php echo htmlspecialchars($user['phone']); ?></p>
                <hr>
                <a href="orders.php" class="btn btn-outline-theme rounded-pill w-100 mb-3 shadow-sm">My Orders</a>
                <a href="wishlist.php" class="btn btn-outline-theme rounded-pill w-100 mb-3 shadow-sm">My Wishlist</a>
                <a href="logout.php" class="btn btn-outline-danger rounded-pill w-100 shadow-sm">Logout</a>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4">
                <h3 class="fw-bold mb-4" style="color: var(--primary-theme);">Profile Details</h3>
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Member Since</label>
                            <input type="text" class="form-control" value="<?php echo date('F Y', strtotime($user['created_at'])); ?>" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
