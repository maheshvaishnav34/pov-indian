<?php
/**
 * Admin Tab: Locations
 */
?>
<section class="tab-section <?= $activeTab === 'locations' ? 'active' : '' ?>" id="section-locations">
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> Locations & Destinations (<?= count($locations) ?>)
      </h2>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search locations..." onkeyup="filterTable(this, 'table-locations')" />
        <button type="button" class="btn-topbar btn-topbar-primary" onclick="openModal('addLocationModal')">
          <i class="fa-solid fa-plus"></i> Add New Location
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-locations">
        <thead>
          <tr>
            <th>ID</th>
            <th>Destination / City</th>
            <th>State</th>
            <th>Slug</th>
            <th>Coverage</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($locations as $loc): ?>
            <tr>
              <td style="font-weight:700;">#<?= htmlspecialchars($loc['id'] ?? '-') ?></td>
              <td style="font-weight:700; font-size:14px;">
                <i class="fa-solid fa-map-pin" style="color:var(--primary); margin-right:6px;"></i>
                <?= htmlspecialchars($loc['name']) ?>
              </td>
              <td style="font-weight:600; color:#475569;"><?= htmlspecialchars($loc['state'] ?? 'India') ?></td>
              <td><code><?= htmlspecialchars($loc['slug']) ?></code></td>
              <td><span class="chip chip-purple"><?= htmlspecialchars($loc['count'] ?? '0+ Listings') ?></span></td>
              <td><span class="chip chip-success">Active</span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
