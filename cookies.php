<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'Cookie Policy | POV Indian';
$page_description = 'How POV Indian uses essential and optional cookies.';
$body_class = 'inner-page';
$hero_eyebrow = 'Legal';
$hero_title = 'Cookie Policy';
$hero_sub = 'What cookies we use, why we use them, and how you can control them.';
$breadcrumbs = [
  'Cookie Policy' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/legal.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container legal-wrap">
    <article class="content-card prose">
      <h2>Cookie Policy</h2>
      <p>Last updated: July 2026. Cookies are small text files stored on your device. POV Indian uses them to keep the site working and, with optional consent, to understand usage.</p>

      <h3>Essential cookies</h3>
      <p>These are required for core features such as session state, form submission feedback, security, and remembering basic preferences. The site may not function correctly if they are blocked.</p>

      <h3>Analytics cookies (optional)</h3>
      <p>If enabled, analytics cookies help us understand which pages are useful, where journeys drop off, and how we can improve navigation. They do not identify you by name.</p>

      <h3>Preference cookies</h3>
      <p>These remember choices such as dismissed notices so we do not ask repeatedly in the same browser.</p>

      <h3>Managing cookies</h3>
      <ul>
        <li>Use your browser settings to block or delete cookies</li>
        <li>Clearing cookies may sign you out of session-based features</li>
        <li>Questions about our cookie use: privacy@povindian.com</li>
      </ul>

      <p>For how personal data is processed more broadly, read our <a href="<?= htmlspecialchars(pov_url('privacy.php')) ?>">Privacy Policy</a>.</p>
    </article>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
