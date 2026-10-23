<section class="py-5 bg-white featured-brands-section">
    <div class="container text-center">

        <div class="text-start mb-4">
            <h3 class="fw-bold m-0" style="color: #2b4968; font-family: 'Playfair Display', Georgia, serif; font-size: 1.85rem;">Luxury Watch Brands</h3>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4 mb-4 justify-content-center">
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
                <div class="col">
                    <a href="watches.php?brand=' . urlencode($brand['name']) . '" class="text-decoration-none">
                        <div class="card h-100 brand-card">
                            <div class="brand-img-wrapper">
                                <img src="assets/images/brands/' . $brand['file'] . '" alt="' . $brand['name'] . '" class="img-fluid brand-img">
                            </div>
                            <div class="brand-info">
                                <h5 class="brand-name mb-1">' . $brand['name'] . '</h5>
                                <small class="brand-location">' . $brand['location'] . '</small>
                            </div>
                        </div>
                    </a>
                </div>';
            }
            ?>
        </div>

        <div class="my-5">
            <a href="watches.php" class="btn-brand-explore">Explore All Watches &rarr;</a>
        </div>

        <div class="text-start mb-4 mt-5 pt-3">
            <h3 class="fw-bold m-0" style="color: #2b4968; font-family: 'Playfair Display', Georgia, serif; font-size: 1.85rem;">Premium Fragrance Brands</h3>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4 mb-4 justify-content-center">
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
                <div class="col">
                    <a href="perfumes.php?brand=' . urlencode($brand['name']) . '" class="text-decoration-none">
                        <div class="card h-100 brand-card">
                            <div class="brand-img-wrapper">
                                <img src="assets/images/brands/' . $brand['file'] . '" alt="' . $brand['name'] . '" class="img-fluid brand-img">
                            </div>
                            <div class="brand-info">
                                <h5 class="brand-name mb-1">' . $brand['name'] . '</h5>
                                <small class="brand-location">' . $brand['location'] . '</small>
                            </div>
                        </div>
                    </a>
                </div>';
            }
            ?>
        </div>

        <div class="my-5">
            <a href="perfumes.php" class="btn-brand-explore">Explore All Fragrances &rarr;</a>
        </div>
    </div>
</section>

<link rel="stylesheet" href="assets/css/featured-brands.css?v=<?php echo time(); ?>">
