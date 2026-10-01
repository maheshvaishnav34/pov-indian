<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'listings';
$page_title = 'Business Categories | POV Indian';
$page_description = "Explore verified local businesses across India's most sought-after sectors.";
$body_class = 'inner-page';
$hero_eyebrow = 'POV Indian';
$hero_title = 'Explore Business Categories';
$hero_sub = "Discover verified local businesses across India's most sought-after sectors.";
$breadcrumbs = [
  'Categories' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/categories.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container">
    <?php
      $allowedCategories = pov_get_user_allowed_categories();
      $isPersonalized = pov_is_user_personalized();
    ?>
    <?php if ($isPersonalized): ?>
      <div style="margin-bottom: 24px; padding: 14px 20px; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
          <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: #fff; font-size: 14px;">
            <i class="fa-solid fa-check"></i>
          </span>
          <div>
            <strong style="color: #9a3412; font-size: 14px; display: block;">Showing Your Chosen Categories</strong>
            <span style="color: #c2410c; font-size: 13px;">Filtered based on your sign-up interests (<?= count($allowedCategories) ?> categories)</span>
          </div>
        </div>
        <button type="button" class="btn btn-outline" data-open-edit-interests style="padding: 7px 16px; font-size: 13px; background:#fff;">
          <i class="fa-solid fa-sliders" style="margin-right: 6px;"></i> Edit Interests
        </button>
      </div>
    <?php endif; ?>

    <div class="cat-grid cat-grid-6">
      <?php if (empty($allowedCategories)): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 48px 20px; background: #fff; border-radius: 16px; border: 1px dashed #cbd5e1;">
          <p style="color: #64748b; font-size: 15px; margin: 0 0 16px;">No categories found matching your selected interests.</p>
          <button type="button" class="btn" data-open-edit-interests>Select Categories</button>
        </div>
      <?php else: ?>
        <?php foreach ($allowedCategories as $cat): ?>
          <a class="cat-card" href="<?= htmlspecialchars(pov_url(pov_category_page($cat['slug']))) ?>">
            <img src="<?= htmlspecialchars(pov_url('assets/img/cats/' . $cat['icon'])) ?>" alt="" />
            <h3><?= htmlspecialchars($cat['name']) ?></h3>
            <span><?= htmlspecialchars($cat['count']) ?> listings</span>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
