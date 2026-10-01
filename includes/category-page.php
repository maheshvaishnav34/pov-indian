<?php
if (empty($category_slug)) {
  http_response_code(404);
  echo 'Category not found';
  exit;
}
$cat = pov_find_category($category_slug);
if (!$cat) {
  http_response_code(404);
  echo 'Category not found';
  exit;
}
$landing = pov_category_landing($category_slug);
$filtered = pov_listings_by_category($category_slug);
$others = pov_other_categories($category_slug, 6);
$cities = array_slice($POV_LOCATIONS, 0, 7);

$headline = $landing['headline'] ?? $cat['name'];
$lead = $landing['lead'] ?? ($cat['desc'] ?? '');
$audience = $landing['audience'] ?? ($cat['desc'] ?? '');
$niches = $landing['niches'] ?? [];
$benefits = $landing['benefits'] ?? [];
$stats = $landing['stats'] ?? [];
$process = $landing['process'] ?? [];
$seeker_title = $landing['seeker_title'] ?? ('Explore ' . $cat['name']);
$seeker_text = $landing['seeker_text'] ?? $lead;
$business_title = $landing['business_title'] ?? 'List your business';
$business_text = $landing['business_text'] ?? 'Reach customers searching this category on POV Indian.';
$landing['cta_primary'] = $landing['cta_primary'] ?? 'Explore Listings';
$landing['cta_secondary'] = $landing['cta_secondary'] ?? 'List Your Business';

