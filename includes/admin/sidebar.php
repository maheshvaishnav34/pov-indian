<?php
/**
 * Admin Sidebar Component
 */
?>
<aside class="admin-sidebar">
  <div class="sidebar-header">
    <a href="<?= htmlspecialchars(pov_url('index.php')) ?>" class="sidebar-brand-link" title="POV Indian - Return to Website">
      <div class="sidebar-brand-badge">
        <i class="fa-solid fa-check-double"></i>
      </div>
      <div>
        <div class="brand-title">POV <span>Indian</span></div>
        <div class="brand-subtitle">SuperAdmin Suite</div>
      </div>
    </a>
  </div>

  <!-- Navigation Menu -->
  <nav class="sidebar-menu">
    <div class="menu-group-title">Main</div>
    <div class="nav-item <?= $activeTab === 'overview' ? 'active' : '' ?>" onclick="switchTab('overview')">
      <div class="nav-item-left">
        <i class="fa-solid fa-gauge-high"></i>
        <span>Overview</span>
      </div>
    </div>

    <div class="menu-group-title">Content Management</div>
    <div class="nav-item <?= ($activeTab === 'listings' && ($activeSubtab ?? '') !== 'rentals') ? 'active' : '' ?>" id="navItemDirectory" onclick="switchTab('listings')">
      <div class="nav-item-left">
        <i class="fa-solid fa-building"></i>
        <span>Directory Listings</span>
      </div>
      <span class="nav-badge" id="badge-listings"><?= count($listings) ?></span>
    </div>

    <div class="nav-item <?= ($activeTab === 'rentals' || ($activeSubtab ?? '') === 'rentals') ? 'active' : '' ?>" id="navItemRentals" onclick="switchTab('rentals')">
      <div class="nav-item-left">
        <i class="fa-solid fa-house-chimney" style="color:var(--primary);"></i>
        <span>Rentals & Stays</span>
      </div>
      <span class="nav-badge" id="badge-rentals" style="background:var(--primary); color:#fff;"><?= count($rentalListings) ?></span>
    </div>

    <div class="nav-item <?= $activeTab === 'categories' ? 'active' : '' ?>" onclick="switchTab('categories')">
      <div class="nav-item-left">
        <i class="fa-solid fa-layer-group"></i>
        <span>Categories</span>
      </div>
      <span class="nav-badge" id="badge-categories"><?= count($categories) ?></span>
    </div>

    <div class="nav-item <?= $activeTab === 'locations' ? 'active' : '' ?>" onclick="switchTab('locations')">
      <div class="nav-item-left">
        <i class="fa-solid fa-location-dot"></i>
        <span>Locations</span>
      </div>
      <span class="nav-badge" id="badge-locations"><?= count($locations) ?></span>
    </div>

    <div class="nav-item <?= $activeTab === 'blogs' ? 'active' : '' ?>" onclick="switchTab('blogs')">
      <div class="nav-item-left">
        <i class="fa-solid fa-newspaper"></i>
        <span>Blog Articles</span>
      </div>
      <span class="nav-badge" id="badge-blogs"><?= count($blogs) ?></span>
    </div>

    <div class="menu-group-title">Higher Education (Find College)</div>
    <div class="nav-item <?= ($activeTab === 'education' && (($activeSubtab ?? 'institutions') === 'institutions')) ? 'active' : '' ?>" id="navItemEduInstitutions" onclick="switchEduSection('institutions', this)">
      <div class="nav-item-left">
        <i class="fa-solid fa-building-columns" style="color:#C9A96E;"></i>
        <span>Colleges & Unis</span>
      </div>
      <span class="nav-badge" id="badge-edu-inst"><?= count($eduInstitutions ?? []) ?></span>
    </div>

    <div class="nav-item <?= ($activeTab === 'education' && (($activeSubtab ?? '') === 'offerings')) ? 'active' : '' ?>" id="navItemEduOfferings" onclick="switchEduSection('offerings', this)">
      <div class="nav-item-left">
        <i class="fa-solid fa-graduation-cap" style="color:#C9A96E;"></i>
        <span>Course Offerings</span>
      </div>
      <span class="nav-badge" id="badge-edu-courses"><?= $totalEduOfferingsCount ?? 0 ?></span>
    </div>

    <div class="nav-item <?= ($activeTab === 'education' && (($activeSubtab ?? '') === 'leads')) ? 'active' : '' ?>" id="navItemEduLeads" onclick="switchEduSection('leads', this)">
      <div class="nav-item-left">
        <i class="fa-solid fa-user-graduate" style="color:#D97746;"></i>
        <span>Admission Leads</span>
      </div>
      <span class="nav-badge" id="badge-edu-leads" style="background:#D97746; color:#fff;"><?= count($eduLeads ?? []) ?></span>
    </div>

    <div class="nav-item <?= ($activeTab === 'education' && (($activeSubtab ?? '') === 'exams')) ? 'active' : '' ?>" id="navItemEduExams" onclick="switchEduSection('exams', this)">
      <div class="nav-item-left">
        <i class="fa-solid fa-pen-clip" style="color:#3B82F6;"></i>
        <span>Entrance Exams</span>
      </div>
      <span class="nav-badge" id="badge-edu-exams"><?= count($eduExams ?? []) ?></span>
    </div>

    <div class="nav-item <?= ($activeTab === 'education' && (($activeSubtab ?? '') === 'scholarships')) ? 'active' : '' ?>" id="navItemEduScholarships" onclick="switchEduSection('scholarships', this)">
      <div class="nav-item-left">
        <i class="fa-solid fa-award" style="color:#10B981;"></i>
        <span>Scholarships</span>
      </div>
      <span class="nav-badge" id="badge-edu-scholarships"><?= count($eduScholarships ?? []) ?></span>
    </div>

    <div class="menu-group-title">Users & Inquiries</div>

    <div class="nav-item <?= $activeTab === 'inquiries' ? 'active' : '' ?>" onclick="switchTab('inquiries')">
      <div class="nav-item-left">
        <i class="fa-solid fa-envelope-open-text"></i>
        <span>Inquiries & Leads</span>
      </div>
      <span class="nav-badge" id="badge-inquiries"><?= count($submissions) ?></span>
    </div>

    <div class="nav-item <?= $activeTab === 'visits' ? 'active' : '' ?>" onclick="switchTab('visits')">
      <div class="nav-item-left">
        <i class="fa-solid fa-calendar-check" style="color:#059669;"></i>
        <span>Scheduled Visits</span>
      </div>
      <span class="nav-badge" id="badge-visits" style="background:#059669; color:#fff;"><?= count($visitsList ?? []) ?></span>
    </div>

    <div class="nav-item <?= $activeTab === 'users' ? 'active' : '' ?>" onclick="switchTab('users')">
      <div class="nav-item-left">
        <i class="fa-solid fa-users"></i>
        <span>Registered Users</span>
      </div>
      <span class="nav-badge" id="badge-users"><?= count($users) ?></span>
    </div>

    <div class="menu-group-title">System</div>
    <div class="nav-item <?= $activeTab === 'system' ? 'active' : '' ?>" onclick="switchTab('system')">
      <div class="nav-item-left">
        <i class="fa-solid fa-server"></i>
        <span>Database & API</span>
      </div>
      <span class="nav-badge" style="background:#10b981; color:#fff;">Live</span>
    </div>
  </nav>

  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    <a href="<?= htmlspecialchars(pov_url('index.php')) ?>" target="_blank" class="btn-side-action btn-side-site">
      <i class="fa-solid fa-arrow-up-right-from-square"></i>
      <span>Live Website</span>
    </a>
    <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=pov_indian_db" target="_blank" class="btn-side-action btn-side-site">
      <i class="fa-solid fa-database"></i>
      <span>phpMyAdmin</span>
    </a>
    <a href="<?= htmlspecialchars(pov_url('admin-dashboard.php?logout=1')) ?>" class="btn-side-action btn-side-logout">
      <i class="fa-solid fa-power-off"></i>
      <span>Sign Out</span>
    </a>
  </div>
</aside>
