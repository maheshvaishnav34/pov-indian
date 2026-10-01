<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'listings';
$location_slug = trim((string)($_GET['location'] ?? ''));
$search_q = trim((string)($_GET['q'] ?? ''));
$active_location = $location_slug !== '' ? pov_find_location($location_slug) : null;
$isPersonalized = pov_is_user_personalized();
$allowedCategories = pov_get_user_allowed_categories();
$base_listings = $isPersonalized ? pov_get_user_allowed_listings() : $POV_LISTINGS;

if ($active_location) {
  $locNeedle = strtolower($active_location['name']);
  $filtered_listings = array_values(array_filter($base_listings, function ($item) use ($locNeedle) {
    $itemLoc = strtolower((string)($item['loc'] ?? ''));
    return str_contains($itemLoc, $locNeedle);
  }));
} else {
  $filtered_listings = $base_listings;
}

if ($search_q !== '') {
  $needle = strtolower($search_q);
  $filtered_listings = array_values(array_filter($filtered_listings, function ($item) use ($needle) {
    $hay = strtolower(($item['title'] ?? '') . ' ' . ($item['desc'] ?? '') . ' ' . ($item['cat'] ?? '') . ' ' . ($item['loc'] ?? ''));
    return str_contains($hay, $needle);
  }));
}
$page_title = ($active_location ? $active_location['name'] . ' Listings' : 'Business Listings') . ' | POV Indian';
$page_description = $active_location
  ? 'Browse verified business listings in ' . $active_location['name'] . '.'
  : 'Browse verified business listings across India.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = $active_location ? 'Listings in ' . $active_location['name'] : 'Explore Verified Listings';
$hero_sub = $active_location
  ? 'Handpicked businesses and experiences in ' . $active_location['name'] . ' with local insights.'
  : 'Handpicked businesses and experiences across India with local insights.';
