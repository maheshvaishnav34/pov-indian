<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'Listing Guidelines | POV Indian';
$page_description = 'Standards for accurate, trustworthy business listings on POV Indian.';
$body_class = 'inner-page';
$hero_eyebrow = 'Legal';
$hero_title = 'Listing Guidelines';
$hero_sub = 'What every business profile must include before it can stay live on POV Indian.';
$breadcrumbs = [
  'Listing Guidelines' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/legal.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container legal-wrap">
    <article class="content-card prose">
      <h2>Listing Guidelines</h2>
      <p>These guidelines help travellers, students, and locals trust what they find. Submitting a listing means you agree to keep it accurate and compliant.</p>

      <h3>Eligibility</h3>
      <ul>
        <li>Listings must represent a real business operating in India (or serving customers in India)</li>
        <li>You must be authorised to manage the listing on behalf of that business</li>
        <li>Duplicate or placeholder listings are not allowed</li>
      </ul>

      <h3>Required details</h3>
      <ul>
        <li>Correct business name, category, city, and working phone or email</li>
        <li>Honest description of services, pricing range, and what guests/customers can expect</li>
        <li>Photos that show the actual premises or offerings (no stock-only or misleading imagery)</li>
      </ul>

      <h3>Claims &amp; promotions</h3>
      <p>Do not exaggerate ratings, certifications, or availability. Superlatives like “best in India” need clear context. Prices shown should be current or clearly marked as approximate.</p>

      <h3>Verification</h3>
      <p>POV Indian may ask for documents, a call, or a site check before publishing or after a report. We may pause or remove listings that fail verification or receive substantiated complaints.</p>

      <h3>Prohibited content</h3>
      <ul>
        <li>Illegal services, scams, or adult content outside applicable law</li>
        <li>Hate, harassment, or discrimination</li>
        <li>Competitor sabotage or fake reviews planted via the listing profile</li>
      </ul>

      <p>Need help updating a profile? <a href="<?= htmlspecialchars(pov_url('contact.php')) ?>">Contact us</a> or start at <a href="<?= htmlspecialchars(pov_url('list-business.php')) ?>">List Your Business</a>.</p>
    </article>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
