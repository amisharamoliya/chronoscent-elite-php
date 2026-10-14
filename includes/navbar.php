<?php
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}

$wishlist_count = 0;
if (isset($_SESSION['user_id']) && isset($pdo)) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $wishlist_count = $stmt->fetchColumn();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white navbar-custom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex flex-column align-items-start lh-1 text-decoration-none" href="index.php" style="padding-top: 0.25rem; text-transform: none;">
        <span class="fw-bold fs-4" style="color: #1a4a75;">ChronoScent</span>
        <span class="fw-normal" style="font-size: 0.65rem; letter-spacing: 0.45em; color: #6c757d; margin-top: 4px; margin-left: 2px;">ELITE</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="watches.php">Watches</a></li>
        <li class="nav-item"><a class="nav-link" href="perfumes.php">Perfumes</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
      <form class="d-flex me-3 position-relative" action="search.php" method="GET">
        <div class="input-group">
            <input class="form-control" type="search" name="q" id="searchInput" placeholder="Search..." aria-label="Search" autocomplete="off" required>
            <button class="btn btn-outline-theme" type="submit"><i class="bi bi-search"></i></button>
        </div>
        <div id="searchResults" class="position-absolute shadow bg-white w-100 rounded mt-1" style="z-index: 1050; display: none; top: 100%; max-height: 300px; overflow-y: auto;"></div>
      </form>
      <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <a class="nav-link position-relative" href="wishlist.php">
                    <i class="bi bi-heart fs-5"></i>
                    <?php if ($wishlist_count > 0): ?>
                        <span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            <?php echo $wishlist_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link position-relative" href="cart.php">
                <i class="bi bi-bag fs-5"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-10 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        <?php echo $cart_count; ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                </ul>
            </li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-person fs-5"></i></a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        fetch('api/search.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const price = item.discount_price > 0 ? item.discount_price : item.price;
                        const div = document.createElement('a');
                        div.href = 'product-details.php?id=' + item.id;
                        div.className = 'd-flex align-items-center text-decoration-none text-dark p-2 border-bottom';
                        div.innerHTML = `
                            <img src="${item.image}" alt="${item.name}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <div class="flex-grow-1">
                                <div class="fw-bold small lh-1">${item.name}</div>
                                <div class="text-theme small fw-bold mt-1">$${parseFloat(price).toFixed(2)}</div>
                            </div>
                        `;
                        div.onmouseover = function() { this.style.backgroundColor = '#f8f9fa'; };
                        div.onmouseout = function() { this.style.backgroundColor = 'transparent'; };
                        searchResults.appendChild(div);
                    });
                    searchResults.style.display = 'block';
                } else {
                    searchResults.innerHTML = '<div class="p-3 text-muted small text-center">No products found</div>';
                    searchResults.style.display = 'block';
                }
            })
            .catch(error => console.error('Error fetching search results:', error));
    });
});
</script>
