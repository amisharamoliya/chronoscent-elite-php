<?php
require_once 'includes/header.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];

if ($q !== '') {
    $searchCategory = $q;
    if (strtolower($q) === 'watches' || strtolower($q) === 'watch') {
        $searchCategory = 'Watch';
    } elseif (strtolower($q) === 'perfumes' || strtolower($q) === 'perfume') {
        $searchCategory = 'Perfume';
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR brand LIKE ? OR category LIKE ?");
    $searchTerm = "%$q%";
    $categoryTerm = "%$searchCategory%";
    $stmt->execute([$searchTerm, $searchTerm, $categoryTerm]);
    $products = $stmt->fetchAll();
}
?>


<div class="container py-5 min-vh-100">
    <h1 class="fw-bold mb-4" style="color: var(--primary-theme);">Search Results</h1>
    <p class="text-muted mb-4">Showing results for: <strong><?php echo htmlspecialchars($q); ?></strong></p>

    <div class="row">
        <?php 
        if (count($products) > 0) {
            foreach($products as $p) echo renderProductCard($p); 
        } else {
            echo "<div class='col-12 text-center py-5'>
                    <i class='bi bi-search display-1 text-muted mb-3 d-block'></i>
                    <h4>No products found matching your search.</h4>
                    <a href='index.php' class='btn btn-outline-theme mt-3'>Return Home</a>
                  </div>";
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
