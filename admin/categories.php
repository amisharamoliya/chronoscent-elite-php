<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$msg = '';
$err = '';

// 1. Handle Add New Category
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['add_new_category'])) {
    $name = trim($_POST['category_name'] ?? '');
    $type = trim($_POST['category_type'] ?? 'General');
    $description = trim($_POST['description'] ?? '');
    $icon_input = trim($_POST['category_icon'] ?? '');
    $image_path = '';

    if (empty($name)) {
        $err = "Please enter a category name.";
    } else {
        // Check if category already exists
        $check = $pdo->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(?)");
        $check->execute([$name]);
        if ($check->fetch()) {
            $err = "A category with the name '{$name}' already exists.";
        } else {
            // Handle image file upload if provided
            if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['category_image']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
                if (in_array($ext, $allowed)) {
                    $new_filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . '_' . time() . '.' . $ext;
                    $target_dir = __DIR__ . '/../assets/images/categories/';
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
                        $image_path = 'assets/images/categories/' . $new_filename;
                    }
                }
            } else {
                // If default images exist for Watch or Perfume
                if ($type === 'Watch' && file_exists(__DIR__ . '/../assets/images/cat_watch.png')) {
                    $image_path = 'assets/images/cat_watch.png';
                } elseif ($type === 'Perfume' && file_exists(__DIR__ . '/../assets/images/cat_perfume.png')) {
                    $image_path = 'assets/images/cat_perfume.png';
                }
            }

            if (!empty($icon_input)) {
                $icon = $icon_input;
            } elseif (stripos($name, 'classic') !== false) {
                $icon = 'bi-award';
            } elseif (stripos($name, 'sport') !== false) {
                $icon = 'bi-speedometer2';
            } elseif (stripos($name, 'diver') !== false) {
                $icon = 'bi-water';
            } elseif (stripos($name, 'dress') !== false) {
                $icon = 'bi-suit-club';
            } elseif (stripos($name, 'modern') !== false || stripos($name, 'lux') !== false) {
                $icon = 'bi-gem';
            } elseif (stripos($name, 'sign') !== false) {
                $icon = 'bi-stars';
            } elseif ($type === 'Watch') {
                $icon = 'bi-clock-history';
            } elseif ($type === 'Perfume') {
                $icon = 'bi-flower1';
            } else {
                $icon = 'bi-folder';
            }

            $stmt = $pdo->prepare("INSERT INTO categories (name, type, description, image, icon) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $type, $description, $image_path, $icon])) {
                $msg = "Category '{$name}' added successfully to the catalog directory!";
            } else {
                $err = "Failed to add category to the database.";
            }
        }
    }
}

// 2. Handle Edit / Update Category Details
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['edit_category'])) {
    $cat_id = (int)$_POST['category_id'];
    $old_name = trim($_POST['old_name'] ?? '');
    $new_name = trim($_POST['category_name'] ?? '');
    $type = trim($_POST['category_type'] ?? 'General');
    $description = trim($_POST['description'] ?? '');
    $icon_input = trim($_POST['category_icon'] ?? '');

    if (empty($new_name)) {
        $err = "Category name cannot be empty.";
    } else {
        // Fetch current image and icon
        $curr = $pdo->prepare("SELECT image, icon FROM categories WHERE id = ?");
        $curr->execute([$cat_id]);
        $curr_row = $curr->fetch(PDO::FETCH_ASSOC);
        $image_path = $curr_row['image'] ?? '';
        $icon = $curr_row['icon'] ?? 'bi-folder';

        // Handle new image file upload if provided
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['category_image']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            if (in_array($ext, $allowed)) {
                $new_filename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $new_name)) . '_' . time() . '.' . $ext;
                $target_dir = __DIR__ . '/../assets/images/categories/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
                    $image_path = 'assets/images/categories/' . $new_filename;
                }
            }
        }

        if (!empty($icon_input)) {
            $icon = $icon_input;
        }

        $stmt = $pdo->prepare("UPDATE categories SET name = ?, type = ?, description = ?, image = ?, icon = ? WHERE id = ?");
        if ($stmt->execute([$new_name, $type, $description, $image_path, $icon, $cat_id])) {
            // Cascade category rename to products table
            if (!empty($old_name) && $old_name !== $new_name) {
                $up_prod = $pdo->prepare("UPDATE products SET category = ? WHERE category = ?");
                $up_prod->execute([$new_name, $old_name]);
            }
            $msg = "Category '{$new_name}' updated successfully!";
        } else {
            $err = "Failed to update category details.";
        }
    }
}

