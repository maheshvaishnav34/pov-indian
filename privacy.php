<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'Privacy Policy | POV Indian';
$page_description = 'How POV Indian collects, uses, and protects personal information.';
$body_class = 'inner-page';
$hero_eyebrow = 'Legal';
$hero_title = 'Privacy Policy';
$hero_sub = 'How we handle personal data for visitors, members, contributors, and business partners.';
$breadcrumbs = [
  'Privacy Policy' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/legal.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container legal-wrap">
    <article class="content-card prose">
      <h2>Privacy Policy</h2>
      <p>Last updated: July 2026. This policy explains what information POV Indian collects and how we use it. We do not sell personal data.</p>

      <h3>Information we collect</h3>
      <ul>
        <li><strong>Account &amp; profile data</strong> — name, email, and interests when you sign up or join the community</li>
        <li><strong>Business enquiry data</strong> — business name, category, city, phone, and description when you request a listing</li>
        <li><strong>Contact &amp; contribution data</strong> — messages, article pitches, and newsletter emails you send us</li>
        <li><strong>Usage data</strong> — pages viewed, device/browser type, and approximate location derived from IP for security and analytics</li>
      </ul>

      <h3>How we use information</h3>
      <ul>
        <li>Operate and improve the directory, search, and editorial experience</li>
        <li>Respond to enquiries and process listing or contributor requests</li>
        <li>Send service updates and, where you opt in, newsletters</li>
        <li>Prevent fraud, abuse, and security incidents</li>
      </ul>

      <h3>Sharing</h3>
      <p>We share data only with service providers who help us host, email, or secure the platform, or when required by law. We do not sell personal information to advertisers.</p>

      <h3>Retention &amp; security</h3>
      <p>We keep information only as long as needed for the purposes above or to meet legal obligations. We apply reasonable technical and organisational safeguards; no method of transmission is 100% secure.</p>

      <h3>Your choices</h3>
      <p>You may request access, correction, or deletion of personal data we hold about you, and you may unsubscribe from marketing emails at any time. Contact privacy@povindian.com or use the <a href="<?= htmlspecialchars(pov_url('contact.php')) ?>">Contact</a> page.</p>

      <h3>Cookies</h3>
      <p>See our <a href="<?= htmlspecialchars(pov_url('cookies.php')) ?>">Cookie Policy</a> for details on essential and optional cookies.</p>
    </article>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
