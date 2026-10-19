<?php
require_once 'includes/header.php';
?>

<div class="container py-5 my-3">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-md-5">
                    <h1 class="fw-bold mb-4" style="color: var(--primary-theme);">Privacy Policy</h1>
                    
                    <section class="mb-4">
                        <h5 class="fw-bold text-dark">1. Overview & Purpose</h5>
                        <p class="text-muted leading-relaxed">
                            ChronoScent Elite operates an online retail catalog specializing in premium timepieces and fragrances. We are committed to maintaining the privacy, confidentiality, and integrity of every visitor and customer who interacts with our portal.
                        </p>
                        <p class="text-muted leading-relaxed mb-0">
                            This Privacy Policy outlines how your personal information is collected, processed, and safeguarded in accordance with fundamental web privacy principles.
                        </p>
                    </section>

                    <hr class="my-4 text-muted opacity-25">

                    <section class="mb-4">
                        <h5 class="fw-bold text-dark">2. Information We Collect</h5>
                        <p class="text-muted mb-2">
                            We only collect information strictly necessary to provide an authentic e-commerce shopping experience:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-2"><strong>Personal Details:</strong> Name, email address, phone number, and delivery address provided during registration or checkout.</li>
                            <li class="mb-2"><strong>Account Credentials:</strong> Your username and securely hashed password for authentication.</li>
                            <li class="mb-2"><strong>Shopping Activity:</strong> Cart contents, wishlist preferences, order history, and generated invoices.</li>
                            <li class="mb-2"><strong>Contact & Inquiries:</strong> Any questions or feedback submitted through our contact forms or product reviews.</li>
                            <li class="mb-0"><strong>Payment Information:</strong> For this project, payments are simulated (e.g. Cash on Delivery). We do not store credit card numbers or banking PINs.</li>
                        </ul>
                    </section>

                    <hr class="my-4 text-muted opacity-25">

                    <section class="mb-4">
                        <h5 class="fw-bold text-dark">3. How We Use Information</h5>
                        <p class="text-muted mb-2">
                            Your collected information is used solely for legitimate operational purposes:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-2"><strong>Order Processing:</strong> To confirm your orders, update delivery statuses, and generate printable invoices.</li>
                            <li class="mb-2"><strong>Account Management:</strong> To enable account login, order tracking, wishlist management, and profile updates.</li>
                            <li class="mb-2"><strong>Customer Support:</strong> To respond promptly to inquiries or issues submitted via our contact page.</li>
                            <li class="mb-0"><strong>Platform Experience:</strong> To maintain session state, retain cart items, and personalize your browsing flow.</li>
                        </ul>
                    </section>

                    <hr class="my-4 text-muted opacity-25">

                    <section class="mb-4">
                        <h5 class="fw-bold text-dark">4. Data Security & Storage</h5>
                        <p class="text-muted mb-2">
                            We implement standard web development security practices to safeguard your information:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-2"><strong>Password Encryption:</strong> Passwords are encrypted using PHP's secure <code>password_hash()</code> with bcrypt.</li>
                            <li class="mb-2"><strong>Database Protection:</strong> All database queries use PDO prepared statements to protect against SQL injection vulnerabilities.</li>
                            <li class="mb-0"><strong>Session Security:</strong> Access to protected accounts and administrative sections is secured with active session verification.</li>
                        </ul>
                    </section>

                    <hr class="my-4 text-muted opacity-25">

                    <section class="mb-4">
                        <h5 class="fw-bold text-dark">5. Cookies & Sessions</h5>
                        <p class="text-muted mb-2">
                            ChronoScent Elite uses standard PHP session cookies strictly for website functionality:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-2">Retaining your login state while navigating across pages.</li>
                            <li class="mb-2">Storing items in your shopping cart before and during checkout.</li>
                            <li class="mb-0">We do not deploy third-party advertising or cross-site tracking cookies.</li>
                        </ul>
                    </section>

                    <hr class="my-4 text-muted opacity-25">

                    <section class="mb-4">
                        <h5 class="fw-bold text-dark">6. Third-Party Sharing</h5>
                        <p class="text-muted leading-relaxed mb-0">
                            We do not sell, rent, trade, or distribute your personal information to third-party marketing companies, advertisers, or any external organizations.
                        </p>
                    </section>

                    <hr class="my-4 text-muted opacity-25">

                    <section class="mb-4">
                        <h5 class="fw-bold text-dark">7. Your Rights & Choices</h5>
                        <p class="text-muted mb-2">
                            As a user of ChronoScent Elite, you maintain control over your personal data:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-2"><strong>Access & Review:</strong> View your account data, wishlist, and order history anytime from your profile.</li>
                            <li class="mb-2"><strong>Edit Details:</strong> Update your name, phone number, or delivery address directly in your profile.</li>
                            <li class="mb-0"><strong>Account Deletion:</strong> Request removal of your account and personal details by contacting us.</li>
                        </ul>
                    </section>

                    <hr class="my-4 text-muted opacity-25">

                    <section class="mb-0">
                        <h5 class="fw-bold text-dark">8. Contact Information</h5>
                        <p class="text-muted mb-2">
                            If you have any questions or concerns regarding this Privacy Policy, please reach out to us:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-1"><strong>Email:</strong> hello@chronoscentelite.com</li>
                            <li class="mb-1"><strong>Location:</strong> Surat, Gujarat, India</li>
                            <li class="mb-0"><strong>Contact Page:</strong> <a href="contact.php" class="text-decoration-none" style="color: var(--primary-theme);">Visit Contact Us</a></li>
                        </ul>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
