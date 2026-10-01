<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'Terms of Service | POV Indian';
$page_description = 'Terms governing use of the POV Indian platform for explorers, contributors, and businesses.';
$body_class = 'inner-page';
$hero_eyebrow = 'Legal';
$hero_title = 'Terms of Service';
$hero_sub = 'The rules for using POV Indian as a visitor, contributor, or business listing partner.';
$breadcrumbs = [
  'Terms of Service' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/legal.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container legal-wrap">
    <article class="content-card prose">
      <h2>Terms of Service</h2>
      <p>Last updated: July 2026. By accessing POV Indian you agree to these terms. If you do not agree, please do not use the platform.</p>

      <h3>1. What POV Indian provides</h3>
      <p>POV Indian is a discovery platform for verified business listings and local perspectives across India. We publish directory information, editorial content, and community contributions. We do not operate the businesses listed and are not a party to bookings or transactions between users and businesses.</p>

      <h3>2. Accounts and accurate information</h3>
      <p>If you create an account, list a business, or submit content, you must provide accurate details and keep them updated. You are responsible for activity under your account and for safeguarding your login credentials.</p>

      <h3>3. Acceptable use</h3>
      <ul>
        <li>Do not post false, misleading, defamatory, or unlawful content</li>
        <li>Do not scrape, spam, or interfere with platform security or availability</li>
        <li>Do not use listings or contacts for harassment or unsolicited bulk marketing outside our guidelines</li>
        <li>Respect intellectual property and local community norms</li>
      </ul>

      <h3>4. Business listings</h3>
      <p>Businesses may request a listing subject to verification. We may approve, reject, edit, suspend, or remove listings that violate these terms, our <a href="<?= htmlspecialchars(pov_url('listing-guidelines.php')) ?>">Listing Guidelines</a>, or applicable law. Paid visibility plans are subject to the plan description at purchase time.</p>

      <h3>5. Contributor content</h3>
      <p>By submitting stories, guides, or pitches you grant POV Indian a non-exclusive licence to publish, edit for clarity, and distribute that content on our channels. You confirm you have the rights to share what you submit and that it follows our <a href="<?= htmlspecialchars(pov_url('content-guidelines.php')) ?>">Content Guidelines</a>.</p>

      <h3>6. Disclaimers</h3>
      <p>Listings and articles are provided for general information. Availability, pricing, and services can change without notice. Always confirm details directly with the business before you travel, enrol, or purchase.</p>

      <h3>7. Limitation of liability</h3>
      <p>To the fullest extent permitted by law, POV Indian is not liable for indirect or consequential losses arising from use of the platform, reliance on listings, or dealings with third-party businesses.</p>

      <h3>8. Changes and contact</h3>
      <p>We may update these terms periodically. Continued use after changes means you accept the revised terms. Questions: <a href="<?= htmlspecialchars(pov_url('contact.php')) ?>">Contact us</a> or email legal@povindian.com.</p>
    </article>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