$current_page = 'listings';
$page_title = $headline . ' | POV Indian';
$page_description = $lead;
$body_class = 'home-page category-landing';
include __DIR__ . '/head.php';
include __DIR__ . '/header.php';
?>
<main>
  <?php include __DIR__ . '/category-hero.php'; ?>

  <section class="section" id="about">
    <div class="container about-split">
      <div class="about-copy">
        <h2 class="script-title"><?= htmlspecialchars($cat['name']) ?></h2>
        <p><?= htmlspecialchars($audience) ?></p>
        <a class="btn" href="<?= htmlspecialchars(pov_url('why-trust.php')) ?>">Why Trust POV Indian?</a>
      </div>
      <div class="about-media">
        <img src="<?= htmlspecialchars(pov_url($cat['hero'] ?? 'assets/img/ui/video-bg.webp')) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" style="border-radius:20px;width:100%;" />
      </div>
    </div>
  </section>

  <?php if ($niches): ?>
  <section class="section" id="categories" style="padding-top:0;">
    <div class="container">
      <div class="cats-head">
        <h2 class="section-title">Explore <?= htmlspecialchars($cat['name']) ?></h2>
        <p class="section-sub">Specialised paths inside this business niche</p>
      </div>
      <div class="cat-grid cat-grid-6">
        <?php foreach ($niches as $niche): ?>
          <a class="cat-card" href="#listings">
            <img src="<?= htmlspecialchars(pov_url('assets/img/cats/' . $cat['icon'])) ?>" alt="" />
            <h3><?= htmlspecialchars($niche['name']) ?></h3>
            <span><?= htmlspecialchars($niche['blurb']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="section locations-section" id="locations">
    <div class="container">
      <div class="cats-head">
        <h2 class="section-title">Discover by Location</h2>
        <p class="section-sub">Explore <?= htmlspecialchars($cat['name']) ?> across popular Indian cities</p>
      </div>
      <h3 class="loc-heading">Popular Destinations</h3>
      <div class="loc-grid">
        <?php foreach ($cities as $loc): ?>
          <a class="loc-card" href="#listings">
            <h3><?= htmlspecialchars($loc['name']) ?></h3>
            <span><?= htmlspecialchars($loc['count']) ?> listings</span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="see-all"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('locations.php')) ?>">View All Locations</a></div>
    </div>
  </section>

  <section class="section benefits">
    <div class="container benefits-grid">
      <div class="benefits-media">
        <img class="main" src="<?= htmlspecialchars(pov_url($cat['hero'] ?? 'assets/img/ui/video-bg.webp')) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" />
        <div class="float-card"><img src="<?= htmlspecialchars(pov_url('assets/img/ui/logocircle.webp')) ?>" alt="" /></div>
      </div>
      <div>
        <span class="eyebrow">Why this niche</span>
        <h2 class="section-title"><?= htmlspecialchars($seeker_title) ?></h2>
        <p class="section-sub"><?= htmlspecialchars($seeker_text) ?></p>
        <ul class="check-list">
          <?php foreach ($benefits as $benefit): ?>
            <li><?= htmlspecialchars($benefit) ?></li>
          <?php endforeach; ?>
          <?php if (!$benefits): ?>
            <li>Verified business badge &amp; profiles</li>
            <li>Enhanced visibility to travelers &amp; locals</li>
            <li>Local contributors and real reviews</li>
          <?php endif; ?>
        </ul>
        <a class="btn" href="#listings">Browse Listings</a>
      </div>
    </div>
  </section>

  <?php if ($stats): ?>
  <section class="section" id="why">
    <div class="container why-grid">
      <div>
        <span class="eyebrow">Why Trust POV Indian?</span>
        <h2 class="section-title">Authentic, verified <?= htmlspecialchars(strtolower($cat['name'])) ?> from those who know India best</h2>
        <p class="section-sub" style="margin-bottom:28px;">We're committed to providing authentic perspectives through trusted listings and local voices.</p>
        <div class="feature-cards">
          <?php
            $icons = ['fa-shield-halved', 'fa-microphone-lines', 'fa-star'];
            foreach ($stats as $i => $stat):
              $icon = $icons[$i] ?? 'fa-check';
          ?>
            <article class="feature-card">
              <div class="feature-icon"><i class="fa-solid <?= $icon ?>"></i></div>
              <div>
                <h3><?= htmlspecialchars($stat['label']) ?></h3>
                <p><?= htmlspecialchars($audience) ?></p>
                <strong class="stat-inline"><?= htmlspecialchars($stat['value']) ?></strong>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="why-visual"><img src="<?= htmlspecialchars(pov_url($cat['hero'] ?? 'assets/img/blog/service.webp')) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" /></div>
    </div>
  </section>
  <?php endif; ?>

  <section class="section" id="listings">
    <div class="container">
      <div class="listings-head">
        <div>
          <h2 class="section-title">Explore Verified Listings</h2>
          <p class="section-sub">Handpicked <?= htmlspecialchars($cat['name']) ?> across India</p>
        </div>
      </div>
      <?php if (!$filtered): ?>
        <div class="empty-note">No listings in this category yet. <a href="<?= htmlspecialchars(pov_url('list-business.php')) ?>">List your business</a>.</div>
      <?php else: ?>
      <div class="listing-grid">
        <?php foreach ($filtered as $item): ?>
          <?php
            $badge = '';
            if ($item['badge'] === 'featured') $badge = '<span class="badge featured">Featured</span>';
            elseif ($item['badge'] === 'top') $badge = '<span class="badge top">Top</span>';
            elseif ($item['badge'] === 'bump') $badge = '<span class="badge bump">Verified</span>';
          ?>
          <article class="listing-card">
            <div class="listing-thumb">
              <img src="<?= htmlspecialchars(pov_img_url($item['img'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" />
              <?= $badge ?>
              <button class="fav-btn" type="button" aria-label="Add to favourites"><i class="fa-regular fa-heart"></i></button>
              <span class="author-chip"><img src="<?= htmlspecialchars(pov_avatar_url($item['avatar'] ?? '')) ?>" alt="logo" /></span>
            </div>
            <div class="listing-body">
              <div class="listing-meta"><span class="listing-cat"><?= htmlspecialchars($item['cat']) ?></span><span class="listing-rating">★ <?= htmlspecialchars($item['rating']) ?></span></div>
              <h3><a href="<?= htmlspecialchars(pov_url('listing-detail.php?id=' . $item['id'])) ?>"><?= htmlspecialchars($item['title']) ?></a></h3>
              <ul class="listing-info">
                <li><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($item['loc']) ?></li>
                <li><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($item['phone']) ?></li>
              </ul>
              <div class="listing-price"><?= htmlspecialchars($item['price']) ?></div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="see-all"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('listings.php')) ?>">Explore Listings</a></div>
    </div>
  </section>

  <?php if ($process): ?>
  <section class="section" id="process">
    <div class="container">
      <div class="cats-head">
        <span class="eyebrow">How It Works</span>
        <h2 class="section-title">Your journey with POV Indian</h2>
      </div>
      <div class="process-grid">
        <?php foreach ($process as $i => $step): ?>
          <article class="process-card">
            <div class="process-num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
            <h3><?= htmlspecialchars($step['title']) ?></h3>
            <p><?= htmlspecialchars($step['text']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="section cta-dual" id="list-business">
    <div class="container cta-grid">
      <article class="cta-card">
        <h3><?= htmlspecialchars($business_title) ?></h3>
        <p><?= htmlspecialchars($business_text) ?></p>
        <ul class="check-list">
          <li>Verified business badge</li>
          <li>Enhanced visibility to targeted audience</li>
          <li>Detailed business profile with photos</li>
        </ul>
        <a class="btn" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>">List Your Business</a>
      </article>
      <article class="cta-card dark">
        <h3>Share Your POV</h3>
        <p>Are you passionate about India? Join our community of contributors and share your authentic perspectives and experiences.</p>
        <ul class="check-list">
          <li>Become a verified contributor</li>
          <li>Share stories, guides, and insights</li>
          <li>Connect with India enthusiasts</li>
        </ul>
        <button class="btn btn-white" type="button" data-open-signup>Become a Contributor</button>
      </article>
    </div>
  </section>

  <section class="section" id="more-categories" style="padding-top:20px;">
    <div class="container">
      <div class="cats-head">
        <h2 class="section-title">Explore Business Categories</h2>
        <p class="section-sub">Discover verified local businesses across India's most sought-after sectors</p>
      </div>
      <div class="cat-grid cat-grid-6">
        <?php foreach ($others as $other): ?>
          <a class="cat-card" href="<?= htmlspecialchars(pov_url(pov_category_page($other['slug']))) ?>">
            <img src="<?= htmlspecialchars(pov_url('assets/img/cats/' . $other['icon'])) ?>" alt="" />
            <h3><?= htmlspecialchars($other['name']) ?></h3>
            <span><?= htmlspecialchars($other['count']) ?> listings</span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="see-all"><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('categories.php')) ?>">View All Categories</a></div>
    </div>
  </section>
</main>
<?php include __DIR__ . '/footer.php'; ?>
