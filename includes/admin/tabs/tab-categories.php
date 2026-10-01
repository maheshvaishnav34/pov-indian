<?php
/**
 * Admin Tab: Categories
 * Interactive category management with instant Sign Up toggle buttons.
 */
$signupCount = 0;
foreach ($categories as $c) {
  if (!empty($c['show_in_signup'])) {
    $signupCount++;
  }
}
$hiddenCount = count($categories) - $signupCount;
?>
<section class="tab-section <?= $activeTab === 'categories' ? 'active' : '' ?>" id="section-categories">
  <div class="card-panel">
    <div class="card-panel-header" style="flex-wrap:wrap; gap:16px;">
      <div>
        <h2 class="card-panel-title">
          <i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Categories Management (<?= count($categories) ?>)
        </h2>
        <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; align-items:center;">
          <span class="cat-stat-badge active-count" id="signupActiveBadge" title="Categories currently visible in the Sign Up form">
            <i class="fa-solid fa-circle-check"></i> <strong id="signupActiveCount"><?= $signupCount ?></strong> Shown in Sign Up Form
          </span>
          <span class="cat-stat-badge hidden-count" id="signupHiddenBadge" title="Categories hidden from the Sign Up form">
            <i class="fa-solid fa-circle-xmark"></i> <strong id="signupHiddenCount"><?= $hiddenCount ?></strong> Hidden from Sign Up
          </span>
          <span style="font-size:12px; color:var(--muted); margin-left:4px;">
            <i class="fa-solid fa-circle-info" style="color:var(--primary);"></i> Click any category's Sign Up button to toggle its visibility on the public Sign Up modal.
          </span>
        </div>
      </div>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search categories..." onkeyup="filterTable(this, 'table-categories')" />
        <button type="button" class="btn-topbar btn-topbar-primary" onclick="openModal('addCategoryModal')">
          <i class="fa-solid fa-plus"></i> Add New Category
        </button>
      </div>
    </div>

    <div class="table-container">
      <table class="data-table" id="table-categories">
        <thead>
          <tr>
            <th style="width:60px;">ID</th>
            <th style="width:70px;">Icon</th>
            <th>Category Name</th>
            <th>Slug</th>
            <th>Listings</th>
            <th>Description</th>
            <th style="min-width:210px;">Sign Up Page Display</th>
            <th style="width:90px; text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
            <?php $inSignup = !empty($cat['show_in_signup']); ?>
            <tr data-cat-id="<?= (int)$cat['id'] ?>">
              <td style="font-weight:700; color:#64748b;">#<?= htmlspecialchars($cat['id'] ?? '-') ?></td>
              <td>
                <div class="category-icon-box">
                  <?= pov_dash_cat_icon($cat['icon'] ?? '') ?>
                </div>
              </td>
              <td>
                <div style="font-weight:700; font-size:14px; color:#0f172a;">
                  <?= htmlspecialchars($cat['name']) ?>
                </div>
              </td>
              <td><code style="background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:12px; color:#475569;"><?= htmlspecialchars($cat['slug']) ?></code></td>
              <td><span class="chip chip-info"><?= htmlspecialchars($cat['count'] ?? '0+ Listings') ?></span></td>
              <td style="color:var(--text-muted); font-size:12.5px; max-width:240px; line-height:1.4;">
                <?= htmlspecialchars($cat['desc'] ?? '-') ?>
              </td>
              <td>
                <!-- Interactive Form Button for Sign Up Display -->
                <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" class="signup-toggle-form" style="display:inline-block; margin:0;" onsubmit="event.preventDefault(); toggleCategorySignup(this.querySelector('.btn-signup-toggle'), <?= (int)$cat['id'] ?>);">
                  <input type="hidden" name="admin_action" value="toggle_category_signup" />
                  <input type="hidden" name="id" value="<?= (int)($cat['id'] ?? 0) ?>" />
                  <?php if ($inSignup): ?>
                    <button type="submit" class="btn-signup-toggle on" title="Currently VISIBLE on Sign Up form. Click to hide.">
                      <span class="toggle-track"><span class="toggle-thumb"></span></span>
                      <span class="toggle-label"><i class="fa-solid fa-check"></i> Shown in Sign Up</span>
                    </button>
                  <?php else: ?>
                    <button type="submit" class="btn-signup-toggle off" title="Currently HIDDEN from Sign Up form. Click to show.">
                      <span class="toggle-track"><span class="toggle-thumb"></span></span>
                      <span class="toggle-label"><i class="fa-solid fa-xmark"></i> Hidden in Sign Up</span>
                    </button>
                  <?php endif; ?>
                </form>
              </td>
              <td style="text-align:right;">
                <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" onsubmit="return confirm('Are you sure you want to delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'?');" style="display:inline-block; margin:0;">
                  <input type="hidden" name="admin_action" value="delete_category" />
                  <input type="hidden" name="id" value="<?= (int)($cat['id'] ?? 0) ?>" />
                  <button type="submit" class="btn-action-icon delete" title="Delete category" style="background:#fee2e2; color:#ef4444; border:none; width:32px; height:32px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
