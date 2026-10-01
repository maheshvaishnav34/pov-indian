<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$item = pov_find_listing($id);
$current_page = 'listings';
$body_class = 'inner-page';

if (!$item) {
  $page_title = 'Listing not found | POV Indian';
  $page_description = 'This listing could not be found.';
  $hero_eyebrow = 'Listings';
  $hero_title = 'Listing not found';
  $hero_sub = 'The listing you requested is unavailable or may have been removed.';
  $breadcrumbs = ['Listings' => 'listings.php', 'Not found' => null];
  include __DIR__ . '/includes/head.php';
  include __DIR__ . '/includes/header.php';
  $hero_image = 'assets/img/heroes/listings.jpg';
  include __DIR__ . '/includes/page-hero.php';
  ?>
  <main class="page-section">
    <div class="container">
      <div class="content-card" style="max-width:640px;">
        <p>We could not find a listing with that ID. Browse verified results or try another category.</p>
        <p style="margin-top:18px;">
          <a class="btn" href="<?= htmlspecialchars(pov_url('listings.php')) ?>">All Listings</a>
          <a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('categories.php')) ?>">Categories</a>
        </p>
      </div>
    </div>
  </main>
  <?php
  include __DIR__ . '/includes/footer.php';
  exit;
}

$profile = pov_listing_profile((string)($item['cat_slug'] ?? ''));
$gallery = pov_listing_gallery($item);
$reviews = pov_listing_reviews($item);
$related = pov_related_listings($item, 3);
$cat_page = !empty($item['cat_slug']) ? pov_category_page($item['cat_slug']) : 'categories.php';

$page_title = !empty($item['meta_title']) ? $item['meta_title'] : ($item['title'] . ' | POV Indian Verified Directory');
$page_description = !empty($item['meta_description']) ? $item['meta_description'] : $item['desc'];
$page_keywords = !empty($item['meta_keywords']) ? $item['meta_keywords'] : ($item['cat'] . ', ' . $item['loc'] . ', ' . $item['title'] . ', verified directory India');
$page_canonical = !empty($item['canonical_url']) ? $item['canonical_url'] : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000') . pov_url('listing-detail.php?id=' . $item['id']));
$page_og_image = !empty($item['og_image']) ? pov_img_url($item['og_image']) : pov_img_url($item['img']);
$page_og_type = 'business.business';

// Generate Schema.org JSON-LD Structured Data
$schemaType = !empty($item['schema_type']) ? $item['schema_type'] : 'LocalBusiness';
$page_schema_json = [
  '@context' => 'https://schema.org',
  '@type' => $schemaType,
  'name' => $item['title'],
  'description' => $page_description,
  'image' => $page_og_image,
  'telephone' => $item['phone'] ?? '+919876543210',
  'priceRange' => $item['price'] ?? '₹₹',
  'url' => $page_canonical,
  'address' => [
    '@type' => 'PostalAddress',
    'addressLocality' => $item['loc'] ?? 'India',
    'addressCountry' => 'IN'
  ],
  'aggregateRating' => [
    '@type' => 'AggregateRating',
    'ratingValue' => (float)($item['rating'] ?? 5.0),
    'reviewCount' => count($reviews) ?: 12,
    'bestRating' => '5',
    'worstRating' => '1'
  ]
];

