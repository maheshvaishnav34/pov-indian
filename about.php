<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'about';
$page_title = 'About Us | POV Indian';
$page_description = 'POV Indian showcases authentic perspectives on India through verified listings and local voices.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'About POV Indian';
$hero_sub = 'A platform dedicated to authentic perspectives on India — through verified businesses and local storytellers.';
$breadcrumbs = [
  'About Us' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/about.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container detail-grid">
    <div class="content-card prose">
      <h2>Our mission</h2>
      <p>POV Indian helps travelers, students, and locals discover India through people who know it best. We combine verified business listings with first-person stories so decisions feel clearer and more human.</p>
      <h2>What we stand for</h2>
      <ul>
        <li>Verified listings and transparent business profiles</li>
        <li>Local contributors with lived experience</li>
        <li>Real reviews and cultural context, not brochure copy</li>
      </ul>
      <h2>Who we serve</h2>
      <p>From heritage hotels in Jaipur to campus life in Delhi and hidden waterfalls in Kerala — POV Indian is built for anyone seeking authenticity over algorithms alone.</p>
      <p><a class="btn" href="<?= htmlspecialchars(pov_url('contributor.php')) ?>">Become a Contributor</a></p>
    </div>
    <aside class="content-card">
      <h3 style="margin-top:0;color:var(--ink);">At a glance</h3>
      <ul class="check-list">
        <li>8,500+ verified businesses</li>
        <li>500+ local contributors</li>
        <li>120,000+ verified reviews</li>
        <li>Coverage across major Indian cities</li>
      </ul>
      <a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('why-trust.php')) ?>" style="width:100%;">Why Trust Us</a>
    </aside>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
