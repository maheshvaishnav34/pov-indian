<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'Why Trust POV Indian';
$page_description = 'Verified listings, local voices, and real experiences.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'Why Trust POV Indian?';
$hero_sub = "We're committed to providing authentic, verified information about India from those who know it best.";
$breadcrumbs = [
  'Why Trust Us' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/trust.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container">
    <div class="trust-grid">
      <article class="feature-card content-card" style="grid-template-columns:1fr;text-align:center;">
        <div class="feature-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-shield-halved"></i></div>
        <h3>Verified Listings</h3>
        <p>Every business undergoes a thorough verification process to ensure authenticity and quality.</p>
        <strong class="stat-inline">8,500+ verified businesses</strong>
      </article>
      <article class="feature-card content-card" style="grid-template-columns:1fr;text-align:center;">
        <div class="feature-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-microphone-lines"></i></div>
        <h3>Local Voices</h3>
        <p>Content created by locals and experts with firsthand experience and deep cultural understanding.</p>
        <strong class="stat-inline">500+ local contributors</strong>
      </article>
      <article class="feature-card content-card" style="grid-template-columns:1fr;text-align:center;">
        <div class="feature-icon" style="margin:0 auto 12px;"><i class="fa-solid fa-star"></i></div>
        <h3>Real Experiences</h3>
        <p>Authentic reviews and ratings from real users who experienced the businesses and destinations.</p>
        <strong class="stat-inline">120,000+ verified reviews</strong>
      </article>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
