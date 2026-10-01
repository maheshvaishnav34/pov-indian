<?php
/**
 * Admin Tab: Overview
 */
?>
<section class="tab-section <?= $activeTab === 'overview' ? 'active' : '' ?>" id="section-overview">
  <!-- Hero Banner -->
  <div class="hero-banner">
    <div>
      <div class="hero-eyebrow"><i class="fa-solid fa-shield-halved"></i> POV Indian Management Suite</div>
      <h1>Namaste, <?= htmlspecialchars($user['name'] ?? 'SuperAdmin') ?>! 🇮🇳</h1>
      <p>
        Welcome to the POV Indian Management Suite. You have full administrative control over all businesses, categories, Indian destinations, blogs, leads, and registered accounts with live MariaDB / MySQL integration.
      </p>
    </div>
    <div class="hero-actions">
      <button type="button" class="btn-hero" onclick="openModal('addListingModal')">
        <i class="fa-solid fa-plus"></i> Add New Business
      </button>
    </div>
  </div>

  <!-- KPI Metrics -->
  <div class="stats-grid">
    <div class="stat-card orange" onclick="switchTab('listings')" style="cursor:pointer;">
      <div>
        <div class="stat-title">Directory Listings</div>
        <div class="stat-value"><?= count($listings) ?></div>
        <div class="stat-desc" style="color:var(--primary);">
          <i class="fa-solid fa-circle-check"></i> Live in MySQL
        </div>
      </div>
      <div class="stat-icon-wrap orange">
        <i class="fa-solid fa-building"></i>
      </div>
    </div>

    <div class="stat-card" onclick="switchTab('rentals')" style="cursor:pointer; background:#fff7ed; border:1px solid #fed7aa;">
      <div>
        <div class="stat-title" style="color:#c2410c; font-weight:700;">Rentals & Stays</div>
        <div class="stat-value" style="color:#9a3412;"><?= count($rentalListings) ?></div>
        <div class="stat-desc" style="color:#ea580c;">
          <i class="fa-solid fa-house-chimney"></i> Find a place that fits your life
        </div>
      </div>
      <div class="stat-icon-wrap" style="background:#ffedd5; color:#ea580c;">
        <i class="fa-solid fa-house-chimney"></i>
      </div>
    </div>

    <div class="stat-card blue" onclick="switchTab('categories')" style="cursor:pointer;">
      <div>
        <div class="stat-title">Categories</div>
        <div class="stat-value"><?= count($categories) ?></div>
        <div class="stat-desc" style="color:var(--info);">
          <i class="fa-solid fa-layer-group"></i> Active Sectors
        </div>
      </div>
      <div class="stat-icon-wrap blue">
        <i class="fa-solid fa-tags"></i>
      </div>
    </div>

    <div class="stat-card purple" onclick="switchTab('locations')" style="cursor:pointer;">
      <div>
        <div class="stat-title">Locations</div>
        <div class="stat-value"><?= count($locations) ?></div>
        <div class="stat-desc" style="color:#8b5cf6;">
          <i class="fa-solid fa-map-location-dot"></i> Indian Cities
        </div>
      </div>
      <div class="stat-icon-wrap purple">
        <i class="fa-solid fa-location-dot"></i>
      </div>
    </div>

    <div class="stat-card green" onclick="switchTab('inquiries')" style="cursor:pointer;">
      <div>
        <div class="stat-title">Inquiries & Leads</div>
        <div class="stat-value"><?= count($submissions) ?></div>
        <div class="stat-desc" style="color:var(--success);">
          <i class="fa-solid fa-inbox"></i> Form Submissions
        </div>
      </div>
      <div class="stat-icon-wrap green">
        <i class="fa-solid fa-envelope-open-text"></i>
      </div>
    </div>

    <div class="stat-card" onclick="switchTab('visits')" style="cursor:pointer; background:#f0fdf4; border:1px solid #bbf7d0;">
      <div>
        <div class="stat-title" style="color:#047857; font-weight:700;">Scheduled Visits</div>
        <div class="stat-value" style="color:#065f46;"><?= count($visitsList ?? []) ?></div>
        <div class="stat-desc" style="color:#059669;">
          <i class="fa-regular fa-calendar-check"></i> <?= $visitMetrics['requested'] ?? 0 ?> Pending Review
        </div>
      </div>
      <div class="stat-icon-wrap" style="background:#dcfce7; color:#059669;">
        <i class="fa-regular fa-calendar-check"></i>
      </div>
    </div>
  </div>

  <!-- Quick Action Shortcuts -->
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-bolt" style="color:var(--primary);"></i> Quick Data Actions
      </h2>
      <span style="font-size:12px; color:var(--text-muted);">Directly insert and manage data in MySQL</span>
    </div>
    <div class="shortcut-grid" style="padding: 20px;">
      <div class="shortcut-card" onclick="openModal('addListingModal')">
        <i class="fa-solid fa-plus-circle"></i>
        <div class="shortcut-card-title">+ Add Listing</div>
        <div class="shortcut-card-desc">Create business directory entry</div>
      </div>

      <div class="shortcut-card" onclick="openModal('addRentalModal')" style="background:#fff7ed; border-color:#fed7aa;">
        <i class="fa-solid fa-house-chimney" style="color:#ea580c;"></i>
        <div class="shortcut-card-title" style="color:#9a3412;">+ Add Rental</div>
        <div class="shortcut-card-desc">PGs, Hostels & Flats</div>
      </div>

      <div class="shortcut-card" onclick="openModal('addCategoryModal')">
        <i class="fa-solid fa-folder-plus"></i>
        <div class="shortcut-card-title">+ Add Category</div>
        <div class="shortcut-card-desc">Create business category</div>
      </div>

      <div class="shortcut-card" onclick="openModal('addLocationModal')">
        <i class="fa-solid fa-map-pin"></i>
        <div class="shortcut-card-title">+ Add Location</div>
        <div class="shortcut-card-desc">Add new city or state</div>
      </div>

      <div class="shortcut-card" onclick="openModal('addBlogModal')">
        <i class="fa-solid fa-file-pen"></i>
        <div class="shortcut-card-title">+ Add Blog</div>
        <div class="shortcut-card-desc">Publish editorial article</div>
      </div>

      <div class="shortcut-card" onclick="switchTab('inquiries')">
        <i class="fa-solid fa-envelope"></i>
        <div class="shortcut-card-title">View Leads (<?= count($submissions) ?>)</div>
        <div class="shortcut-card-desc">Review contact submissions</div>
      </div>

      <div class="shortcut-card" onclick="switchTab('visits')" style="background:#f0fdf4; border-color:#bbf7d0;">
        <i class="fa-regular fa-calendar-check" style="color:#059669;"></i>
        <div class="shortcut-card-title" style="color:#065f46;">Scheduled Visits (<?= count($visitsList ?? []) ?>)</div>
        <div class="shortcut-card-desc">Manage tenant walkthroughs</div>
      </div>

      <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=pov_indian_db" target="_blank" class="shortcut-card">
        <i class="fa-solid fa-database"></i>
        <div class="shortcut-card-title">phpMyAdmin</div>
        <div class="shortcut-card-desc">Direct MariaDB tables UI</div>
      </a>
    </div>
  </div>

  <!-- Recent Inquiries Preview Table -->
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-inbox" style="color:var(--primary);"></i> Recent Leads & Inquiries
      </h2>
      <button type="button" class="btn-topbar btn-topbar-secondary" onclick="switchTab('inquiries')">
        View All (<?= count($submissions) ?>) &rarr;
      </button>
    </div>
    <div class="table-container">
      <?php if (empty($submissions)): ?>
        <div class="empty-state-box">
          <i class="fa-regular fa-folder-open"></i>
          <p>No user inquiries or leads recorded yet.</p>
        </div>
      <?php else: ?>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Type</th>
              <th>Sender Name</th>
              <th>Email Address</th>
              <th>Subject & Message</th>
              <th>Received At</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($submissions, 0, 5) as $sub): ?>
              <tr>
                <td style="font-weight:700;">#<?= htmlspecialchars($sub['id']) ?></td>
                <td><span class="chip chip-slate"><?= htmlspecialchars($sub['type']) ?></span></td>
                <td style="font-weight:600;"><?= htmlspecialchars($sub['name'] ?? 'N/A') ?></td>
                <td><a href="mailto:<?= htmlspecialchars($sub['email']) ?>" style="color:var(--info); text-decoration:none;"><?= htmlspecialchars($sub['email']) ?></a></td>
                <td style="max-width:280px;">
                  <div style="font-weight:600; margin-bottom:2px;"><?= htmlspecialchars($sub['subject'] ?? '') ?></div>
                  <div style="color:var(--text-muted); font-size:12px; line-height:1.4; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($sub['message'] ?? '') ?></div>
                </td>
                <td style="color:var(--text-muted); font-size:12px; white-space:nowrap;">
                  <?= htmlspecialchars(date('M d, Y · h:i A', strtotime($sub['created_at']))) ?>
                </td>
                <td><span class="chip chip-success"><?= htmlspecialchars($sub['status']) ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</section>
