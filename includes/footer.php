</main>
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h4 class="footer-heading fw-bold">ChronoScent Elite</h4>
                <p>Your ultimate destination for luxury watches and exquisite perfumes. Experience timeless elegance with every purchase.</p>
                <div class="d-flex gap-3 fs-5">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-twitter"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h5 class="text-white mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="watches.php">Watches</a></li>
                    <li><a href="perfumes.php">Perfumes</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-white mb-3">Customer Service</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Returns & Exchanges</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h5 class="text-white mb-3">Contact Info</h5>
                <ul class="list-unstyled text-muted">
                    <li><i class="bi bi-geo-alt text-theme me-2"></i> 123 Luxury Ave, NY 10001</li>
                    <li><i class="bi bi-telephone text-theme me-2"></i> +1 234 567 8900</li>
                    <li><i class="bi bi-envelope text-theme me-2"></i> info@chronoscentelite.com</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary mt-4 mb-4">
        <div class="text-center text-muted">
            <p class="mb-0">&copy; 2026 ChronoScent Elite. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Use Event Delegation to absolutely guarantee we catch form submissions
document.addEventListener('submit', function(e) {
    
    // Check if the form being submitted is cart or wishlist
    const form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    
    // Skip if form explicitly disables ajax
    if (form.getAttribute('data-ajax') === 'false') return;
    
    const actionUrl = form.getAttribute('action') || '';
    if (!actionUrl.includes('cart.php') && !actionUrl.includes('wishlist.php')) {
        return; // Not our forms, let them submit normally
    }

    // Get the action type (add or remove)
    const actionInput = form.querySelector('input[name="action"]');
    if (!actionInput || (actionInput.value !== 'add' && actionInput.value !== 'remove')) {
        return;
    }

    // Don't intercept if we are actively viewing the cart or wishlist page
    if (window.location.pathname.includes('cart.php') || window.location.pathname.includes('wishlist.php')) {
        return;
    }

    // STOP the page from reloading!
    e.preventDefault();
    
    // Package up the form data
    const formData = new FormData(form);
    formData.append('is_ajax', '1'); 
    
    // Visual feedback for Add to Cart immediately
    const btn = form.querySelector('button[type="submit"]');
    let originalBtnHTML = '';
    if (btn && actionUrl.includes('cart.php')) {
        originalBtnHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';
        btn.disabled = true;
    }

    // Send the data
    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            console.error("HTTP Status:", response.status);
        }
        return response.text();
    }) // Read as raw text first for debugging!
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
            
            // 1. Update Cart
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
                
                // Success feedback reset
                if (btn && actionUrl.includes('cart.php')) {
                    btn.innerHTML = originalBtnHTML;
                    btn.disabled = false;
                }
            }
            
            // 2. Update Wishlist
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
