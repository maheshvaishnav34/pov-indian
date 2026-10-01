<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'listings';
$page_title = 'Discover India by Location | POV Indian';
$page_description = 'Explore businesses and insights from different regions across India.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'Discover India by Location';
$hero_sub = 'Explore businesses and insights from different regions across India.';
$breadcrumbs = [
  'Locations' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/locations.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container">
    <h3 class="loc-heading">Popular Destinations</h3>
    <div class="loc-grid">
      <?php foreach ($POV_LOCATIONS as $loc): ?>
        <a class="loc-card" href="<?= htmlspecialchars(pov_url('listings.php?location=' . urlencode($loc['slug']))) ?>">
          <h3><?= htmlspecialchars($loc['name']) ?></h3>
          <span><?= htmlspecialchars($loc['count']) ?> listings</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
