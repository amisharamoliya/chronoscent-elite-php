<!-- Featured Products -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold" style="color: #2c3e50; font-family: 'Playfair Display', serif;">Featured Products</h2>
        
        <div class="row g-4">
            <?php
            // Using the custom images provided by the user
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
                <div class="card h-100 border-0 bg-transparent" style="transition: all 0.3s ease; border-radius: 8px;">
                    <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; text-align: center;">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($item['name']) ?>" style="height: 220px; object-fit: contain;">
                    </div>
                    <div class="card-body text-center px-0 mt-2">
                        <h5 class="card-title text-truncate mb-1" style="font-family: 'Playfair Display', serif; color: #2c3e50; font-weight: 600;"><?= htmlspecialchars($item['name']) ?></h5>
                        <p class="card-text text-theme fw-bold mb-3" style="font-size: 1.1rem;">$<?= number_format($item['price'], 2) ?></p>
                        <form action="cart.php" method="POST" class="w-100 px-3" data-ajax="false">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-outline-theme w-100 rounded-pill" style="font-size: 0.85rem; letter-spacing: 1px;">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
