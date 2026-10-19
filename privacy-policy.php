<?php
require_once 'includes/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-house-door me-1"></i>Home</a></li>
            <li class="breadcrumb-item active fw-semibold text-theme" aria-current="page">Privacy Policy</li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="text-center mb-5">
        <span class="badge bg-light text-theme border px-3 py-2 rounded-pill fw-semibold mb-2">
            <i class="bi bi-shield-check me-1"></i> Transparency & Trust
        </span>
        <h1 class="display-5 fw-bold mb-3" style="color: var(--primary-theme);">Privacy Policy</h1>
        <p class="text-muted mx-auto" style="max-width: 680px; font-size: 1.05rem;">
            At <strong>ChronoScent Elite</strong>, we value your trust. This Privacy Policy clearly outlines what personal data we collect, how it is used, and how we keep it safe while you explore our luxury watches and perfumes.
        </p>
        <div class="text-muted small">
            <i class="bi bi-calendar3 me-1"></i> Last Updated: October 2026 &nbsp;|&nbsp; 
            <i class="bi bi-mortarboard me-1"></i> Academic Student Project
        </div>
    </div>

    <!-- Student Academic Project Notice -->
    <div class="card border-0 shadow-sm mb-4" style="background-color: #f0f7fa; border-left: 4px solid var(--primary-theme) !important;">
        <div class="card-body p-4 d-flex align-items-start">
            <div class="me-3 p-2 rounded-circle bg-white shadow-sm text-theme">
                <i class="bi bi-info-circle-fill fs-4" style="color: var(--primary-theme);"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1" style="color: #1a4a75;">Student Project Disclaimer</h6>
                <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                    This website has been built as a student academic project demonstrating modern PHP e-commerce development. All customer registrations, orders, and inquiries are for demonstrative educational evaluation. We handle all entered details with utmost respect and standard security practices.
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Navigation / Table of Contents (Sticky on desktop) -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-theme">
                        <i class="bi bi-list-nested me-2"></i>Quick Navigation
                    </h5>
                    <div class="list-group list-group-flush small">
                        <a href="#overview" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            1. Overview & Purpose
                        </a>
                        <a href="#collection" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            2. Information We Collect
                        </a>
                        <a href="#usage" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            3. How We Use Information
                        </a>
                        <a href="#security" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            4. Data Security & Storage
                        </a>
                        <a href="#cookies" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            5. Cookies & Sessions
                        </a>
                        <a href="#sharing" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            6. Third-Party Sharing
                        </a>
                        <a href="#rights" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            7. Your Rights & Choices
                        </a>
                        <a href="#contact" class="list-group-item list-group-item-action border-0 px-0 py-2 text-muted">
                            8. Contact Information
                        </a>
                    </div>

                    <hr class="my-3 text-muted">
                    <div class="p-3 bg-light rounded text-center">
                        <p class="small text-muted mb-2">Have a question regarding this policy?</p>
                        <a href="contact.php" class="btn btn-sm btn-outline-theme w-100">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <!-- Section 1 -->
                    <div id="overview" class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            <h4 class="fw-bold mb-0">1. Overview & Purpose</h4>
                        </div>
                        <p class="text-muted leading-relaxed">
                            ChronoScent Elite operates an online retail catalog specializing in premium timepieces and fragrances. We are committed to maintaining the privacy, confidentiality, and integrity of every visitor and customer who interacts with our portal.
                        </p>
                        <p class="text-muted leading-relaxed mb-0">
                            This Privacy Policy outlines how your personal information is collected, processed, and safeguarded in accordance with fundamental web privacy principles.
                        </p>
                    </div>

                    <hr class="my-4">

                    <!-- Section 2 -->
                    <div id="collection" class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-person-lines-fill"></i>
                            </span>
                            <h4 class="fw-bold mb-0">2. Information We Collect</h4>
                        </div>
                        <p class="text-muted">
                            We only collect information that is strictly necessary to provide an authentic e-commerce shopping experience:
                        </p>
                        <ul class="text-muted mb-3">
                            <li class="mb-2">
                                <strong>Personal Details:</strong> Name, email address, phone number, and delivery address provided during registration or checkout.
                            </li>
                            <li class="mb-2">
                                <strong>Account Credentials:</strong> Your username and securely hashed password for authentication.
                            </li>
                            <li class="mb-2">
                                <strong>Shopping Activity:</strong> Cart contents, wishlist preferences, order history, and generated invoices.
                            </li>
                            <li class="mb-2">
                                <strong>Contact & Feedback:</strong> Any questions, inquiries, or feedback submitted through our contact forms or product reviews.
                            </li>
                        </ul>
                        <div class="alert alert-light border small text-muted mb-0">
                            <i class="bi bi-credit-card me-1 text-theme"></i>
                            <strong>Payment Information:</strong> For this project, payments are simulated (e.g. Cash on Delivery or test gateway). We do <em>not</em> store sensitive credit card numbers or banking PINs.
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Section 3 -->
                    <div id="usage" class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-gear-fill"></i>
                            </span>
                            <h4 class="fw-bold mb-0">3. How We Use Your Information</h4>
                        </div>
                        <p class="text-muted">
                            Your collected information is used solely for legitimate operational purposes:
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-cart-check text-theme me-2"></i>Order Processing</h6>
                                    <p class="small text-muted mb-0">To confirm your orders, update delivery statuses, and generate official printable invoices.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-person-circle text-theme me-2"></i>Account Management</h6>
                                    <p class="small text-muted mb-0">To allow you to log in, view past purchases, manage your wishlist, and update personal profile details.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-chat-dots text-theme me-2"></i>Customer Support</h6>
                                    <p class="small text-muted mb-0">To respond promptly to questions, technical issues, or product inquiries submitted via our contact page.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded h-100">
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-sliders text-theme me-2"></i>Platform Experience</h6>
                                    <p class="small text-muted mb-0">To keep items in your cart active and personalize your browsing flow across sessions.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Section 4 -->
                    <div id="security" class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <h4 class="fw-bold mb-0">4. Data Security & Storage</h4>
                        </div>
                        <p class="text-muted leading-relaxed">
                            We take website security seriously and implement foundational web development security practices:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-2">
                                <strong>Password Encryption:</strong> User passwords are never saved in plain text. They are hashed using PHP's robust <code>password_hash()</code> standard with secure bcrypt algorithms.
                            </li>
                            <li class="mb-2">
                                <strong>SQL Injection Defense:</strong> All database operations utilize PDO prepared statements with parameterized queries to prevent SQL injection vulnerabilities.
                            </li>
                            <li class="mb-2">
                                <strong>Role-Based Access:</strong> Sensitive administrative areas and orders are protected by session verification checks.
                            </li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    <!-- Section 5 -->
                    <div id="cookies" class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-cookie"></i>
                            </span>
                            <h4 class="fw-bold mb-0">5. Cookies & Sessions</h4>
                        </div>
                        <p class="text-muted leading-relaxed">
                            ChronoScent Elite uses standard PHP session cookies (<code>PHPSESSID</code>) strictly for website functionality:
                        </p>
                        <div class="p-3 border rounded bg-light">
                            <ul class="text-muted mb-0 small">
                                <li class="mb-1">Keeping you logged into your customer or admin account as you browse pages.</li>
                                <li class="mb-1">Retaining items in your shopping cart before and during checkout.</li>
                                <li><strong>No intrusive tracking:</strong> We do not deploy third-party advertising or cross-site tracking cookies.</li>
                            </ul>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Section 6 -->
                    <div id="sharing" class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-share-fill"></i>
                            </span>
                            <h4 class="fw-bold mb-0">6. Third-Party Sharing</h4>
                        </div>
                        <p class="text-muted leading-relaxed mb-0">
                            We respect your privacy and follow an absolute non-sharing standard:
                            <strong>We do not sell, rent, trade, or distribute your personal information to third-party marketing companies, advertisers, or external organizations.</strong>
                        </p>
                    </div>

                    <hr class="my-4">

                    <!-- Section 7 -->
                    <div id="rights" class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-check2-circle"></i>
                            </span>
                            <h4 class="fw-bold mb-0">7. Your Rights & Choices</h4>
                        </div>
                        <p class="text-muted">
                            As a user of ChronoScent Elite, you maintain complete authority over your personal information:
                        </p>
                        <ul class="text-muted mb-0">
                            <li class="mb-2"><strong>Access & Review:</strong> View your account information, wishlist, and past orders at any time from your profile.</li>
                            <li class="mb-2"><strong>Edit Details:</strong> Update your name, phone number, or delivery address directly.</li>
                            <li class="mb-2"><strong>Account Removal:</strong> Request deletion of your test account and associated demo orders by contacting our team.</li>
                        </ul>
                    </div>

                    <hr class="my-4">

                    <!-- Section 8 -->
                    <div id="contact" class="mb-2">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-circle p-2 bg-light text-theme me-2 fs-6">
                                <i class="bi bi-envelope-paper-fill"></i>
                            </span>
                            <h4 class="fw-bold mb-0">8. Contact Information</h4>
                        </div>
                        <p class="text-muted">
                            If you have questions, feedback, or concerns regarding this Privacy Policy or how your data is handled, feel free to reach out to us:
                        </p>
                        
                        <div class="card border bg-light">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-envelope fs-4 text-theme me-3"></i>
                                            <div>
                                                <small class="text-muted d-block">Email Support</small>
                                                <strong>hello@chronoscentelite.com</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt fs-4 text-theme me-3"></i>
                                            <div>
                                                <small class="text-muted d-block">Location</small>
                                                <strong>Surat, Gujarat, India</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-top text-center">
                                    <a href="contact.php" class="btn btn-theme btn-sm px-4">
                                        <i class="bi bi-chat-dots me-1"></i> Go to Contact Page
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
