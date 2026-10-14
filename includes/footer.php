</main>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="mb-3">
                    <h3 class="footer-logo-title">ChronoScent</h3>
                    <span class="footer-logo-sub">ELITE</span>
                </div>
                <p class="footer-desc mb-4">
                    Discover luxury watches and premium fragrances crafted for those who appreciate timeless elegance.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="footer-social-btn" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="footer-social-btn" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social-btn" aria-label="X (Twitter)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
                        </svg>
                    </a>
                    <a href="#" class="footer-social-btn" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4 col-6">
                <h5 class="footer-col-title">Shop</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="watches.php">Watches</a></li>
                    <li><a href="perfumes.php">Fragrances</a></li>
                    <li><a href="watches.php">New Arrivals</a></li>
                    <li><a href="perfumes.php">Collections</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4 col-6">
                <h5 class="footer-col-title">Company</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="footer-col-title">Contact</h5>
                <ul class="list-unstyled">
                    <li class="footer-contact-item">
                        <i class="bi bi-geo-alt"></i>
                        <span>Surat, Gujarat, India</span>
                    </li>
                    <li class="footer-contact-item">
                        <i class="bi bi-envelope"></i>
                        <span>hello@chronoscentelite.com</span>
                    </li>
                    <li class="footer-contact-item">
                        <i class="bi bi-telephone"></i>
                        <span>+91 1xxxxxxxxx9</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="mt-4 mb-4" style="border-color: rgba(0, 0, 0, 0.08);">

        <div class="text-center py-2">
            <p class="mb-1 text-muted" style="font-size: 0.88rem;">&copy; 2026 ChronoScent Elite. All Rights Reserved.</p>
            <p class="mb-0 text-muted" style="font-size: 0.8rem; opacity: 0.75;">Crafted with precision for luxury enthusiasts.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('submit', function(e) {

    const form = e.target;
    if (!form || form.tagName !== 'FORM') return;

    if (form.getAttribute('data-ajax') === 'false') return;

    const actionUrl = form.getAttribute('action') || '';
    if (!actionUrl.includes('cart.php') && !actionUrl.includes('wishlist.php')) {
        return;
    }

    const actionInput = form.querySelector('input[name="action"]');
    if (!actionInput || (actionInput.value !== 'add' && actionInput.value !== 'remove')) {
        return;
    }

    if (window.location.pathname.includes('cart.php') || window.location.pathname.includes('wishlist.php')) {
        return;
    }

    e.preventDefault();

    const formData = new FormData(form);
    formData.append('is_ajax', '1');

    const btn = form.querySelector('button[type="submit"]');
    let originalBtnHTML = '';
    if (btn && actionUrl.includes('cart.php')) {
        originalBtnHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';
        btn.disabled = true;
    }

    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            console.error("HTTP Status:", response.status);
        }
        return response.text();
    })
    .then(rawText => {
        let data;
        try {
            data = JSON.parse(rawText);
        } catch (e) {
            console.error("Invalid JSON:", rawText);
            throw e;
        }

        if (data.status === 'error' && data.message === 'not_logged_in') {
            window.location.href = 'login.php';
            return;
        }

        if (data.status === 'success') {

            if (data.cart_count !== undefined) {
                let cartLink = document.querySelector('a[href="cart.php"]');
                if (cartLink) {
                    let cartBadge = cartLink.querySelector('.badge');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    } else if (data.cart_count > 0) {
                        cartLink.innerHTML += '<span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">' + data.cart_count + '</span>';
                    }
                }

                if (btn && actionUrl.includes('cart.php')) {
                    btn.innerHTML = originalBtnHTML;
                    btn.disabled = false;
                }
            }

            if (data.wishlist_count !== undefined) {
                let wlLink = document.querySelector('a[href="wishlist.php"]');
                if (wlLink) {
                    let wlBadge = wlLink.querySelector('.badge');
                    if (wlBadge) {
                        wlBadge.textContent = data.wishlist_count;
                        if (data.wishlist_count == 0) wlBadge.remove();
                    } else if (data.wishlist_count > 0) {
                        wlLink.innerHTML += '<span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">' + data.wishlist_count + '</span>';
                    }
                }

                const icon = form.querySelector('i');
                const formBtn = form.querySelector('button');

                if (actionInput.value === 'add') {
                    if(icon) {
                        icon.className = 'bi bi-heart-fill';
                        icon.style.color = '#dc3545';
                    }
                    if(formBtn && formBtn.classList.contains('btn-outline-danger')) {
                        formBtn.className = 'btn btn-danger';
                    }
                    actionInput.value = 'remove';
                } else {
                    if(icon) {
                        icon.className = 'bi bi-heart';
                        icon.style.color = '#666';
                    }
                    if(formBtn && formBtn.classList.contains('btn-danger')) {
                        formBtn.className = 'btn btn-outline-danger';
                    }
                    actionInput.value = 'add';
                }
            }
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        if (btn && originalBtnHTML !== '') {
            btn.innerHTML = originalBtnHTML;
            btn.disabled = false;
        }
    });
});
</script>
</body>
</html>
