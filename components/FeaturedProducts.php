<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-2 fw-bold" style="color: #2b4968; font-family: 'Playfair Display', Georgia, serif; font-size: 2.6rem;">Featured Products</h2>
            <p class="text-muted mx-auto mb-0" style="font-size: 1.05rem; color: #64748b;">
                Premium picks curated exclusively for you.
            </p>
        </div>

        <div class="row g-4">
            <?php

            $featuredItems = [
                [
                    'id' => 83,
                    'name' => 'Hublot Geneve',
                    'price' => 15500.00,
                    'image' => 'assets/images/featured products/Hublot geneve.jpg',
                    'link' => 'watches.php'
                ],
                [
                    'id' => 81,
                    'name' => 'Cartier Baignoire',
                    'price' => 12300.00,
                    'image' => 'assets/images/featured products/cartier Baignoreire.jpg',
                    'link' => 'watches.php'
                ],
                [
                    'id' => 108,
                    'name' => 'Chanel Chance',
                    'price' => 145.00,
                    'image' => 'assets/images/featured products/chanel chance.jpg',
                    'link' => 'perfumes.php'
                ],
                [
                    'id' => 121,
                    'name' => 'YSL Libre',
                    'price' => 160.00,
                    'image' => 'assets/images/featured products/ysl libre.jpg',
                    'link' => 'perfumes.php'
                ]
            ];
            foreach ($featuredItems as $item): ?>
            <div class="col-md-3">
                <div class="card h-100 border-0 featured-product-card">
                    <div style="background-color: #ffffff; padding: 20px; text-align: center; border-bottom: 1px solid rgba(114, 155, 168, 0.12);">
                        <a href="<?= htmlspecialchars($item['link']) ?>" class="d-block text-decoration-none">
                            <img src="<?= htmlspecialchars($item['image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($item['name']) ?>" style="height: 220px; object-fit: contain; transition: transform 0.3s ease;">
                        </a>
                    </div>
                    <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title text-truncate mb-1" style="font-family: 'Playfair Display', serif; color: #2c3e50; font-weight: 600;">
                                <a href="<?= htmlspecialchars($item['link']) ?>" class="text-decoration-none" style="color: inherit;"><?= htmlspecialchars($item['name']) ?></a>
                            </h5>
                            <p class="card-text text-theme fw-bold mb-3" style="font-size: 1.15rem;">₹<?= formatIndianNumber($item['price']) ?></p>
                        </div>
                        <form action="cart.php" method="POST" class="w-100" data-ajax="false">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-outline-theme w-100 rounded-pill shadow-sm" style="font-size: 0.85rem; letter-spacing: 1px;">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
