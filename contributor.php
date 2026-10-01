<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/form-store.php';
$current_page = 'pages';
$page_title = 'Become a Contributor | POV Indian';
$page_description = 'Share your authentic perspectives and experiences about India.';
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'Share Your POV';
$hero_sub = 'Join our community of contributors and share authentic perspectives on India.';
$breadcrumbs = [
  'Become a Contributor' => null
];
$form_success = pov_flash_get('form_success');
$form_error = pov_flash_get('form_error');
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/community.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container contact-grid">
    <div class="content-card prose">
      <h2>Why contribute?</h2>
      <ul>
        <li>Become a verified contributor</li>
        <li>Share stories, guides, and insights</li>
        <li>Connect with a community of India enthusiasts</li>
      </ul>
      <p>From student diaries to hidden gems and cultural deep-dives — if you have a real POV, we want it.</p>
      <button class="btn" type="button" data-open-signup>Join Community</button>
    </div>
    <div class="content-card">
      <h2 class="section-title" style="font-size:26px;">Pitch an article</h2>
      <?php if ($form_success): ?><p class="form-alert form-alert--ok"><?= htmlspecialchars($form_success) ?></p><?php endif; ?>
      <?php if ($form_error): ?><p class="form-alert form-alert--err"><?= htmlspecialchars($form_error) ?></p><?php endif; ?>
      <form method="post" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>" style="margin-top:18px;">
        <input type="hidden" name="form_type" value="contributor" />
        <input type="hidden" name="redirect" value="contributor.php" />
        <div class="form-grid-2">
          <div class="form-row"><label for="aName">Your Name</label><input class="form-control" id="aName" name="name" required /></div>
          <div class="form-row"><label for="aEmail">Email</label><input class="form-control" id="aEmail" name="email" type="email" required /></div>
          <div class="form-row full"><label for="aTopic">Topic</label><input class="form-control" id="aTopic" name="topic" required /></div>
          <div class="form-row full"><label for="aPitch">Pitch</label><textarea class="form-control" id="aPitch" name="pitch" required></textarea></div>
        </div>
        <button class="btn" type="submit">Submit Pitch</button>
      </form>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
