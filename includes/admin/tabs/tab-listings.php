<?php
/**
 * Admin Tab: Listings (Directory Listings & "Find a place that fits your life" Rentals & Stays)
 */
?>
<?php
$isRentalsActive = ($activeTab === 'rentals' || ($activeSubtab ?? '') === 'rentals');
?>
<section class="tab-section <?= ($activeTab === 'listings' || $activeTab === 'rentals') ? 'active' : '' ?>" id="section-listings">

  <!-- Sub-navigation Switcher between Directory and Rentals -->
  <div class="subtab-switcher">
    <button type="button" class="subtab-pill <?= !$isRentalsActive ? 'active' : '' ?>" id="subtabBtnDirectory" onclick="switchListingsSubtab('directory')">
      <i class="fa-solid fa-building"></i> Directory Listings 
      <span class="subtab-badge"><?= count($listings) ?></span>
    </button>
    <button type="button" class="subtab-pill <?= $isRentalsActive ? 'active' : '' ?>" id="subtabBtnRentals" onclick="switchListingsSubtab('rentals')">
      <i class="fa-solid fa-house-chimney" style="color:var(--primary);"></i> Find a place that fits your life. (Rentals & Stays)
      <span class="subtab-badge"><?= count($rentalListings) ?></span>
    </button>
  </div>

  <!-- ================= SUB-PANEL 1: DIRECTORY LISTINGS ================= -->
  <div class="card-panel" id="panel-directory-listings" style="<?= $isRentalsActive ? 'display:none;' : '' ?>">
    <div class="card-panel-header">
      <div>
        <h2 class="card-panel-title">
          <i class="fa-solid fa-building" style="color:var(--primary);"></i> Directory Listings (<?= count($listings) ?>)
        </h2>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">
          Manage verified business profiles, service providers, clinics, restaurants and venues.
        </div>
      </div>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search directory listings..." onkeyup="filterTable(this, 'table-listings')" />
        <button type="button" class="btn-topbar btn-topbar-primary" onclick="openModal('addListingModal')">
          <i class="fa-solid fa-plus"></i> Add New Listing
        </button>
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="openModal('addRentalModal')" title="Add Rental Property">
          <i class="fa-solid fa-house-chimney"></i> Add Rental Property
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-listings">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Business Title</th>
            <th>Category</th>
            <th>Location</th>
            <th>Price</th>
            <th>Phone</th>
            <th>Rating</th>
            <th>Badge</th>
            <th>SEO Status</th>
            <th style="min-width:260px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($listings)): ?>
            <tr><td colspan="11" class="empty-state-box">No listings found in database.</td></tr>
          <?php else: ?>
            <?php foreach ($listings as $item): ?>
              <tr>
                <td style="font-weight:700;">#<?= htmlspecialchars($item['id']) ?></td>
                <td>
                  <div style="position:relative; width:52px; height:38px; display:inline-block;">
                    <img src="<?= htmlspecialchars(pov_dash_listing_img($item['img'] ?? '')) ?>" class="media-thumb" alt="thumb" style="width:52px; height:38px; border-radius:8px; object-fit:cover;" onerror="this.src='<?= htmlspecialchars(pov_url('assets/img/listings/realestate_01.webp')) ?>'" />
                    <img src="<?= htmlspecialchars(pov_dash_listing_avatar($item['avatar'] ?? '')) ?>" alt="logo" title="Logo / Brand Icon" style="position:absolute; bottom:-3px; right:-3px; width:20px; height:20px; border-radius:50%; border:2px solid #ffffff; object-fit:cover; background:#ffffff; box-shadow:0 1px 4px rgba(0,0,0,0.25);" onerror="this.style.display='none'" />
                  </div>
                </td>
                <td>
                  <div style="font-weight:700; font-size:14px;"><?= htmlspecialchars($item['title']) ?></div>
                  <div style="font-size:12px; color:var(--text-muted); max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    <?= htmlspecialchars($item['desc'] ?? '') ?>
                  </div>
                </td>
                <td>
                  <span class="chip chip-primary"><?= htmlspecialchars($item['cat'] ?? 'Business') ?></span>
                </td>
                <td style="font-size:13px; color:#334155;">
                  <i class="fa-solid fa-location-dot" style="color:#94a3b8; margin-right:4px;"></i>
                  <?= htmlspecialchars($item['loc'] ?? 'India') ?>
                </td>
                <td style="font-weight:600; font-size:13px; color:#0f172a; white-space:nowrap;">
                  <?= htmlspecialchars($item['price'] ?? '₹ -') ?>
                </td>
                <td style="font-size:12px; color:var(--text-muted); white-space:nowrap;">
                  <?= htmlspecialchars($item['phone'] ?? '-') ?>
                </td>
                <td>
                  <span class="chip chip-warning">
                    <i class="fa-solid fa-star" style="font-size:10px;"></i> <?= htmlspecialchars($item['rating'] ?? '5.0') ?>
                  </span>
                </td>
                <td>
                  <?php if (!empty($item['badge'])): 
                    $bLower = strtolower($item['badge']);
                    $bClass = 'chip-primary';
                    if (str_contains($bLower, 'feat')) $bClass = 'chip-featured';
                    elseif (str_contains($bLower, 'top') || str_contains($bLower, 'pop')) $bClass = 'chip-top';
                    elseif (str_contains($bLower, 'verif')) $bClass = 'chip-success';
                  ?>
                    <span class="chip <?= $bClass ?>"><?= htmlspecialchars($item['badge']) ?></span>
                  <?php else: ?>
                    <span style="color:#cbd5e1;">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php 
                    $hasCustomSeo = (!empty($item['meta_title']) || !empty($item['meta_description']) || !empty($item['slug']));
                  ?>
                  <?php if ($hasCustomSeo): ?>
                    <span class="chip" style="background:#fdf4ff; color:#7e22ce; border:1px solid #f0abfc; font-size:11.5px; font-weight:600; cursor:pointer;" onclick="openSeoModal(<?= htmlspecialchars(json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Click to view & edit SEO tags">
                      <i class="fa-solid fa-circle-check" style="color:#9333ea; font-size:10px; margin-right:3px;"></i> Optimized
                    </span>
                  <?php else: ?>
                    <span class="chip" style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; font-size:11.5px; font-weight:500; cursor:pointer;" onclick="openSeoModal(<?= htmlspecialchars(json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Click to customize SEO tags">
                      <i class="fa-solid fa-wand-magic-sparkles" style="color:#94a3b8; font-size:10px; margin-right:3px;"></i> Auto SEO
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:6px;">
                    <!-- View Button -->
                    <a href="<?= htmlspecialchars(pov_url('listing-detail.php?id=' . $item['id'])) ?>" target="_blank" class="btn-action-view" title="View Listing Live on Site">
                      <i class="fa-solid fa-arrow-up-right-from-square"></i> View
                    </a>
                    <!-- SEO Button -->
                    <button type="button" class="btn-action-seo" onclick="openSeoModal(<?= htmlspecialchars(json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Configure Google SEO, Meta Description & Schema">
                      <i class="fa-solid fa-magnifying-glass-chart"></i> SEO
                    </button>
                    <!-- Edit Button -->
                    <button type="button" class="btn-action-edit" onclick="openEditListingModal(<?= htmlspecialchars(json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Edit Listing Details">
                      <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                    <!-- Delete Button -->
                    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" onsubmit="return confirm('Are you sure you want to delete listing #<?= (int)$item['id'] ?> (<?= htmlspecialchars(addslashes($item['title'])) ?>)?');" style="display:inline; margin:0;">
                      <input type="hidden" name="admin_action" value="delete_listing" />
                      <input type="hidden" name="id" value="<?= (int)$item['id'] ?>" />
                      <button type="submit" class="btn-action-delete" title="Delete Listing">
                        <i class="fa-solid fa-trash"></i> Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ================= SUB-PANEL 2: FIND A PLACE THAT FITS YOUR LIFE (RENTALS & STAYS) ================= -->
  <div class="card-panel" id="panel-rentals-listings" style="<?= $isRentalsActive ? 'display:block;' : 'display:none;' ?>">
    <div class="card-panel-header">
      <div>
        <h2 class="card-panel-title">
          <i class="fa-solid fa-house-chimney" style="color:var(--primary);"></i> Find a place that fits your life. (<?= count($rentalListings) ?>)
        </h2>
        <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">
          Rentals & Stays accommodation layer — verified PGs, hostels, 1/2 BHK flats, boutique homestays with real photos, honest house rules & GPS coordinates.
        </div>
      </div>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search rentals & stays..." onkeyup="filterTable(this, 'table-rentals')" />
        <button type="button" class="btn-topbar btn-topbar-primary" onclick="openModal('addRentalModal')">
          <i class="fa-solid fa-plus"></i> Add Rental Property
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-rentals">
        <thead>
          <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Property Title & Subtitle</th>
            <th>Type / BHK</th>
            <th>City & Landmark</th>
            <th>Rent & Deposit</th>
            <th>Host / Contact</th>
            <th>Lifestyle & Rules</th>
            <th style="min-width:210px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rentalListings)): ?>
            <tr><td colspan="9" class="empty-state-box">No rental properties found in database.</td></tr>
          <?php else: ?>
            <?php foreach ($rentalListings as $r): ?>
              <tr>
                <td style="font-weight:700;">#<?= htmlspecialchars($r['id']) ?></td>
                <td>
                  <div style="position:relative; width:56px; height:42px; display:inline-block;">
                    <img src="<?= htmlspecialchars(pov_dash_listing_img($r['img'] ?? $r['image'] ?? '')) ?>" class="media-thumb" alt="rental thumb" style="width:56px; height:42px; border-radius:8px; object-fit:cover;" onerror="this.src='<?= htmlspecialchars(pov_url('assets/img/listings/realestate_01.webp')) ?>'" />
                    <?php if (!empty($r['verified_badge'])): ?>
                      <span title="Verified Property" style="position:absolute; bottom:-3px; right:-3px; width:18px; height:18px; border-radius:50%; background:#10b981; color:#fff; font-size:10px; display:flex; align-items:center; justify-content:center; border:2px solid #fff;">
                        <i class="fa-solid fa-check"></i>
                      </span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <div style="font-weight:700; font-size:14px; color:#0f172a;">
                    <?= htmlspecialchars($r['title']) ?>
                  </div>
                  <div style="font-size:12px; color:var(--text-muted); max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    <?= htmlspecialchars($r['desc'] ?? $r['description'] ?? 'Find a place that fits your life.') ?>
                  </div>
                </td>
                <td>
                  <span class="chip chip-primary" style="font-size:11.5px;"><?= htmlspecialchars($r['category'] ?? 'Rental') ?></span>
                  <div style="font-size:12px; font-weight:600; color:#334155; margin-top:3px;">
                    <?= htmlspecialchars($r['bhk'] ?? '1 BHK') ?>
                  </div>
                </td>
                <td style="font-size:13px; color:#334155;">
                  <div style="font-weight:600; color:#0f172a;">
                    <i class="fa-solid fa-location-dot" style="color:var(--primary); margin-right:4px;"></i>
                    <?= htmlspecialchars($r['city'] ?? 'India') ?>
                  </div>
                  <div style="font-size:12px; color:#64748b;">
                    <?= htmlspecialchars($r['landmark'] ?? ($r['locality'] ?? '')) ?>
                  </div>
                  <?php if (!empty($r['latitude']) && !empty($r['longitude'])): ?>
                    <div style="font-size:10.5px; color:#94a3b8; font-family:monospace; margin-top:2px;">
                      <?= round((float)$r['latitude'], 4) ?>, <?= round((float)$r['longitude'], 4) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight:700; font-size:13.5px; color:#0f172a; white-space:nowrap;">
                    <?= htmlspecialchars($r['price'] ?? '₹ -') ?>
                    <span style="font-size:11px; font-weight:400; color:#64748b;"><?= htmlspecialchars($r['price_unit'] ?? '/ mo') ?></span>
                  </div>
                  <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                    Dep: <span style="font-weight:600;"><?= htmlspecialchars($r['deposit'] ?? 'Nil') ?></span>
                  </div>
                </td>
                <td style="font-size:12px; color:#334155;">
                  <div style="font-weight:600;"><?= htmlspecialchars($r['host_name'] ?? $r['provider_name'] ?? 'Host') ?></div>
                  <div style="color:#64748b; font-size:11.5px;"><?= htmlspecialchars($r['host_phone'] ?? $r['phone'] ?? '-') ?></div>
                </td>
                <td>
                  <div style="display:flex; flex-direction:column; gap:3px;">
                    <span style="font-size:11px; background:#f1f5f9; padding:2px 6px; border-radius:4px; color:#334155;">
                      <i class="fa-solid fa-couch" style="font-size:10px; color:#64748b;"></i> <?= htmlspecialchars($r['furnishing'] ?? 'Semi-Furnished') ?>
                    </span>
                    <span style="font-size:11px; background:#f8fafc; border:1px solid #e2e8f0; padding:2px 6px; border-radius:4px; color:#475569;">
                      <i class="fa-solid fa-utensils" style="font-size:10px; color:#64748b;"></i> <?= htmlspecialchars($r['food_rule'] ?? $r['food_label'] ?? 'Food Open') ?>
                    </span>
                  </div>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:6px;">
                    <!-- View Button -->
                    <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $r['id'])) ?>" target="_blank" class="btn-action-view" title="View Property Live on Site">
                      <i class="fa-solid fa-arrow-up-right-from-square"></i> View
                    </a>
                    <!-- Edit Button -->
                    <button type="button" class="btn-action-edit" onclick="openEditRentalModal(<?= htmlspecialchars(json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>)" title="Edit Property Details">
                      <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                    <!-- Delete Button -->
                    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" onsubmit="return confirm('Are you sure you want to delete rental property #<?= (int)$r['id'] ?> (<?= htmlspecialchars(addslashes($r['title'])) ?>)?');" style="display:inline; margin:0;">
                      <input type="hidden" name="admin_action" value="delete_rental_listing" />
                      <input type="hidden" name="id" value="<?= (int)$r['id'] ?>" />
                      <button type="submit" class="btn-action-delete" title="Delete Rental Property">
                        <i class="fa-solid fa-trash"></i> Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</section>
