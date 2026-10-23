<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$msg = '';
$err = '';

// 1. Handle Add New Brand
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['add_new_brand'])) {
    $name = trim($_POST['brand_name'] ?? '');
    $category = trim($_POST['category'] ?? 'Watch');
    $origin = trim($_POST['origin'] ?? 'Europe');
    $description = trim($_POST['description'] ?? '');
    $logo_path = 'assets/images/brands/' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . '.jpg';

    if (empty($name)) {
        $err = "Please enter a brand name.";
    } else {
        // Check if brand already exists
        $check = $pdo->prepare("SELECT id FROM brands WHERE name = ?");
        $check->execute([$name]);
        if ($check->fetch()) {
            $err = "A brand with the name '{$name}' already exists.";
        } else {
            // Handle logo file upload if provided
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['logo_file']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
                if (in_array($ext, $allowed)) {
                    $new_filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . '.' . $ext;
                    $target_dir = __DIR__ . '/../assets/images/brands/';
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
                        $logo_path = 'assets/images/brands/' . $new_filename;
                    }
                }
            }

            $stmt = $pdo->prepare("INSERT INTO brands (name, category, origin, description, logo) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $category, $origin, $description, $logo_path])) {
                $msg = "Luxury Brand '{$name}' added successfully to the directory!";
            } else {
                $err = "Failed to add brand to database.";
            }
        }
    }
}

// 2. Handle Edit / Update Brand Details
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['edit_brand'])) {
    $brand_id = (int)$_POST['brand_id'];
    $old_name = trim($_POST['old_name'] ?? '');
    $new_name = trim($_POST['brand_name'] ?? '');
    $category = trim($_POST['category'] ?? 'Watch');
    $origin = trim($_POST['origin'] ?? 'Europe');
    $description = trim($_POST['description'] ?? '');

    if (empty($new_name)) {
        $err = "Brand name cannot be empty.";
    } else {
        // Fetch current brand logo
        $curr = $pdo->prepare("SELECT logo FROM brands WHERE id = ?");
        $curr->execute([$brand_id]);
        $current_logo = $curr->fetchColumn() ?: '';

        $logo_path = $current_logo;
        // Handle new logo file upload if provided
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['logo_file']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            if (in_array($ext, $allowed)) {
                $new_filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $new_name)) . '_' . time() . '.' . $ext;
                $target_dir = __DIR__ . '/../assets/images/brands/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
                    $logo_path = 'assets/images/brands/' . $new_filename;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE brands SET name = ?, category = ?, origin = ?, description = ?, logo = ? WHERE id = ?");
        if ($stmt->execute([$new_name, $category, $origin, $description, $logo_path, $brand_id])) {
            // Also update product catalog brand name if renamed
            if (!empty($old_name) && $old_name !== $new_name) {
                $up_prod = $pdo->prepare("UPDATE products SET brand = ? WHERE brand = ?");
                $up_prod->execute([$new_name, $old_name]);
            }
            $msg = "Brand '{$new_name}' details updated successfully!";
        } else {
            $err = "Failed to update brand details.";
        }
    }
}

// 3. Handle Brand Deletion
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['delete_brand'])) {
    $brand_id = (int)$_POST['brand_id'];
    $b_stmt = $pdo->prepare("SELECT name FROM brands WHERE id = ?");
    $b_stmt->execute([$brand_id]);
    $brand_to_del = $b_stmt->fetchColumn();

    if ($brand_to_del) {
        $pdo->prepare("DELETE FROM brands WHERE id = ?")->execute([$brand_id]);
        $msg = "Brand '{$brand_to_del}' removed from directory.";
    }
}

// Query brands with inventory stats
$brands_query = "
    SELECT 
        b.id,
        b.name as brand,
        b.category,
        b.origin,
        b.description,
        b.logo,
        COUNT(p.id) as total_items,
        COALESCE(MIN(p.price), 0) as min_price,
        COALESCE(MAX(p.price), 0) as max_price,
        COALESCE(SUM(p.stock), 0) as total_stock
    FROM brands b
    LEFT JOIN products p ON b.name = p.brand
    GROUP BY b.id, b.name, b.category, b.origin, b.description, b.logo
    ORDER BY b.name ASC
";
$brands_list = $pdo->query($brands_query)->fetchAll();

$total_brands_count = count($brands_list);
$watch_brands_count = 0;
$perfume_brands_count = 0;
$total_catalog_items = 0;