$breadcrumbs = [
  'Listings' => $active_location ? pov_url('listings.php') : null,
];
if ($active_location) {
  $breadcrumbs[$active_location['name']] = null;
}
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/listings.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container layout-2">
    <aside class="layout-sidebar content-card">
      <div class="filter-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <h4 style="margin:0;">Categories</h4>
          <?php if ($isPersonalized): ?>
            <button type="button" data-open-edit-interests style="background:none; border:none; color:var(--primary); font-size:12px; font-weight:600; cursor:pointer; padding:0;">
              <i class="fa-solid fa-sliders"></i> Edit
            </button>
          <?php endif; ?>
        </div>
        <div class="filter-list">
          <?php foreach ($allowedCategories as $cat): ?>
            <?php
              $catMatchCount = 0;
              foreach ($base_listings as $bl) {
                if (pov_category_matches_interest($cat, $bl['cat_slug'] ?? '') || pov_category_matches_interest($cat, $bl['cat'] ?? '')) {
                  $catMatchCount++;
                }
              }
              $displayCatCount = $isPersonalized ? $catMatchCount : ($cat['count'] ?? $catMatchCount);
            ?>
            <a href="<?= htmlspecialchars(pov_url(pov_category_page($cat['slug']))) ?>">
              <span><?= htmlspecialchars($cat['name']) ?></span>
              <span><?= htmlspecialchars($displayCatCount) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="filter-box">
        <h4>Locations</h4>
        <div class="filter-list">
          <a href="<?= htmlspecialchars(pov_url('listings.php')) ?>"<?= !$active_location ? ' class="is-active"' : '' ?>>
            <span>All India</span>
            <span><?= count($base_listings) ?></span>
          </a>
          <?php foreach (array_slice($POV_LOCATIONS, 0, 7) as $loc): ?>
            <?php
              $locMatchCount = 0;
              $locNameLower = strtolower($loc['name']);
              foreach ($base_listings as $bl) {
                if (str_contains(strtolower((string)($bl['loc'] ?? '')), $locNameLower)) {
                  $locMatchCount++;
                }
              }
              $displayLocCount = $isPersonalized ? $locMatchCount : ($loc['count'] ?? $locMatchCount);
            ?>
            <a href="<?= htmlspecialchars(pov_url('listings.php?location=' . urlencode($loc['slug']))) ?>"<?= ($active_location && $active_location['slug'] === $loc['slug']) ? ' class="is-active"' : '' ?>>
              <span><?= htmlspecialchars($loc['name']) ?></span>
              <span><?= htmlspecialchars($displayLocCount) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>
    <div>
      <?php if ($isPersonalized): ?>
        <div style="margin-bottom: 20px; padding: 12px 18px; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
          <span style="color: #9a3412; font-size: 13.5px; font-weight: 500;">
            <i class="fa-solid fa-sparkles" style="color: var(--primary); margin-right: 6px;"></i>
            Showing listings matching your selected interests (<?= count($allowedCategories) ?> categories)
          </span>
          <button type="button" class="btn btn-outline" data-open-edit-interests style="padding: 5px 14px; font-size: 12px; background: #fff;">
            Edit Interests
          </button>
        </div>
      <?php endif; ?>

      <div class="listings-head" style="margin-bottom:24px;">
        <div>
          <h2 class="section-title" style="font-size:28px;"><?= $active_location ? htmlspecialchars($active_location['name'] . ' Listings') : ($isPersonalized ? 'Your Verified Listings' : 'All Listings') ?></h2>
          <p class="section-sub">Showing <?= count($filtered_listings) ?> verified result<?= count($filtered_listings) === 1 ? '' : 's' ?><?= $active_location ? ' in ' . htmlspecialchars($active_location['name']) : '' ?></p>
        </div>
      </div>
      <?php if (!$filtered_listings): ?>
        <div class="content-card" style="text-align: center; padding: 48px 24px;">
          <div style="width: 52px; height: 52px; border-radius: 50%; background: #fef3c7; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: #d97706; font-size: 22px;">
            <i class="fa-solid fa-filter"></i>
          </div>
          <h3 style="margin: 0 0 8px; font-size: 18px;">No Listings Match Your Feed</h3>
          <p style="color: #64748b; font-size: 14px; margin: 0 0 16px;">
            There are no verified listings matching your selected categories in this view.
          </p>
          <button type="button" class="btn" data-open-edit-interests>Add More Categories</button>
        </div>
      <?php else: ?>
      <div class="listing-grid page-listings">
        <?php foreach ($filtered_listings as $item): ?>
          <?php
            $badge = '';
            if ($item['badge'] === 'featured') $badge = '<span class="badge featured">Featured</span>';
            elseif ($item['badge'] === 'top') $badge = '<span class="badge top">Top</span>';
            elseif ($item['badge'] === 'bump') $badge = '<span class="badge bump">Verified</span>';
          ?>
          <article class="listing-card">
            <div class="listing-thumb">
              <img src="<?= htmlspecialchars(pov_img_url($item['img'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" />
              <?= $badge ?>
              <button class="fav-btn" type="button" aria-label="Add to favourites"><i class="fa-regular fa-heart"></i></button>
              <span class="author-chip"><img src="<?= htmlspecialchars(pov_avatar_url($item['avatar'] ?? '')) ?>" alt="logo" /></span>
            </div>
            <div class="listing-body">
              <div class="listing-meta"><span class="listing-cat"><?= htmlspecialchars($item['cat']) ?></span><span class="listing-rating">★ <?= htmlspecialchars($item['rating']) ?></span></div>
              <h3><a href="<?= htmlspecialchars(pov_url('listing-detail.php?id=' . $item['id'])) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <ul class="listing-info">
                <li><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($item['loc']) ?></li>
                <li><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($item['phone']) ?></li>
              </ul>
              <div class="listing-price"><?= htmlspecialchars($item['price']) ?></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
