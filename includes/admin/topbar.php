<?php
/**
 * Admin Topbar Component
 */
?>
<header class="admin-topbar">
  <div class="topbar-left">
    <h1 class="page-title" id="pageTitleHeading">
      <i class="fa-solid fa-gauge-high" style="color:var(--primary);"></i> Overview
    </h1>
  </div>

  <div class="topbar-right">
    <!-- Quick Add Modal Trigger Dropdown -->
    <button type="button" class="btn-topbar btn-topbar-primary" onclick="openModal('addListingModal')">
      <i class="fa-solid fa-plus"></i> Add Listing
    </button>
    <button type="button" class="btn-topbar btn-topbar-secondary" onclick="openModal('addCategoryModal')">
      <i class="fa-solid fa-folder-plus"></i> Add Category
    </button>
    <a href="<?= htmlspecialchars(pov_url('index.php')) ?>" target="_blank" class="btn-topbar btn-topbar-secondary" title="View Frontend">
      <i class="fa-solid fa-arrow-up-right-from-square"></i> Website
    </a>
  </div>
</header>
