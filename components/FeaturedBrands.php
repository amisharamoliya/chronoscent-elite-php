<!-- Featured Brands -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="mb-2 fw-bold" style="color: #2c3e50; font-family: 'Playfair Display', serif;">Featured Luxury Brands</h2>
        <p class="text-muted mb-5">Explore the world's finest watchmakers and fragrance houses.</p>

        <!-- Luxury Watch Brands -->
        <div class="text-start mb-4">
            <h4 style="color: #2c3e50; font-family: 'Playfair Display', serif;">Luxury Watch Brands</h4>
        </div>
        
        <div class="row align-items-center justify-content-center g-4 mb-5">
            <?php
            $watchBrands = [
                ['file' => 'rolex.jpg', 'name' => 'Rolex', 'location' => 'GENEVA, SWITZERLAND'],
                ['file' => 'omega.jpg', 'name' => 'Omega', 'location' => 'BIEL, SWITZERLAND'],
                ['file' => 'cartier.jpg', 'name' => 'Cartier', 'location' => 'PARIS, FRANCE'],
                ['file' => 'gucci.jpg', 'name' => 'Gucci', 'location' => 'FLORENCE, ITALY'],
                ['file' => 'hublot.jpg', 'name' => 'Hublot', 'location' => 'NYON, SWITZERLAND']
            ];
            foreach ($watchBrands as $brand) {
                echo '
                <div class="col-6 col-md-4 col-lg-2 m-2">
                    <div class="card h-100 border-0 bg-transparent brand-card" style="border-radius: 8px; border: 1px solid #e2e8f0 !important; transition: all 0.3s ease;">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center py-4">
                            <img src="assets/images/brands/' . $brand['file'] . '" alt="' . $brand['name'] . '" class="img-fluid mb-3" style="max-height: 50px; object-fit: contain;">
                            <h6 class="mb-1" style="color: #2c3e50; font-family: \'Playfair Display\', serif;">' . $brand['name'] . '</h6>
                            <small class="text-muted" style="font-size: 0.65rem; letter-spacing: 1px; text-transform: uppercase;">' . $brand['location'] . '</small>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
        
        <div class="mb-5">
            <a href="watches.php" class="btn btn-outline-dark px-4 py-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">Explore All Watches &rarr;</a>
        </div>

        <!-- Premium Fragrance Brands -->
        <div class="text-start mb-4 mt-5">
            <h4 style="color: #2c3e50; font-family: 'Playfair Display', serif;">Premium Fragrance Brands</h4>
        </div>
        
        <div class="row align-items-center justify-content-center g-4 mb-5">
            <?php
            $fragranceBrands = [
                ['file' => 'dior.jpg', 'name' => 'Dior', 'location' => 'PARIS, FRANCE'],
                ['file' => 'chanel.jpg', 'name' => 'Chanel', 'location' => 'PARIS, FRANCE'],
                ['file' => 'ysl.jpg', 'name' => 'YSL', 'location' => 'PARIS, FRANCE'],
                ['file' => 'versace.jpg', 'name' => 'Versace', 'location' => 'MILAN, ITALY'],
                ['file' => 'creed.jpg', 'name' => 'Creed', 'location' => 'PARIS, FRANCE']
            ];
            foreach ($fragranceBrands as $brand) {
                echo '
                <div class="col-6 col-md-4 col-lg-2 m-2">
                    <div class="card h-100 border-0 bg-transparent brand-card" style="border-radius: 8px; border: 1px solid #e2e8f0 !important; transition: all 0.3s ease;">
                        <div class="card-body text-center d-flex flex-column justify-content-center align-items-center py-4">
                            <img src="assets/images/brands/' . $brand['file'] . '" alt="' . $brand['name'] . '" class="img-fluid mb-3" style="max-height: 50px; object-fit: contain;">
                            <h6 class="mb-1" style="color: #2c3e50; font-family: \'Playfair Display\', serif;">' . $brand['name'] . '</h6>
                            <small class="text-muted" style="font-size: 0.65rem; letter-spacing: 1px; text-transform: uppercase;">' . $brand['location'] . '</small>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
        
        <div class="mb-5">
            <a href="perfumes.php" class="btn btn-outline-dark px-4 py-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.85rem;">Explore All Fragrances &rarr;</a>
        </div>
    </div>
</section>

<link rel="stylesheet" href="assets/css/featured-brands.css">