foreach ($brands_list as $b) {
    if ($b['category'] == 'Watch') $watch_brands_count++;
    if ($b['category'] == 'Perfume') $perfume_brands_count++;
    $total_catalog_items += $b['total_items'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?php echo time(); ?>">
    <style>
        .brand-logo-preview {
            width: 52px;
            height: 52px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 4px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .brand-card-inner {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            padding: 14px;
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Wrapper -->
        <div class="admin-main-wrapper">
            <!-- Topbar -->
            <?php include 'topbar.php'; ?>

            <main class="admin-content">

                <!-- Alert Messages -->
                <?php if ($msg): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if ($err): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($err) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Top Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">TOTAL BRANDS</div>
                                <div class="stat-value"><?= $total_brands_count ?></div>
                                <div class="stat-subtitle">Registered Luxury Houses</div>
                            </div>
                            <div class="stat-icon-wrapper icon-blue">
                                <i class="bi bi-tag"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">WATCH HOUSES</div>
                                <div class="stat-value"><?= $watch_brands_count ?></div>
                                <div class="stat-subtitle">Swiss &amp; European Horology</div>
                            </div>
                            <div class="stat-icon-wrapper icon-orange">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">FRAGRANCE HOUSES</div>
                                <div class="stat-value"><?= $perfume_brands_count ?></div>
                                <div class="stat-subtitle">Haute Parfumeries</div>
                            </div>
                            <div class="stat-icon-wrapper icon-cyan">
                                <i class="bi bi-flower1"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">CATALOG PRODUCTS</div>
                                <div class="stat-value"><?= $total_catalog_items ?></div>
                                <div class="stat-subtitle">Associated in Inventory</div>
                            </div>
                            <div class="stat-icon-wrapper icon-green">
                                <i class="bi bi-bag-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brands Table Card -->
                <div class="purchases-card">
                    <div class="purchases-header">
                        <div>
                            <h5 class="purchases-title">Brand Directory</h5>
                            <p class="purchases-count"><?= $total_brands_count ?> luxury brands configured</p>
                        </div>
                        <div>
                            <!-- ADD NEW BRAND BUTTON -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewBrandModal" style="background-color: #1e3a5f; border-color: #1e3a5f;">
                                <i class="bi bi-plus-lg me-1"></i> Add New Brand
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="orders-table align-middle">
                            <thead>
                                <tr>
                                    <th>BRAND</th>
                                    <th>HEADQUARTERS</th>
                                    <th>CATEGORY</th>
                                    <th>DESCRIPTION / SPECIALTY</th>
                                    <th>PRODUCTS</th>
                                    <th>STOCK</th>
                                    <th>PRICE RANGE</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($brands_list as $b): 
                                    $bname = $b['brand'];
                                    $logo_file = $b['logo'] ?: ('assets/images/brands/' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $bname)) . '.jpg');
                                    $has_logo = file_exists(__DIR__ . '/../' . $logo_file);
                                    $cat_badge = ($b['category'] == 'Watch') 
                                        ? '<span class="badge bg-primary-subtle text-primary border px-2 py-1 rounded-pill">Watch</span>' 
                                        : '<span class="badge bg-info-subtle text-info border px-2 py-1 rounded-pill">Perfume</span>';
                                ?>
                                <tr>
                                    <!-- Brand House & Logo -->
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if ($has_logo): ?>
                                                <img src="../<?= htmlspecialchars($logo_file) ?>" alt="<?= htmlspecialchars($bname) ?>" class="brand-logo-preview">
                                            <?php else: ?>
                                                <div class="brand-logo-preview d-flex align-items-center justify-content-center fw-bold text-secondary fs-5">
                                                    <?= strtoupper(substr($bname, 0, 2)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($bname) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($b['origin'] ?: 'Europe') ?></small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Headquarters / Origin -->
                                    <td>
                                        <div class="text-secondary small fw-medium">
                                            <i class="bi bi-geo-alt me-1 text-muted"></i><?= htmlspecialchars($b['origin'] ?: 'Europe') ?>
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td>
                                        <?= $cat_badge ?>
                                    </td>

                                    <!-- Description / Specialty -->
                                    <td>
                                        <div class="text-muted small text-truncate" style="max-width: 260px;" title="<?= htmlspecialchars($b['description'] ?? '') ?>">
                                            <?= htmlspecialchars($b['description'] ?: 'Luxury Heritage Collection') ?>
                                        </div>
                                    </td>

                                    <!-- Items in Catalog -->
                                    <td>
                                        <span class="fw-bold text-dark"><?= $b['total_items'] ?></span>
                                        <small class="text-muted">items</small>
                                    </td>

                                    <!-- Stock -->
                                    <td>
                                        <?php if ($b['total_stock'] > 0): ?>
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill"><?= $b['total_stock'] ?> units</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-muted border px-2 py-1 rounded-pill">0 units</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Price Range -->
                                    <td>
                                        <?php if ($b['total_items'] > 0): ?>
                                            <span class="fw-semibold text-dark">₹<?= formatIndianNumber($b['min_price']) ?></span>
                                            <span class="text-muted small">&ndash;</span>
                                            <span class="fw-semibold text-dark">₹<?= formatIndianNumber($b['max_price']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">&mdash;</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <!-- View in Products -->
                                            <a href="products.php?search=<?= urlencode($bname) ?>" class="btn btn-sm btn-outline-secondary" title="View Products in Catalog">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <!-- Edit Brand Modal Trigger -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBrandModal<?= $b['id'] ?>" title="Edit Brand Details">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <!-- Boutique Link -->
                                            <?php $link = ($b['category'] == 'Watch') ? "../watches.php?brand=" . urlencode($bname) : "../perfumes.php?brand=" . urlencode($bname); ?>
                                            <a href="<?= $link ?>" target="_blank" class="btn btn-sm btn-outline-info" title="View in Boutique">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                            <!-- Delete Brand Form -->
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete brand <?= htmlspecialchars($bname) ?>?');">
                                                <input type="hidden" name="delete_brand" value="1">
                                                <input type="hidden" name="brand_id" value="<?= $b['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-action-delete" title="Delete Brand">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- EDIT BRAND DETAILS MODAL -->
                                        <div class="modal fade text-start" id="editBrandModal<?= $b['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Edit Brand Details: <?= htmlspecialchars($bname) ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="edit_brand" value="1">
                                                            <input type="hidden" name="brand_id" value="<?= $b['id'] ?>">
                                                            <input type="hidden" name="old_name" value="<?= htmlspecialchars($bname) ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Brand Name</label>
                                                                <input type="text" name="brand_name" class="form-control" value="<?= htmlspecialchars($bname) ?>" required>
                                                            </div>

                                                            <div class="row mb-3">
                                                                <div class="col">
                                                                    <label class="form-label fw-semibold">Department / Category</label>
                                                                    <select name="category" class="form-select">
                                                                        <option value="Watch" <?= $b['category'] == 'Watch' ? 'selected' : '' ?>>Watch</option>
                                                                        <option value="Perfume" <?= $b['category'] == 'Perfume' ? 'selected' : '' ?>>Perfume</option>
                                                                        <option value="Both" <?= $b['category'] == 'Both' ? 'selected' : '' ?>>Both</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col">
                                                                    <label class="form-label fw-semibold">Headquarters / Origin</label>
                                                                    <input type="text" name="origin" class="form-control" value="<?= htmlspecialchars($b['origin'] ?? '') ?>" placeholder="e.g. Geneva, Switzerland">
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Brand Description &amp; Heritage</label>
                                                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($b['description'] ?? '') ?></textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Change Brand Logo</label>
                                                                <input type="file" name="logo_file" class="form-control" accept="image/*">
                                                                <small class="text-muted">Upload PNG, JPG, or SVG logo. Leave empty to keep existing logo.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary" style="background-color: #1e3a5f; border-color: #1e3a5f;">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- ADD NEW BRAND MODAL -->
    <div class="modal fade" id="addNewBrandModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Luxury Brand</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="add_new_brand" value="1">

                        <!-- Brand Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" name="brand_name" class="form-control" placeholder="e.g. Patek Philippe, Tom Ford, Seiko" required>
                        </div>

                        <!-- Category & Origin Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="Watch">Watch</option>
                                    <option value="Perfume">Perfume</option>
                                    <option value="Both">Both (Watches &amp; Fragrances)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Headquarters / Origin</label>
                                <input type="text" name="origin" class="form-control" placeholder="e.g. Geneva, Switzerland">
                            </div>
                        </div>

                        <!-- Brand Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Brand Description &amp; Heritage</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Briefly describe the brand's horological or fragrance heritage, craft, and specialty..."></textarea>
                        </div>

                        <!-- Brand Logo Image Upload -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Brand Logo Image</label>
                            <input type="file" name="logo_file" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended: Square format (JPG/PNG) on white or transparent background.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #1e3a5f; border-color: #1e3a5f;">
                            <i class="bi bi-plus-lg me-1"></i> Add Brand
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
