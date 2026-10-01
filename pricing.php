<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'Pricing | POV Indian';
$page_description = 'Business visibility plans for Indian brands on POV Indian.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'Business Visibility Plans';
$hero_sub = 'Grow your reach with travelers, students, and local consumers on POV Indian.';
$breadcrumbs = [
  'Pricing' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/pricing.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container">
    <div class="pricing-grid">
      <article class="price-card">
        <h3>Starter</h3>
        <p class="plan-desc">For new local businesses</p>
        <div class="price">₹999 <span>/Mon</span></div>
        <ul>
          <li>3 Regular listings</li>
          <li>1 Featured listing</li>
          <li>Basic business profile</li>
          <li>30 days visibility</li>
          <li>Community support</li>
        </ul>
        <a class="btn" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>" data-purchase="Starter">List Your Business</a>
      </article>
      <article class="price-card popular">
        <span class="popular-tag">Popular</span>
        <h3>Growth</h3>
        <p class="plan-desc">For growing brands</p>
        <div class="price">₹2,499 <span>/Mon</span></div>
        <ul>
          <li>20 Regular listings</li>
          <li>5 Featured listings</li>
          <li>Verified business badge</li>
          <li>90 days visibility</li>
          <li>Priority placement</li>
          <li>Photo-rich profile</li>
        </ul>
        <a class="btn" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>" data-purchase="Growth">List Your Business</a>
      </article>
      <article class="price-card">
        <h3>Enterprise</h3>
        <p class="plan-desc">For multi-location brands</p>
        <div class="price">₹7,999 <span>/Mon</span></div>
        <ul>
          <li>Unlimited listings</li>
          <li>Priority featured slots</li>
          <li>Dedicated support</li>
          <li>365 days visibility</li>
          <li>Campaign boosts</li>
        </ul>
        <a class="btn" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>" data-purchase="Enterprise">List Your Business</a>
      </article>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
