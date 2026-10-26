<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

if (!function_exists('resolveBrandImage')) {
    function resolveBrandImage($brand) {
        $logo = $brand['logo'] ?? '';
        if (!empty($logo)) {
            $cleanPath = ltrim($logo, '/');
            if (file_exists(__DIR__ . '/../' . $cleanPath)) {
                return $cleanPath;
            }
        }
        // Check fallback by name (e.g. rolex.jpg, dior.jpg, casio.jpg)
        $cleanedName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $brand['name']));
        $candidate = 'assets/images/brands/' . $cleanedName . '.jpg';
        if (file_exists(__DIR__ . '/../' . $candidate)) {
            return $candidate;
        }
        // Direct file property fallback
        if (!empty($brand['file'])) {
            $candidate2 = 'assets/images/brands/' . $brand['file'];
            if (file_exists(__DIR__ . '/../' . $candidate2)) {
                return $candidate2;
            }
        }
        return '';
    }
}

// Fetch dynamic Watch Brands from DB
$watchBrands = $pdo->query("SELECT * FROM brands WHERE category IN ('Watch', 'Both') ORDER BY id ASC")->fetchAll();
if (empty($watchBrands)) {
    $watchBrands = [
        ['name' => 'Rolex', 'origin' => 'GENEVA, SWITZERLAND', 'file' => 'rolex.jpg'],
        ['name' => 'Omega', 'origin' => 'BIEL, SWITZERLAND', 'file' => 'omega.jpg'],
        ['name' => 'Cartier', 'origin' => 'PARIS, FRANCE', 'file' => 'cartier.jpg'],
        ['name' => 'Gucci', 'origin' => 'FLORENCE, ITALY', 'file' => 'gucci.jpg'],
        ['name' => 'Hublot', 'origin' => 'NYON, SWITZERLAND', 'file' => 'hublot.jpg']
    ];
}

// Fetch dynamic Fragrance Brands from DB
$fragranceBrands = $pdo->query("SELECT * FROM brands WHERE category IN ('Perfume', 'Both') ORDER BY id ASC")->fetchAll();
if (empty($fragranceBrands)) {
    $fragranceBrands = [
        ['name' => 'Dior', 'origin' => 'PARIS, FRANCE', 'file' => 'dior.jpg'],
        ['name' => 'Chanel', 'origin' => 'PARIS, FRANCE', 'file' => 'chanel.jpg'],
        ['name' => 'Yves Saint Laurent', 'origin' => 'PARIS, FRANCE', 'file' => 'ysl.jpg'],
        ['name' => 'Versace', 'origin' => 'MILAN, ITALY', 'file' => 'versace.jpg'],
        ['name' => 'Creed', 'origin' => 'PARIS, FRANCE', 'file' => 'creed.jpg']
    ];
}
?>

<section class="py-5 bg-white featured-brands-section">
    <div class="container text-center">

        <!-- Watch Brands Header with Scroll Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0 text-start" style="color: #2b4968; font-family: 'Playfair Display', Georgia, serif; font-size: 1.85rem;">Luxury Watch Brands</h3>
            <div class="brand-scroll-controls d-flex gap-2">
                <button type="button" class="brand-nav-btn" data-target="watchBrandsTrack" data-dir="prev" aria-label="Previous Watch Brands" title="Previous">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="brand-nav-btn" data-target="watchBrandsTrack" data-dir="next" aria-label="Next Watch Brands" title="Next">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Watch Brands Horizontal Scroll Track -->
        <div class="brands-scroll-wrapper">
            <div class="brands-scroll-container" id="watchBrandsTrack">
                <?php foreach ($watchBrands as $brand): 
                    $bname = $brand['name'];
                    $location = !empty($brand['origin']) ? $brand['origin'] : (!empty($brand['location']) ? $brand['location'] : 'Europe');
                    $img_src = resolveBrandImage($brand);
                ?>
                <div class="brand-scroll-item">
                    <a href="watches.php?brand=<?= urlencode($bname) ?>" class="text-decoration-none d-block h-100">
                        <div class="card h-100 brand-card">
                            <div class="brand-img-wrapper">
                                <?php if (!empty($img_src)): ?>
                                    <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($bname) ?>" class="img-fluid brand-img">
                                <?php else: ?>
                                    <div class="brand-monogram">
                                        <span class="brand-monogram-text"><?= htmlspecialchars(strtoupper(substr($bname, 0, 2))) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="brand-info">
                                <h5 class="brand-name mb-1"><?= htmlspecialchars($bname) ?></h5>
                                <small class="brand-location"><?= htmlspecialchars(strtoupper($location)) ?></small>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="my-5">
            <a href="watches.php" class="btn-brand-explore">Explore All Watches &rarr;</a>
        </div>

        <!-- Fragrance Brands Header with Scroll Navigation -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-5 pt-3">
            <h3 class="fw-bold m-0 text-start" style="color: #2b4968; font-family: 'Playfair Display', Georgia, serif; font-size: 1.85rem;">Premium Fragrance Brands</h3>
            <div class="brand-scroll-controls d-flex gap-2">
                <button type="button" class="brand-nav-btn" data-target="fragranceBrandsTrack" data-dir="prev" aria-label="Previous Fragrance Brands" title="Previous">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="brand-nav-btn" data-target="fragranceBrandsTrack" data-dir="next" aria-label="Next Fragrance Brands" title="Next">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Fragrance Brands Horizontal Scroll Track -->
        <div class="brands-scroll-wrapper">
            <div class="brands-scroll-container" id="fragranceBrandsTrack">
                <?php foreach ($fragranceBrands as $brand): 
                    $bname = $brand['name'];
                    $location = !empty($brand['origin']) ? $brand['origin'] : (!empty($brand['location']) ? $brand['location'] : 'Europe');
                    $img_src = resolveBrandImage($brand);
                ?>
                <div class="brand-scroll-item">
                    <a href="perfumes.php?brand=<?= urlencode($bname) ?>" class="text-decoration-none d-block h-100">
                        <div class="card h-100 brand-card">
                            <div class="brand-img-wrapper">
                                <?php if (!empty($img_src)): ?>
                                    <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($bname) ?>" class="img-fluid brand-img">
                                <?php else: ?>
                                    <div class="brand-monogram">
                                        <span class="brand-monogram-text"><?= htmlspecialchars(strtoupper(substr($bname, 0, 2))) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="brand-info">
                                <h5 class="brand-name mb-1"><?= htmlspecialchars($bname) ?></h5>
                                <small class="brand-location"><?= htmlspecialchars(strtoupper($location)) ?></small>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="my-5">
            <a href="perfumes.php" class="btn-brand-explore">Explore All Fragrances &rarr;</a>
        </div>
    </div>
</section>

<link rel="stylesheet" href="assets/css/featured-brands.css?v=<?php echo time(); ?>">
<script src="assets/js/featured-brands.js?v=<?php echo time(); ?>"></script>
