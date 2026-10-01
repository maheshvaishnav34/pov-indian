<?php
$current = $current_page ?? '';
?>
<header class="site-header">
  <div class="container">
    <div class="header-pill">
      <a class="brand" href="<?= htmlspecialchars(pov_url('index.php')) ?>" aria-label="POV Indian home">
        <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-check-double"></i></span>
        <span class="brand-text">
          <strong>POV Indian</strong>
          <small>AUTHENTIC PERSPECTIVES</small>
        </span>
      </a>
      <button class="menu-toggle" type="button" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>
      <div class="nav-panel">
        <ul class="nav">
          <li class="<?= pov_active('home', $current) ?>">
            <a href="<?= htmlspecialchars(pov_url('index.php')) ?>">Home <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></a>
            <div class="dropdown">
              <a href="<?= htmlspecialchars(pov_url('index.php')) ?>">Home</a>
              <a href="<?= htmlspecialchars(pov_url('categories.php')) ?>">Categories</a>
              <a href="<?= htmlspecialchars(pov_url('locations.php')) ?>">Locations</a>
              <a href="<?= htmlspecialchars(pov_url('pricing.php')) ?>">Pricing</a>
            </div>
          </li>
          <li class="<?= pov_active('listings', $current) ?>">
            <a href="<?= htmlspecialchars(pov_url('listings.php')) ?>">Listings <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></a>
            <div class="dropdown">
              <?php 
                $isHeaderPersonalized = function_exists('pov_is_user_personalized') && pov_is_user_personalized();
                $headerCats = function_exists('pov_get_user_allowed_categories') ? pov_get_user_allowed_categories() : $POV_CATEGORIES;
              ?>
              <a href="<?= htmlspecialchars(pov_url('listings.php')) ?>"><?= $isHeaderPersonalized ? 'My Feed Listings' : 'All Listings' ?></a>
              <a href="<?= htmlspecialchars(pov_url('categories.php')) ?>"><?= $isHeaderPersonalized ? 'My Categories' : 'All Categories' ?></a>
              <?php foreach (array_slice($headerCats, 0, 8) as $navCat): ?>
                <a href="<?= htmlspecialchars(pov_url(pov_category_page($navCat['slug']))) ?>"><?= htmlspecialchars($navCat['name']) ?></a>
              <?php endforeach; ?>
              <?php if ($isHeaderPersonalized): ?>
                <a href="#" data-open-edit-interests style="color:var(--primary); font-weight:600; border-top:1px solid #f1f5f9; padding-top:8px;">
                  <i class="fa-solid fa-sliders" style="margin-right:6px;"></i> Edit Interests
                </a>
              <?php endif; ?>
            </div>
          </li>
          <li class="<?= pov_active('pages', $current) ?>">
            <a href="<?= htmlspecialchars(pov_url('how-it-works.php')) ?>">Explore <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></a>
            <div class="dropdown">
              <a href="<?= htmlspecialchars(pov_url('education.php')) ?>" style="color:#C9A96E; font-weight:700;">
                <i class="fa-solid fa-graduation-cap" style="margin-right:6px;"></i> Higher Education (2026–27)
              </a>
              <a href="<?= htmlspecialchars(pov_url('colleges-explore.php')) ?>" style="padding-left:28px; font-size:13px; color:#475569;">
                <i class="fa-solid fa-building-columns" style="margin-right:6px; color:#C9A96E;"></i> Explore Colleges
              </a>
              <a href="<?= htmlspecialchars(pov_url('entrance-exams.php')) ?>" style="padding-left:28px; font-size:13px; color:#475569;">
                <i class="fa-solid fa-pen-clip" style="margin-right:6px; color:#C9A96E;"></i> Entrance Exams 2026
              </a>
              <a href="<?= htmlspecialchars(pov_url('scholarships.php')) ?>" style="padding-left:28px; font-size:13px; color:#475569;">
                <i class="fa-solid fa-award" style="margin-right:6px; color:#C9A96E;"></i> Scholarships & Aid
              </a>
              <a href="<?= htmlspecialchars(pov_url('admission-inquiry.php')) ?>" style="padding-left:28px; font-size:13px; color:#475569; border-bottom:1px solid #f1f5f9; padding-bottom:8px; margin-bottom:4px;">
                <i class="fa-solid fa-headset" style="margin-right:6px; color:#C9A96E;"></i> Free Counselling
              </a>
              <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>" style="color:var(--primary); font-weight:600;">
                <i class="fa-solid fa-building-user" style="margin-right:6px;"></i> Hotels & Rentals Platform
              </a>
              <a href="<?= htmlspecialchars(pov_url('how-it-works.php')) ?>">How It Works</a>
              <a href="<?= htmlspecialchars(pov_url('pricing.php')) ?>">Pricing</a>
              <a href="<?= htmlspecialchars(pov_url('list-business.php')) ?>">For Business</a>
              <a href="<?= htmlspecialchars(pov_url('why-trust.php')) ?>">Why Trust Us</a>
            </div>
          </li>
          <li class="<?= pov_active('blog', $current) ?>">
            <a href="<?= htmlspecialchars(pov_url('blog.php')) ?>">Blog <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></a>
            <div class="dropdown">
              <a href="<?= htmlspecialchars(pov_url('blog.php')) ?>">All Articles</a>
              <a href="<?= htmlspecialchars(pov_url('blog.php')) ?>?tag=hidden-gems">Hidden Gems</a>
              <a href="<?= htmlspecialchars(pov_url('blog.php')) ?>?tag=student-diaries">Student Diaries</a>
            </div>
          </li>
          <li class="<?= pov_active('about', $current) ?>"><a href="<?= htmlspecialchars(pov_url('about.php')) ?>">About Us</a></li>
          <li class="<?= pov_active('contact', $current) ?>"><a href="<?= htmlspecialchars(pov_url('contact.php')) ?>">Contact</a></li>
        </ul>
      </div>
      <div class="header-actions">
        <?php 
          $activeUser = $_SESSION['admin_user'] ?? $_SESSION['auth_user'] ?? null;
          $isAdmin = !empty($_SESSION['admin_user']) || (($activeUser['role'] ?? '') === 'admin');
        ?>
        <?php if ($activeUser): ?>
          <div class="user-nav-item">
            <button type="button" class="user-pill-btn" aria-label="User Menu">
              <span class="user-avatar-circle">
                <?= htmlspecialchars(strtoupper(substr($activeUser['name'] ?? 'U', 0, 1))) ?>
              </span>
              <span class="user-pill-name"><?= htmlspecialchars($activeUser['name'] ?? 'Account') ?></span>
              <i class="fa-solid fa-chevron-down user-pill-chevron"></i>
            </button>
            <div class="user-menu-dropdown">
              <div class="user-menu-header">
                <div class="user-menu-name"><?= htmlspecialchars($activeUser['name'] ?? 'User') ?></div>
                <div class="user-menu-email"><?= htmlspecialchars($activeUser['email'] ?? '') ?></div>
                <span class="user-menu-badge <?= $isAdmin ? 'admin' : '' ?>">
                  <?= $isAdmin ? 'SuperAdmin' : 'Community Member' ?>
                </span>
              </div>
              <?php if ($isAdmin): ?>
                <a href="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" class="user-menu-item">
                  <i class="fa-solid fa-gauge-high" style="color: var(--primary);"></i> Admin Dashboard
                </a>
              <?php endif; ?>
              <a href="<?= htmlspecialchars(pov_url('listings.php')) ?>" class="user-menu-item">
                <i class="fa-solid fa-compass" style="color: #3b82f6;"></i> Browse Listings
              </a>
              <button type="button" class="user-menu-item" data-open-edit-interests style="background:none; border:none; width:100%; text-align:left; cursor:pointer; font-family:inherit; font-size:inherit;">
                <i class="fa-solid fa-sliders" style="color: #f59e0b;"></i> Edit Interests
              </button>
              <a href="<?= htmlspecialchars(pov_url('list-business.php')) ?>" class="user-menu-item">
                <i class="fa-solid fa-plus-circle" style="color: #10b981;"></i> List a Business
              </a>
              <a href="<?= htmlspecialchars(pov_url('logout.php')) ?>" class="user-menu-item logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
              </a>
            </div>
          </div>
        <?php else: ?>
          <button class="icon-btn" type="button" data-open-signup aria-label="Sign In or Sign Up"><i class="fa-regular fa-user"></i></button>
        <?php endif; ?>
        <a class="btn btn-add-listing" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><i class="fa-solid fa-plus"></i> Add Listing</a>
      </div>
    </div>
  </div>
</header>
