<?php
$topbar_search = htmlspecialchars($_GET['search'] ?? '');
?>
<header class="admin-topbar">
    <form method="GET" action="dashboard.php" class="topbar-search-form">
        <div class="input-group topbar-input-group">
            <input type="text" name="search" class="form-control topbar-search-input" placeholder="Search products, orders, customer..." value="<?= $topbar_search ?>">
            <button type="submit" class="btn btn-search">
                <i class="bi bi-search me-1"></i> Search
            </button>
        </div>
    </form>

    <div class="topbar-actions">
        <button type="button" onclick="location.reload();" class="btn-topbar-action" title="Refresh Live Data">
            <i class="bi bi-arrow-repeat"></i> Sync
        </button>
        <a href="../index.php" target="_blank" class="btn-topbar-action" title="Open Boutique / Main Site">
            <i class="bi bi-box-arrow-up-right"></i> Boutique
        </a>
        <div class="topbar-avatar" title="<?= htmlspecialchars(ucfirst($admin_display_name ?? 'Admin')) ?>">
            <?= $admin_display_initial ?? 'A' ?>
        </div>
    </div>
</header>