// 3. Handle Category Deletion
if (($_SERVER['REQUEST_METHOD'] ?? '') == 'POST' && isset($_POST['delete_category'])) {
    $cat_id = (int)$_POST['category_id'];
    $c_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $c_stmt->execute([$cat_id]);
    $cat_to_del = $c_stmt->fetchColumn();

    if ($cat_to_del) {
        // Check if products exist in this category
        $p_check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category = ?");
        $p_check->execute([$cat_to_del]);
        $prod_count = (int)$p_check->fetchColumn();

        if ($prod_count > 0) {
            $err = "Cannot delete category '{$cat_to_del}' because {$prod_count} products are currently assigned to it. Please reassign the products first.";
        } else {
            $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$cat_id]);
            $msg = "Category '{$cat_to_del}' removed from directory.";
        }
    }
}

// Query categories with catalog stats
$cat_query = "
    SELECT 
        c.id,
        c.name,
        c.type,
        c.description,
        c.image,
        c.icon,
        c.created_at,
        COUNT(p.id) as total_items,
        COALESCE(MIN(p.price), 0) as min_price,
        COALESCE(MAX(p.price), 0) as max_price,
        COALESCE(SUM(p.stock), 0) as total_stock
    FROM categories c
    LEFT JOIN products p ON (
        p.category = c.name 
        OR (c.name = 'Classic' AND (p.category = 'Classic' OR p.description LIKE '%Classic watch.%' OR p.name LIKE '%Classic%'))
        OR (c.name = 'Sports' AND (p.category = 'Sports' OR p.description LIKE '%Sports watch.%'))
        OR (c.name = 'Diver' AND (p.category = 'Diver' OR p.description LIKE '%Diver watch.%' OR p.description LIKE '%diving%'))
        OR (c.name = 'Dress' AND (p.category = 'Dress' OR p.description LIKE '%Dress watch.%' OR p.name LIKE '%Tank%'))
        OR (c.name = 'Modern' AND (p.category = 'Modern' OR p.description LIKE '%Modern%' OR p.description LIKE '%Contemporary%'))
        OR (c.name = 'Signature' AND (p.category = 'Signature' OR (p.category = 'Perfume' AND p.name LIKE '%Aventus%')))
        OR (c.name = 'Floral' AND (p.profile = 'Floral' OR p.category = 'Floral'))
        OR (c.name = 'Woody' AND (p.profile = 'Woody' OR p.category = 'Woody'))
        OR (c.name = 'Fresh' AND (p.profile = 'Fresh' OR p.category = 'Fresh'))
        OR (c.name = 'Oriental' AND (p.profile = 'Oriental' OR p.category = 'Oriental'))
        OR (c.name = 'Fruity' AND (p.profile = 'Fruity' OR p.category = 'Fruity'))
        OR (c.name = 'Watch' AND (p.category = 'Watch' OR p.category IN ('Classic', 'Sports', 'Diver', 'Dress')))
        OR (c.name = 'Perfume' AND (p.category = 'Perfume' OR p.category IN ('Modern', 'Signature', 'Floral', 'Woody', 'Fresh', 'Oriental', 'Fruity') OR p.profile IS NOT NULL))
    )
    GROUP BY c.id, c.name, c.type, c.description, c.image, c.icon, c.created_at
    ORDER BY c.name ASC
";
$categories_list = $pdo->query($cat_query)->fetchAll();

$total_categories_count = count($categories_list);
$watch_categories_count = 0;
$perfume_categories_count = 0;
$total_catalog_items = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

