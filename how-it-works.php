<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'How It Works | POV Indian';
$page_description = 'Your journey with POV Indian — explore, trust local POV, and share.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'How It Works';
$hero_sub = 'A simple path from discovery to contribution.';
$breadcrumbs = [
  'How It Works' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/process.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container">
    <div class="process-grid">
      <article class="process-card content-card">
        <div class="process-num">01</div>
        <h3>Explore Categories</h3>
        <p>Browse hotels, restaurants, colleges, wellness, real estate, travel, and more across India.</p>
      </article>
      <article class="process-card content-card">
        <div class="process-num">02</div>
        <h3>Trust Local POV</h3>
        <p>Read verified listings and authentic insights from locals, students, and travelers.</p>
      </article>
      <article class="process-card content-card">
        <div class="process-num">03</div>
        <h3>Share &amp; Connect</h3>
        <p>List your business or become a contributor and join the POV Indian community.</p>
      </article>
    </div>
    <div class="see-all" style="margin-top:40px;">
      <a class="btn" href="<?= htmlspecialchars(pov_url('listings.php')) ?>">Explore Listings</a>
      <a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('contributor.php')) ?>" style="margin-left:10px;">Become a Contributor</a>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
