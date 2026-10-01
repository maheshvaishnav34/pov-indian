<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/form-store.php';
$current_page = 'pages';
$page_title = 'List Your Business | POV Indian';
$page_description = 'Join thousands of verified Indian businesses on POV Indian.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'List Your Business';
$hero_sub = 'Reach travelers, students, and local consumers looking for authentic experiences.';
$breadcrumbs = [
  'List Your Business' => null
];
$form_success = pov_flash_get('form_success');
$form_error = pov_flash_get('form_error');
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/business.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container contact-grid">
    <div class="content-card">
      <h2 class="section-title" style="font-size:26px;">Business details</h2>
      <?php if ($form_success): ?><p class="form-alert form-alert--ok"><?= htmlspecialchars($form_success) ?></p><?php endif; ?>
      <?php if ($form_error): ?><p class="form-alert form-alert--err"><?= htmlspecialchars($form_error) ?></p><?php endif; ?>
      <form method="post" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>" style="margin-top:18px;">
        <input type="hidden" name="form_type" value="list-business" />
        <input type="hidden" name="redirect" value="list-business.php" />
        <div class="form-grid-2">
          <div class="form-row"><label for="bName">Business Name</label><input class="form-control" id="bName" name="business_name" required /></div>
          <div class="form-row"><label for="bCat">Category</label>
            <select class="form-control" id="bCat" name="category" required>
              <option value="">Select category</option>
              <?php foreach ($POV_CATEGORIES as $cat): ?>
                <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-row"><label for="bCity">City</label><input class="form-control" id="bCity" name="city" required /></div>
          <div class="form-row"><label for="bPhone">Phone</label><input class="form-control" id="bPhone" name="phone" required /></div>
          <div class="form-row full"><label for="bEmail">Email</label><input class="form-control" id="bEmail" name="email" type="email" required /></div>
          <div class="form-row full"><label for="bDesc">Short description</label><textarea class="form-control" id="bDesc" name="description" required></textarea></div>
        </div>
        <button class="btn" type="submit">Submit for Verification</button>
      </form>
    </div>
    <div class="content-card">
      <h2 class="section-title" style="font-size:26px;">What you get</h2>
      <ul class="check-list">
        <li>Verified business badge</li>
        <li>Enhanced visibility to targeted audience</li>
        <li>Detailed business profile with photos</li>
        <li>Access to travelers, students, and locals</li>
      </ul>
      <p style="margin-top:20px;"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('pricing.php')) ?>">View Pricing Plans</a></p>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