foreach ($categories_list as $c) {
    if ($c['type'] == 'Watch') $watch_categories_count++;
    if ($c['type'] == 'Perfume') $perfume_categories_count++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - ChronoScent Elite Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css?v=2" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css?v=2">
    <link rel="stylesheet" href="../assets/css/admin-common.css?v=<?php echo time(); ?>">
    <style>
        .category-img-preview {
            width: 52px;
            height: 52px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 4px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .category-badge-watch {
            background-color: #eff6ff;
            color: #1e3a5f;
            border: 1px solid #bfdbfe;
        }
        .category-badge-perfume {
            background-color: #ecfeff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .category-badge-general {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
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
                                <div class="stat-title">TOTAL CATEGORIES</div>
                                <div class="stat-value"><?= $total_categories_count ?></div>
                                <div class="stat-subtitle">Configured Catalog Departments</div>
                            </div>
                            <div class="stat-icon-wrapper icon-blue">
                                <i class="bi bi-folder"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">WATCH DEPARTMENTS</div>
                                <div class="stat-value"><?= $watch_categories_count ?></div>
                                <div class="stat-subtitle">Horology &amp; Timepieces</div>
                            </div>
                            <div class="stat-icon-wrapper icon-orange">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div>
                                <div class="stat-title">FRAGRANCE DEPARTMENTS</div>
                                <div class="stat-value"><?= $perfume_categories_count ?></div>
                                <div class="stat-subtitle">Haute Parfumerie &amp; Scents</div>
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
                                <div class="stat-subtitle">Items in Categories</div>
                            </div>
                            <div class="stat-icon-wrapper icon-green">
                                <i class="bi bi-bag-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Table Card -->
                <div class="purchases-card">
                    <div class="purchases-header">
                        <div>
                            <h5 class="purchases-title">Category Directory</h5>
                            <p class="purchases-count"><?= $total_categories_count ?> store categories configured</p>
                        </div>
                        <div>
                            <!-- ADD NEW CATEGORY BUTTON -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewCategoryModal" style="background-color: #1e3a5f; border-color: #1e3a5f;">
                                <i class="bi bi-plus-lg me-1"></i> Add New Category
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="orders-table align-middle">
                            <thead>
                                <tr>
                                    <th>CATEGORY</th>
                                    <th>DEPARTMENT / TYPE</th>
                                    <th>DESCRIPTION / DETAILS</th>
                                    <th>PRODUCTS</th>
                                    <th>STOCK</th>
                                    <th>PRICE RANGE</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories_list as $cat): 
                                    $cname = $cat['name'];
                                    $img_file = $cat['image'] ?: ('assets/images/categories/' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cname)) . '.png');
                                    $has_img = (!empty($img_file) && file_exists(__DIR__ . '/../' . $img_file));
                                    
                                    if ($cat['type'] == 'Watch') {
                                        $type_badge = '<span class="badge category-badge-watch px-2 py-1 rounded-pill"><i class="bi bi-clock me-1"></i>Watch</span>';
                                        $boutique_link = in_array($cname, ['Classic', 'Sports', 'Diver', 'Dress']) ? "../watches.php?style=" . urlencode($cname) : "../watches.php";
                                    } elseif ($cat['type'] == 'Perfume') {
                                        $type_badge = '<span class="badge category-badge-perfume px-2 py-1 rounded-pill"><i class="bi bi-flower1 me-1"></i>Perfume</span>';
                                        $boutique_link = in_array($cname, ['Floral', 'Woody', 'Fresh', 'Oriental', 'Fruity']) ? "../perfumes.php?profile=" . urlencode($cname) : "../perfumes.php";
                                    } else {
                                        $type_badge = '<span class="badge category-badge-general px-2 py-1 rounded-pill"><i class="bi bi-tag me-1"></i>' . htmlspecialchars($cat['type']) . '</span>';
                                        $boutique_link = "../index.php";
                                    }
                                ?>
                                <tr>
                                    <!-- Category Title & Icon / Image -->
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if ($has_img): ?>
                                                <img src="../<?= htmlspecialchars($img_file) ?>" alt="<?= htmlspecialchars($cname) ?>" class="category-img-preview">
                                            <?php else: ?>
                                                <div class="category-img-preview d-flex align-items-center justify-content-center fw-bold text-secondary fs-5">
                                                    <i class="bi <?= htmlspecialchars($cat['icon'] ?: 'bi-folder') ?> text-primary"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($cname) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($cat['type']) ?> Collection</small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Department Type -->
                                    <td>
                                        <?= $type_badge ?>
                                    </td>

                                    <!-- Description / Specialty -->
                                    <td>
                                        <div class="text-muted small text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($cat['description'] ?? '') ?>">
                                            <?= htmlspecialchars($cat['description'] ?: 'Luxury boutique category') ?>
                                        </div>
                                    </td>

                                    <!-- Products in Category -->
                                    <td>
                                        <span class="fw-bold text-dark"><?= $cat['total_items'] ?></span>
                                        <small class="text-muted">items</small>
                                    </td>

                                    <!-- Stock -->
                                    <td>
                                        <?php if ($cat['total_stock'] > 0): ?>
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill"><?= $cat['total_stock'] ?> units</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-muted border px-2 py-1 rounded-pill">0 units</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Price Range -->
                                    <td>
                                        <?php if ($cat['total_items'] > 0): ?>
                                            <span class="fw-semibold text-dark">₹<?= formatIndianNumber($cat['min_price']) ?></span>
                                            <span class="text-muted small">&ndash;</span>
                                            <span class="fw-semibold text-dark">₹<?= formatIndianNumber($cat['max_price']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">&mdash;</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <!-- View in Products -->
                                            <a href="products.php?category=<?= urlencode($cname) ?>" class="btn btn-sm btn-outline-secondary" title="View Products in Catalog">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <!-- Edit Category Modal Trigger -->
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?= $cat['id'] ?>" title="Edit Category Details">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <!-- Boutique Link -->
                                            <a href="<?= $boutique_link ?>" target="_blank" class="btn btn-sm btn-outline-info" title="View in Boutique">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                            <!-- Delete Category Form -->
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete category <?= htmlspecialchars($cname) ?>?');">
                                                <input type="hidden" name="delete_category" value="1">
                                                <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-action-delete" title="Delete Category">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- EDIT CATEGORY DETAILS MODAL -->
                                        <div class="modal fade text-start" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Edit Category: <?= htmlspecialchars($cname) ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="edit_category" value="1">
                                                            <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                                            <input type="hidden" name="old_name" value="<?= htmlspecialchars($cname) ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="category_name" class="form-control" value="<?= htmlspecialchars($cname) ?>" required>
                                                                <small class="text-muted">Renaming this category will automatically update all associated products.</small>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Department / Type <span class="text-danger">*</span></label>
                                                                <select name="category_type" class="form-select" required>
                                                                    <option value="Watch" <?= $cat['type'] == 'Watch' ? 'selected' : '' ?>>Watch</option>
                                                                    <option value="Perfume" <?= $cat['type'] == 'Perfume' ? 'selected' : '' ?>>Perfume</option>
                                                                    <option value="General" <?= $cat['type'] == 'General' ? 'selected' : '' ?>>General</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Category Icon</label>
                                                                <select name="category_icon" class="form-select">
                                                                    <option value="bi-award" <?= ($cat['icon'] == 'bi-award') ? 'selected' : '' ?>>Award / Classic Ribbon (bi-award)</option>
                                                                    <option value="bi-clock-history" <?= ($cat['icon'] == 'bi-clock-history') ? 'selected' : '' ?>>Watch / Clock (bi-clock-history)</option>
                                                                    <option value="bi-speedometer2" <?= ($cat['icon'] == 'bi-speedometer2') ? 'selected' : '' ?>>Sports / Chrono (bi-speedometer2)</option>
                                                                    <option value="bi-flower1" <?= ($cat['icon'] == 'bi-flower1') ? 'selected' : '' ?>>Perfume / Flower (bi-flower1)</option>
                                                                    <option value="bi-gem" <?= ($cat['icon'] == 'bi-gem') ? 'selected' : '' ?>>Modern / Gem (bi-gem)</option>
                                                                    <option value="bi-stars" <?= ($cat['icon'] == 'bi-stars') ? 'selected' : '' ?>>Signature / Stars (bi-stars)</option>
                                                                    <option value="bi-tag" <?= ($cat['icon'] == 'bi-tag') ? 'selected' : '' ?>>Tag (bi-tag)</option>
                                                                    <option value="bi-folder" <?= ($cat['icon'] == 'bi-folder') ? 'selected' : '' ?>>Folder (bi-folder)</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Category Description</label>
                                                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Change Category Banner / Image</label>
                                                                <input type="file" name="category_image" class="form-control" accept="image/*">
                                                                <small class="text-muted">Upload PNG, JPG, or SVG banner image. Leave empty to keep existing image.</small>
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

    <!-- ADD NEW CATEGORY MODAL -->
    <div class="modal fade" id="addNewCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="add_new_category" value="1">

                        <!-- Category Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="category_name" class="form-control" placeholder="e.g. Classic, Sports, Niche Cologne, Accessories" required>
                        </div>

                        <!-- Department / Type -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Department / Type <span class="text-danger">*</span></label>
                            <select name="category_type" class="form-select" required>
                                <option value="Watch">Watch</option>
                                <option value="Perfume">Perfume</option>
                                <option value="General">General</option>
                            </select>
                        </div>

                        <!-- Category Icon -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Icon</label>
                            <select name="category_icon" class="form-select">
                                <option value="">Auto Detect (Recommended)</option>
                                <option value="bi-award">Award / Classic Ribbon (bi-award)</option>
                                <option value="bi-clock-history">Watch / Clock (bi-clock-history)</option>
                                <option value="bi-speedometer2">Sports / Chrono (bi-speedometer2)</option>
                                <option value="bi-flower1">Perfume / Flower (bi-flower1)</option>
                                <option value="bi-gem">Modern / Luxury Gem (bi-gem)</option>
                                <option value="bi-stars">Signature / Stars (bi-stars)</option>
                                <option value="bi-tag">Tag (bi-tag)</option>
                                <option value="bi-folder">Folder (bi-folder)</option>
                            </select>
                        </div>

                        <!-- Category Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Briefly describe this luxury category collection..."></textarea>
                        </div>

                        <!-- Category Image Upload -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Banner / Image</label>
                            <input type="file" name="category_image" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended: High quality PNG or JPG on transparent or clean background.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #1e3a5f; border-color: #1e3a5f;">
                            <i class="bi bi-plus-lg me-1"></i> Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
