<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/form-store.php';
$current_page = 'contact';
$page_title = 'Contact | POV Indian';
$page_description = 'Get in touch with the POV Indian team.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'Contact Us';
$hero_sub = 'Questions about listings, partnerships, or contributions? We are here to help.';
$breadcrumbs = [
  'Contact' => null
];
$form_success = pov_flash_get('form_success');
$form_error = pov_flash_get('form_error');
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/contact.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container contact-grid">
    <div class="content-card">
      <h2 class="section-title" style="font-size:26px;">Send a message</h2>
      <?php if ($form_success): ?><p class="form-alert form-alert--ok"><?= htmlspecialchars($form_success) ?></p><?php endif; ?>
      <?php if ($form_error): ?><p class="form-alert form-alert--err"><?= htmlspecialchars($form_error) ?></p><?php endif; ?>
      <form method="post" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>" class="prose" style="margin-top:18px;">
        <input type="hidden" name="form_type" value="contact" />
        <input type="hidden" name="redirect" value="contact.php" />
        <div class="form-grid-2">
          <div class="form-row"><label for="cName">Full Name</label><input class="form-control" id="cName" name="name" required /></div>
          <div class="form-row"><label for="cEmail">Email</label><input class="form-control" id="cEmail" name="email" type="email" required /></div>
          <div class="form-row full"><label for="cSubject">Subject</label><input class="form-control" id="cSubject" name="subject" required /></div>
          <div class="form-row full"><label for="cMessage">Message</label><textarea class="form-control" id="cMessage" name="message" required></textarea></div>
        </div>
        <button class="btn" type="submit">Send Message</button>
      </form>
    </div>
    <div class="content-card">
      <h2 class="section-title" style="font-size:26px;">Reach us</h2>
      <ul class="contact-list">
        <li><i class="fa-solid fa-envelope"></i><div><strong>Email</strong><br />hello@povindian.com</div></li>
        <li><i class="fa-solid fa-phone"></i><div><strong>Phone</strong><br />1800 6565 222</div></li>
        <li><i class="fa-solid fa-location-dot"></i><div><strong>India</strong><br />Serving communities across major cities</div></li>
      </ul>
      <p style="margin-top:22px;"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>">List Your Business</a></p>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