$hero_eyebrow = $item['cat'];
$hero_title = $item['title'];
$hero_sub = $item['loc'] . ' · Rated ' . $item['rating'] . '/5';
$breadcrumbs = ['Listings' => 'listings.php', $item['title'] => null];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = pov_img_url($item['img']);
include __DIR__ . '/includes/page-hero.php';
?>
<main class="page-section">
  <div class="container detail-grid">
    <div>
      <div class="detail-gallery">
        <img src="<?= htmlspecialchars(pov_img_url($gallery[0])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" />
        <?php if (count($gallery) > 1): ?>
        <div class="detail-thumbs">
          <?php foreach (array_slice($gallery, 1) as $thumb): ?>
            <img src="<?= htmlspecialchars(pov_img_url($thumb)) ?>" alt="" />
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="content-card" style="margin-top:22px;">
        <h2 class="section-title" style="font-size:24px;">About this listing</h2>
        <div class="prose">
          <p><?= htmlspecialchars($item['desc']) ?></p>
          <p>Every business on POV Indian goes through verification so travelers, students, and locals can trust what they see. Contact the host directly, ask about availability, and share your experience after your visit.</p>
        </div>
        <?php if (!empty($profile['highlights'])): ?>
        <ul class="detail-highlights">
          <?php foreach ($profile['highlights'] as $line): ?>
            <li><i class="fa-solid fa-check"></i> <?= htmlspecialchars($line) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div class="content-card" style="margin-top:22px;">
        <h2 class="section-title" style="font-size:24px;">Amenities &amp; practical info</h2>
        <p class="detail-hours"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($profile['hours']) ?></p>
        <ul class="detail-amenity-grid">
          <?php foreach ($profile['amenities'] as $amenity): ?>
            <li><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($amenity) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="content-card" style="margin-top:22px;">
        <h2 class="section-title" style="font-size:24px;">Guest notes</h2>
        <div class="detail-reviews">
          <?php foreach ($reviews as $review): ?>
            <blockquote class="detail-review">
              <p>“<?= htmlspecialchars($review['text']) ?>”</p>
              <footer>— <?= htmlspecialchars($review['name']) ?>, <?= htmlspecialchars($review['city']) ?></footer>
            </blockquote>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <aside class="content-card detail-panel">
      <?php if (!empty($item['badge'])): ?>
        <p class="detail-badge">
          <?php if ($item['badge'] === 'featured'): ?>Featured<?php elseif ($item['badge'] === 'top'): ?>Top rated<?php else: ?>Verified<?php endif; ?>
        </p>
      <?php endif; ?>
      <div class="listing-price"><?= htmlspecialchars($item['price']) ?></div>
      <ul class="detail-meta-list">
        <li><i class="fa-solid fa-layer-group"></i> <?= htmlspecialchars($item['cat']) ?></li>
        <li><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($item['loc']) ?></li>
        <li><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($item['phone']) ?></li>
        <li><i class="fa-solid fa-star"></i> <?= htmlspecialchars($item['rating']) ?> / 5 rating</li>
        <li><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($profile['hours']) ?></li>
      </ul>
      <a class="btn" href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $item['phone'])) ?>" style="width:100%;margin-bottom:10px;">Call Now</a>
      <a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('contact.php')) ?>" style="width:100%;margin-bottom:10px;">Send Enquiry</a>
      <a class="btn btn-outline" href="<?= htmlspecialchars(pov_url($cat_page)) ?>" style="width:100%;">More in <?= htmlspecialchars($item['cat']) ?></a>
      <div class="detail-host">
        <img src="<?= htmlspecialchars(pov_avatar_url($item['avatar'] ?? '')) ?>" alt="logo" />
        <div>
          <strong>Listed host</strong>
          <span>Verified on POV Indian</span>
        </div>
      </div>
    </aside>
  </div>

  <?php if ($related): ?>
  <div class="container" style="margin-top:40px;">
    <div class="listings-head" style="margin-bottom:20px;">
      <h2 class="section-title" style="font-size:28px;">Related listings</h2>
      <p class="section-sub">More verified options in the same category</p>
    </div>
    <div class="listing-grid page-listings">
      <?php foreach ($related as $rel): ?>
        <article class="listing-card">
          <div class="listing-thumb">
            <img src="<?= htmlspecialchars(pov_img_url($rel['img'])) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" />
          </div>
          <div class="listing-body">
            <div class="listing-meta"><span class="listing-cat"><?= htmlspecialchars($rel['cat']) ?></span><span class="listing-rating">★ <?= htmlspecialchars($rel['rating']) ?></span></div>
            <h3><a href="<?= htmlspecialchars(pov_url('listing-detail.php?id=' . $rel['id'])) ?>"><?= htmlspecialchars($rel['title']) ?></a></h3>
            <ul class="listing-info">
              <li><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($rel['loc']) ?></li>
            </ul>
            <div class="listing-price"><?= htmlspecialchars($rel['price']) ?></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
