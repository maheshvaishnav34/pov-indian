<?php
$hero_eyebrow = $hero_eyebrow ?? '';
$hero_title = $hero_title ?? ($page_title ?? '');
$hero_sub = $hero_sub ?? '';
$crumbs = $breadcrumbs ?? [];
$hero_image = $hero_image ?? 'assets/img/ui/banner.webp';
$hero_image_url = (preg_match('#^(https?:)?//#i', $hero_image) || str_starts_with($hero_image, 'data:image')) ? $hero_image : str_replace(' ', '%20', pov_url($hero_image));
?>
<section class="page-hero" style="--page-hero-image: url('<?= htmlspecialchars($hero_image_url, ENT_QUOTES) ?>');">
  <div class="container">
    <?php if ($crumbs): ?>
    <nav class="breadcrumbs" aria-label="Breadcrumb">
      <a href="<?= htmlspecialchars(pov_url('index.php')) ?>">Home</a>
      <?php foreach ($crumbs as $label => $url): ?>
        <span>/</span>
        <?php if ($url): ?>
          <a href="<?= htmlspecialchars(pov_url($url)) ?>"><?= htmlspecialchars($label) ?></a>
        <?php else: ?>
          <span class="current"><?= htmlspecialchars($label) ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>
    <?php if ($hero_eyebrow): ?><p class="eyebrow"><?= htmlspecialchars($hero_eyebrow) ?></p><?php endif; ?>
    <h1 class="page-hero-title"><?= htmlspecialchars($hero_title) ?></h1>
    <?php if ($hero_sub): ?><p class="page-hero-sub"><?= htmlspecialchars($hero_sub) ?></p><?php endif; ?>
  </div>
</section>
