<?php
require_once 'includes/header.php';

$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $subject, $message])) {
        require_once 'includes/mailer.php';
        send_email('hello@chronoscentelite.com', "New Contact Inquiry from $name: $subject", "Customer Name: $name\nEmail: $email\nSubject: $subject\nMessage: $message");
        $success = "Thank you for reaching out! We will get back to you shortly.";
    }
}
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold" style="color: var(--primary-theme);">Contact Us</h1>
        <p class="lead text-muted">We'd love to hear from you. Drop us a message!</p>
    </div>

    <div class="row g-5">
        <div class="col-md-6">
            <h3 class="mb-4">Get In Touch</h3>
            <div class="d-flex align-items-center mb-4">
                <div class="bg-light p-3 rounded-circle me-3">
                    <i class="bi bi-geo-alt fs-3" style="color: var(--primary-theme);"></i>
                </div>
                <div>
                    <h5 class="mb-1">Address</h5>
                    <p class="text-muted mb-0">Surat, Gujarat, India</p>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div class="bg-light p-3 rounded-circle me-3">
                    <i class="bi bi-envelope fs-3" style="color: var(--primary-theme);"></i>
                </div>
                <div>
                    <h5 class="mb-1">Email</h5>
                    <p class="text-muted mb-0">hello@chronoscentelite.com</p>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="bg-light p-3 rounded-circle me-3">
                    <i class="bi bi-telephone fs-3" style="color: var(--primary-theme);"></i>
                </div>
                <div>
                    <h5 class="mb-1">Phone</h5>
                    <p class="text-muted mb-0">+91 1xxxxxxxxx9</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 auth-card-shadow p-4 p-md-5">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Your Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Your Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-theme rounded-pill w-100 py-3 fw-bold shadow">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
