<?php
$title = $page_title ?? 'POV Indian - Authentic Perspectives on India';
$description = $page_description ?? 'Explore verified business listings and insightful content about Indian culture, tourism, education, and more.';
$keywords = $page_keywords ?? 'POV Indian, business directory, verified listings India, travel, tourism, stays';
$canonical = $page_canonical ?? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000') . ($_SERVER['REQUEST_URI'] ?? ''));
$ogImage = $page_og_image ?? pov_url('assets/img/ui/pov-social-banner.jpg');
$ogType = $page_og_type ?? 'website';
$body_class = $body_class ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- Primary Meta Tags -->
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="title" content="<?= htmlspecialchars($title) ?>" />
  <meta name="description" content="<?= htmlspecialchars($description) ?>" />
  <?php if (!empty($keywords)): ?>
  <meta name="keywords" content="<?= htmlspecialchars($keywords) ?>" />
  <?php endif; ?>
  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>" />
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>" />
  <meta property="og:title" content="<?= htmlspecialchars($title) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($description) ?>" />
  <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>" />
  <meta property="og:site_name" content="POV Indian" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:url" content="<?= htmlspecialchars($canonical) ?>" />
  <meta name="twitter:title" content="<?= htmlspecialchars($title) ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>" />
  <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>" />

  <?php if (!empty($page_schema_json)): ?>
  <!-- Schema.org Structured Data (JSON-LD) -->
  <script type="application/ld+json">
  <?= is_array($page_schema_json) ? json_encode($page_schema_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $page_schema_json ?>
  </script>
  <?php endif; ?>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400;1,9..144,600&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
  <link rel="stylesheet" href="<?= htmlspecialchars(pov_url('css/main.css')) ?>?v=20260928c" />
  <link rel="stylesheet" href="<?= htmlspecialchars(pov_url('css/pages.css')) ?>?v=20260928c" />
  <?php if (!empty($extra_css)): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars(pov_url($extra_css)) ?>?v=20260928c" />
  <?php endif; ?>
  <link rel="icon" href="<?= htmlspecialchars(pov_url('assets/img/ui/pov-logo-dark.svg')) ?>" />
</head>
<body class="<?= htmlspecialchars($body_class) ?>" data-base="<?= htmlspecialchars(str_replace(' ', '%20', $pov_base ?? '')) ?>">
