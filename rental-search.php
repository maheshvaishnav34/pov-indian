<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$current_page = 'listings';
$body_class = 'category-landing rentals-page';
$extra_css = 'css/rentals.css';

// Read search inputs
$intent = trim($_GET['intent'] ?? 'all');
$city = trim($_GET['city'] ?? 'all');
$landmark = trim($_GET['landmark'] ?? '');
$budget = trim($_GET['budget'] ?? 'all');
$q = trim($_GET['q'] ?? '');

// Primary Matches
$primary_matches = pov_get_rentals($intent, $city, $landmark, $budget, $q);
$primary_ids = array_column($primary_matches, 'id');

// Related Matches (nearby in same city or same category)
$related_matches = [];
if (!empty($landmark)) {
  $city_pool = pov_get_rentals($intent, $city, null, $budget);
  foreach ($city_pool as $item) {
    if (!in_array($item['id'], $primary_ids, true)) {
      $related_matches[] = $item;
    }
  }
} elseif ($city !== 'all') {
  $all_pool = pov_get_rentals($intent, 'all', null, $budget);
  foreach ($all_pool as $item) {
    if (!in_array($item['id'], $primary_ids, true)) {
      $related_matches[] = $item;
    }
  }
  $related_matches = array_slice($related_matches, 0, 3);
}

// Helper to determine BHK / Room format
function pov_detect_bhk_type($item) {
  $text = strtolower($item['title'] . ' ' . ($item['desc'] ?? '') . ' ' . ($item['occupancy'] ?? ''));
  if (str_contains($text, '1 bhk') || str_contains($text, '1bhk') || str_contains($text, 'studio') || str_contains($text, '1 bed')) {
    return '1bhk';
  }
  if (str_contains($text, '2 bhk') || str_contains($text, '2bhk') || str_contains($text, '2 bed')) {
    return '2bhk';
  }
  if (str_contains($text, '3 bhk') || str_contains($text, '3bhk') || str_contains($text, '3 bed')) {
    return '3bhk';
  }
  if (str_contains($text, 'single') || str_contains($text, 'private room')) {
    return 'single';
  }
  if (str_contains($text, 'sharing') || str_contains($text, 'shared') || str_contains($text, 'per bed')) {
    return 'sharing';
  }
  return 'all';
}

// Generate Heading
$search_label = [];
if (!empty($landmark)) $search_label[] = '"' . htmlspecialchars($landmark) . '"';
if ($city !== 'all') $search_label[] = htmlspecialchars($city);

$display_title = !empty($search_label)
  ? 'Stays & Rentals in ' . implode(', ', $search_label)
  : 'All Verified Accommodations Across India';

$page_title = $display_title . ' | POV Indian';
$page_description = 'Simple, verified search for flats, PGs, and stays in India.';

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

<main class="simple-search-page" style="padding-top:100px; padding-bottom:70px; background:#FAF7F2; min-height:85vh;">
  <div class="container">
    
    <!-- 1. Sleek Search Bar & Smart Filter Row -->
    <div style="background:#ffffff; border-radius:16px; border:1px solid #EAE4DC; padding:18px 20px; box-shadow:0 4px 20px rgba(0,0,0,0.04); margin-bottom:24px;">
      <form action="<?= htmlspecialchars(pov_url('rental-search.php')) ?>" method="GET" id="simpleSearchForm" style="display:flex; flex-direction:column; gap:12px;">
        
        <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
          <!-- Search input -->
          <div style="flex:1; min-width:240px; position:relative;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#94A3B8; font-size:14px;"></i>
            <input type="text" name="landmark" id="searchLandmarkInput" placeholder="Area, landmark, college, or station (e.g. Deccan Metro, Hinjewadi)..." value="<?= htmlspecialchars($landmark) ?>" style="width:100%; height:44px; padding:8px 14px 8px 38px; border-radius:10px; border:1.5px solid #CBD5E1; font-size:14px; outline:none; background:#fff;" />
          </div>

          <!-- City Dropdown -->
          <div style="min-width:140px;">
            <select name="city" id="searchCitySelect" style="width:100%; height:44px; padding:0 12px; border-radius:10px; border:1.5px solid #CBD5E1; font-size:13.5px; font-weight:700; color:#334155; background:#fff; outline:none; cursor:pointer;">
              <option value="all" <?= $city === 'all' ? 'selected' : '' ?>>All Cities</option>
              <option value="Pune" <?= strcasecmp($city, 'Pune') === 0 ? 'selected' : '' ?>>Pune</option>
              <option value="Jaipur" <?= strcasecmp($city, 'Jaipur') === 0 ? 'selected' : '' ?>>Jaipur</option>
              <option value="Bangalore" <?= strcasecmp($city, 'Bangalore') === 0 ? 'selected' : '' ?>>Bangalore</option>
              <option value="Kota" <?= strcasecmp($city, 'Kota') === 0 ? 'selected' : '' ?>>Kota</option>
              <option value="Delhi NCR" <?= strcasecmp($city, 'Delhi NCR') === 0 ? 'selected' : '' ?>>Delhi NCR</option>
              <option value="Udaipur" <?= strcasecmp($city, 'Udaipur') === 0 ? 'selected' : '' ?>>Udaipur</option>
              <option value="Goa" <?= strcasecmp($city, 'Goa') === 0 ? 'selected' : '' ?>>Goa</option>
              <option value="Nashik" <?= strcasecmp($city, 'Nashik') === 0 ? 'selected' : '' ?>>Nashik</option>
              <option value="Lucknow" <?= strcasecmp($city, 'Lucknow') === 0 ? 'selected' : '' ?>>Lucknow</option>
              <option value="Indore" <?= strcasecmp($city, 'Indore') === 0 ? 'selected' : '' ?>>Indore</option>
              <option value="Kochi" <?= strcasecmp($city, 'Kochi') === 0 ? 'selected' : '' ?>>Kochi</option>
            </select>
          </div>

          <!-- Search Button -->
          <button type="submit" style="height:44px; padding:0 24px; border-radius:10px; font-weight:700; font-size:14px; background:#D95D39; color:#fff; border:none; display:inline-flex; align-items:center; gap:8px; cursor:pointer; box-shadow:0 3px 10px rgba(217,93,57,0.25);">
            <i class="fa-solid fa-magnifying-glass"></i> Search
          </button>
        </div>

        <!-- 2. Interactive 1-Tap Filter Row (Category + BHK + Rules) -->
        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:7px; padding-top:10px; border-top:1px solid #F1ECE5;">
          <span style="font-size:11px; font-weight:800; color:#64748B; text-transform:uppercase; letter-spacing:0.8px; display:inline-flex; align-items:center; gap:5px; margin-right:4px;">
            <i class="fa-solid fa-sliders" style="color:#D95D39; font-size:12px;"></i> Filter:
          </span>
          
          <button type="button" class="s-pill <?= ($intent === 'all') ? 'active' : '' ?>" onclick="filterSimpleIntent('all', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #CBD5E1; background:<?= ($intent === 'all') ? '#142132' : '#F8FAFC' ?>; color:<?= ($intent === 'all') ? '#fff' : '#334155' ?>; font-size:12px; font-weight:700; cursor:pointer;">All Types</button>
          
          <button type="button" class="s-pill <?= ($intent === 'long_term') ? 'active' : '' ?>" onclick="filterSimpleIntent('long_term', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #CBD5E1; background:<?= ($intent === 'long_term') ? '#142132' : '#F8FAFC' ?>; color:<?= ($intent === 'long_term') ? '#fff' : '#334155' ?>; font-size:12px; font-weight:700; cursor:pointer;">Rent (Flats)</button>
          
          <button type="button" class="s-pill <?= ($intent === 'pg_shared') ? 'active' : '' ?>" onclick="filterSimpleIntent('pg_shared', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #CBD5E1; background:<?= ($intent === 'pg_shared') ? '#142132' : '#F8FAFC' ?>; color:<?= ($intent === 'pg_shared') ? '#fff' : '#334155' ?>; font-size:12px; font-weight:700; cursor:pointer;">PG & Hostels</button>
          
          <button type="button" class="s-pill <?= ($intent === 'short_stay') ? 'active' : '' ?>" onclick="filterSimpleIntent('short_stay', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #CBD5E1; background:<?= ($intent === 'short_stay') ? '#142132' : '#F8FAFC' ?>; color:<?= ($intent === 'short_stay') ? '#fff' : '#334155' ?>; font-size:12px; font-weight:700; cursor:pointer;">Stays & Havelis</button>
          
          <span style="display:inline-block; width:1px; height:18px; background:#CBD5E1; margin:0 3px;"></span>

          <!-- BHK Filter Pills -->
          <button type="button" class="s-pill-bhk" onclick="toggleBhkFilter('1bhk', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#334155; font-size:12px; font-weight:700; cursor:pointer;">1 BHK / Studio</button>
          <button type="button" class="s-pill-bhk" onclick="toggleBhkFilter('2bhk', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#334155; font-size:12px; font-weight:700; cursor:pointer;">2 BHK</button>
          <button type="button" class="s-pill-bhk" onclick="toggleBhkFilter('single', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#334155; font-size:12px; font-weight:700; cursor:pointer;">Single Room</button>
          <button type="button" class="s-pill-bhk" onclick="toggleBhkFilter('sharing', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#334155; font-size:12px; font-weight:700; cursor:pointer;">Sharing Bed</button>

          <span style="display:inline-block; width:1px; height:18px; background:#CBD5E1; margin:0 3px;"></span>

          <button type="button" class="s-pill-tag" onclick="toggleTag('pure_veg', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#065F46; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px;"><i class="fa-solid fa-leaf text-success"></i> Pure Veg</button>
          
          <button type="button" class="s-pill-tag" onclick="toggleTag('no_curfew', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#1E40AF; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px;"><i class="fa-regular fa-clock text-primary"></i> 24x7 Open</button>
          
          <button type="button" class="s-pill-tag" onclick="toggleTag('zero_brokerage', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#0F172A; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px;"><i class="fa-solid fa-handshake-simple"></i> Zero Brokerage</button>

          <button type="button" class="s-pill-tag" onclick="toggleBudget('under-15k', this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #E2E8F0; background:#F8FAFC; color:#92400E; font-size:12px; font-weight:700; cursor:pointer;">Under ₹15k</button>

          <!-- Shortlist View Filter Button -->
          <button type="button" id="savedFilterBtn" onclick="toggleSavedOnly(this)" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #FECDD3; background:#FFF1F2; color:#BE123C; font-size:12px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
            <i class="fa-solid fa-heart" style="color:#E11D48;"></i> Saved <span id="savedCountBadge" style="background:#FFE4E6; color:#9F1239; font-size:10.5px; font-weight:800; padding:1px 6px; border-radius:999px; margin-left:2px;">0</span>
          </button>

          <button type="button" onclick="resetSimpleFilters()" style="height:32px; padding:0 12px; border-radius:999px; border:1px solid #FEE2E2; background:#FEF2F2; color:#DC2626; font-size:12px; font-weight:700; cursor:pointer; margin-left:auto; display:inline-flex; align-items:center; gap:5px;">
            <i class="fa-solid fa-rotate-left"></i> Reset
          </button>
        </div>
      </form>
    </div>

    <!-- 3. Header, Sort & Share Row -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h1 style="font-family:var(--font-serif, Georgia); font-size:24px; font-weight:800; color:#142132; margin:0 0 4px;">
          <?= htmlspecialchars($display_title) ?>
        </h1>
        <p style="margin:0; font-size:13.5px; color:#64748B;">
          Showing <strong style="color:#142132;" id="simpleCountLabel"><?= count($primary_matches) + count($related_matches) ?></strong> verified accommodation<?= (count($primary_matches) + count($related_matches)) !== 1 ? 's' : '' ?>
        </p>
      </div>

      <!-- Controls: Sort Dropdown + Share Search -->
      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        
        <!-- Sort Dropdown -->
        <div style="display:flex; align-items:center; gap:6px;">
          <span style="font-size:12.5px; font-weight:700; color:#64748B;"><i class="fa-solid fa-arrow-down-short-wide"></i> Sort:</span>
          <select id="simpleSortSelect" onchange="runInstantSort(this.value)" style="height:36px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; font-weight:700; color:#334155; background:#fff; outline:none; cursor:pointer;">
            <option value="default">Recommended</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="rating">Top Rated (4.8+)</option>
          </select>
        </div>

        <!-- Share Search Link -->
        <button type="button" onclick="shareCurrentSearch()" title="Share this search with friend or family on WhatsApp" style="height:36px; padding:0 12px; border-radius:8px; background:#F0FDF4; border:1px solid #BBF7D0; color:#15803D; font-size:12.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
          <i class="fa-brands fa-whatsapp" style="font-size:15px;"></i> Share Search
        </button>

        <!-- Quick Try Suggestions -->
        <div style="display:flex; align-items:center; gap:5px; font-size:12px; color:#64748B; margin-left:6px;">
          <span style="font-weight:700;">Try:</span>
          <a href="<?= htmlspecialchars(pov_url('rental-search.php?landmark=Hinjewadi&city=Pune')) ?>" style="color:#D95D39; text-decoration:none; font-weight:600; background:#FFF5ED; padding:3px 8px; border-radius:6px;">Hinjewadi</a>
          <a href="<?= htmlspecialchars(pov_url('rental-search.php?landmark=Pune+station&city=Pune')) ?>" style="color:#D95D39; text-decoration:none; font-weight:600; background:#FFF5ED; padding:3px 8px; border-radius:6px;">Pune Station</a>
          <a href="<?= htmlspecialchars(pov_url('rental-search.php?landmark=Deccan+Metro&city=Pune')) ?>" style="color:#D95D39; text-decoration:none; font-weight:600; background:#FFF5ED; padding:3px 8px; border-radius:6px;">Deccan Metro</a>
        </div>
      </div>
    </div>

    <!-- 4. Spacious Modern Cards Grid with Exact vs Nearby Separation -->
    <div class="simple-cards-grid" id="simpleCardsContainer" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:22px;">
      
      <?php if (empty($primary_matches) && empty($related_matches)): ?>
        <div style="grid-column:1/-1; background:#ffffff; border-radius:16px; border:1px dashed #CBD5E1; padding:48px 20px; text-align:center;">
          <i class="fa-solid fa-house-chimney-crack" style="font-size:36px; color:#94a3b8; margin-bottom:12px;"></i>
          <h3 style="font-size:18px; font-weight:800; color:#142132; margin:0 0 6px;">No accommodations matched this search</h3>
          <p style="font-size:13.5px; color:#64748B; max-width:400px; margin:0 auto 16px;">Try clearing your search keyword or city filter to see all verified properties across India.</p>
          <a href="<?= htmlspecialchars(pov_url('rental-search.php')) ?>" class="btn" style="padding:8px 18px; font-size:13px; text-decoration:none; display:inline-block;">Show All Accommodations</a>
        </div>
      <?php endif; ?>

      <!-- Primary Exact Matches -->
      <?php foreach ($primary_matches as $item): ?>
        <?php 
          $waMessage = urlencode("Hello! I saw your listing '" . $item['title'] . "' (ID: #" . $item['id'] . ") on POV Indian near " . $item['landmark'] . ". Is this currently available?");
          $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $item['phone']) . "?text=" . $waMessage;
          $bhkType = pov_detect_bhk_type($item);
          $photoCount = 5 + ($item['id'] % 6);
        ?>
        <article class="simple-card-item primary-card" 
                 data-id="<?= $item['id'] ?>"
                 data-intent="<?= htmlspecialchars($item['rental_type']) ?>"
                 data-price="<?= $item['price_num'] ?>"
                 data-rating="<?= $item['rating'] ?? '4.8' ?>"
                 data-bhk="<?= $bhkType ?>"
                 data-food="<?= htmlspecialchars($item['food_policy'] ?? '') ?>"
                 data-curfew="<?= htmlspecialchars($item['gate_curfew'] ?? '') ?>"
                 data-brokerage="<?= htmlspecialchars($item['brokerage_type'] ?? '') ?>"
                 style="background:#ffffff; border-radius:16px; border:1px solid #EAE4DC; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 3px 14px rgba(0,0,0,0.03); transition:transform 0.2s, box-shadow 0.2s;">
          
          <!-- Image -->
          <div style="position:relative; height:195px; background:#e2e8f0; overflow:hidden;">
            <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>" style="display:block; width:100%; height:100%;">
              <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy" style="width:100%; height:100%; object-fit:cover;" />
            </a>
            
            <!-- Category Tag -->
            <span style="position:absolute; top:10px; left:10px; background:rgba(20,33,50,0.85); color:#fff; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px;">
              <?= htmlspecialchars($item['category']) ?>
            </span>

            <!-- Shortlist Wishlist Button -->
            <button type="button" class="wishlist-btn" onclick="toggleSimpleShortlist(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>', this, event)" aria-label="Save to shortlist" style="position:absolute; top:10px; right:10px; width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.92); border:none; display:grid; place-items:center; cursor:pointer; color:#64748B; font-size:15px; box-shadow:0 2px 8px rgba(0,0,0,0.15); transition:all 0.2s;">
              <i class="fa-regular fa-heart"></i>
            </button>
            
            <!-- POV Verified Badge -->
            <span style="position:absolute; bottom:10px; left:10px; background:#ECFDF5; color:#065F46; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px; border:1px solid #A7F3D0;">
              <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($item['trust_badge']) ?>
            </span>

            <!-- Photo Count Badge -->
            <span style="position:absolute; bottom:10px; right:10px; background:rgba(20,33,50,0.75); backdrop-filter:blur(4px); color:#fff; font-size:10.5px; font-weight:700; padding:3px 8px; border-radius:999px;">
              <i class="fa-solid fa-camera"></i> <?= $photoCount ?> Photos
            </span>
          </div>

          <!-- Body -->
          <div style="padding:16px; display:flex; flex-direction:column; flex:1;">
            
            <!-- Proximity & Locality -->
            <div style="font-size:12px; font-weight:700; color:#D95D39; margin-bottom:4px; display:flex; align-items:center; gap:5px;">
              <i class="fa-solid fa-location-dot"></i>
              <span>
                <?php
                  $locBits = [];
                  if (!empty($item['landmark_distance']) && trim($item['landmark_distance']) !== '') {
                    $locBits[] = '<strong>' . htmlspecialchars($item['landmark_distance']) . '</strong>';
                  }
                  $area = trim((string)($item['locality'] ?? $item['landmark'] ?? ''));
                  $city = trim((string)($item['city'] ?? ''));
                  if ($area !== '' && $city !== '' && strtolower($area) !== strtolower($city)) {
                    $locBits[] = htmlspecialchars($area . ', ' . $city);
                  } elseif ($area !== '') {
                    $locBits[] = htmlspecialchars($area);
                  } elseif ($city !== '') {
                    $locBits[] = htmlspecialchars($city);
                  } else {
                    $locBits[] = 'India';
                  }
                  echo implode(' • ', $locBits);
                ?>
              </span>
            </div>

            <!-- Title -->
            <h3 style="font-size:15px; font-weight:800; color:#142132; margin:0 0 8px; line-height:1.35;">
              <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>" style="color:inherit; text-decoration:none;">
                <?= htmlspecialchars($item['title']) ?>
              </a>
            </h3>

            <!-- Tags -->
            <div style="display:flex; flex-wrap:wrap; gap:5px; margin-bottom:12px;">
              <?php if (($item['food_policy'] ?? '') === 'pure_veg'): ?>
                <span style="font-size:11px; font-weight:700; background:#ECFDF5; color:#065F46; padding:2px 7px; border-radius:4px;"><i class="fa-solid fa-leaf"></i> Veg</span>
              <?php endif; ?>
              <?php if (($item['gate_curfew'] ?? '') === 'no_curfew'): ?>
                <span style="font-size:11px; font-weight:700; background:#EFF6FF; color:#1E40AF; padding:2px 7px; border-radius:4px;"><i class="fa-regular fa-clock"></i> 24x7</span>
              <?php endif; ?>
              <?php if (($item['brokerage_type'] ?? '') === 'zero_brokerage'): ?>
                <span style="font-size:11px; font-weight:700; background:#F8FAFC; color:#0F172A; border:1px solid #E2E8F0; padding:2px 7px; border-radius:4px;">0 Brokerage</span>
              <?php endif; ?>
            </div>

            <!-- Price -->
            <div style="margin-top:auto; padding-top:10px; border-top:1px solid #F1ECE5; display:flex; justify-content:space-between; align-items:baseline;">
              <div>
                <span style="font-size:18px; font-weight:800; color:#142132;"><?= htmlspecialchars($item['price']) ?></span>
                <span style="font-size:12px; color:#64748B;"><?= htmlspecialchars($item['price_unit']) ?></span>
              </div>
              <span style="font-size:11px; font-weight:600; color:#059669;"><?= htmlspecialchars($item['deposit']) ?></span>
            </div>

            <!-- Action Buttons: WhatsApp + View Details + Quick Share -->
            <div style="display:flex; gap:8px; margin-top:12px;">
              <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" style="flex:1; background:#22C55E; color:#fff; text-decoration:none; text-align:center; padding:8px 8px; border-radius:8px; font-weight:700; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:5px;">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp
              </a>

              <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>" style="flex:1; background:#FAF7F2; border:1px solid #CBD5E1; color:#142132; text-decoration:none; text-align:center; padding:8px 8px; border-radius:8px; font-weight:700; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:5px;">
                View Details
              </a>

              <button type="button" onclick="shareProperty('<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>', '<?= htmlspecialchars($item['price']) ?>', '<?= htmlspecialchars(addslashes($item['landmark_distance'])) ?>', '<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>')" title="Share this property" style="width:36px; height:36px; border-radius:8px; background:#F8FAFC; border:1px solid #E2E8F0; color:#64748B; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; transition:all 0.2s;" onmouseover="this.style.background='#EEF2F6'; this.style.color='#142132';" onmouseout="this.style.background='#F8FAFC'; this.style.color='#64748B';">
                <i class="fa-solid fa-share-nodes"></i>
              </button>
            </div>

          </div>
        </article>
      <?php endforeach; ?>

      <!-- 2. Clean Visual Divider for Nearby Options (if any) -->
      <?php if (!empty($primary_matches) && !empty($related_matches)): ?>
        <div class="nearby-section-divider" style="grid-column:1/-1; margin:16px 0 6px; padding:12px 18px; background:#FFF5ED; border:1px solid #FED7AA; border-radius:12px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <span style="width:26px; height:26px; border-radius:50%; background:#EA580C; color:#fff; display:grid; place-items:center; font-size:12px;"><i class="fa-solid fa-map-location-dot"></i></span>
            <span style="font-size:13.5px; font-weight:700; color:#9A3412;">
              More verified options nearby in <?= htmlspecialchars($city !== 'all' ? $city : 'this region') ?>
            </span>
          </div>
          <span style="font-size:12px; color:#C2410C; font-weight:600;"><?= count($related_matches) ?> nearby options</span>
        </div>
      <?php endif; ?>

      <!-- Related Matches (Nearby in city) -->
      <?php foreach ($related_matches as $item): ?>
        <?php 
          $waMessage = urlencode("Hello! I saw your listing '" . $item['title'] . "' (ID: #" . $item['id'] . ") on POV Indian. Is this available?");
          $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $item['phone']) . "?text=" . $waMessage;
          $bhkType = pov_detect_bhk_type($item);
          $photoCount = 4 + ($item['id'] % 5);
        ?>
        <article class="simple-card-item related-item" 
                 data-id="<?= $item['id'] ?>"
                 data-intent="<?= htmlspecialchars($item['rental_type']) ?>"
                 data-price="<?= $item['price_num'] ?>"
                 data-rating="<?= $item['rating'] ?? '4.7' ?>"
                 data-bhk="<?= $bhkType ?>"
                 data-food="<?= htmlspecialchars($item['food_policy'] ?? '') ?>"
                 data-curfew="<?= htmlspecialchars($item['gate_curfew'] ?? '') ?>"
                 data-brokerage="<?= htmlspecialchars($item['brokerage_type'] ?? '') ?>"
                 style="background:#ffffff; border-radius:16px; border:1px solid #EAE4DC; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 3px 14px rgba(0,0,0,0.03);">
          
          <!-- Image -->
          <div style="position:relative; height:195px; background:#e2e8f0; overflow:hidden;">
            <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>" style="display:block; width:100%; height:100%;">
              <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy" style="width:100%; height:100%; object-fit:cover;" />
            </a>
            
            <span style="position:absolute; top:10px; left:10px; background:rgba(20,33,50,0.85); color:#fff; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px;">
              <?= htmlspecialchars($item['category']) ?>
            </span>

            <!-- Shortlist Wishlist Button -->
            <button type="button" class="wishlist-btn" onclick="toggleSimpleShortlist(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>', this, event)" aria-label="Save to shortlist" style="position:absolute; top:10px; right:10px; width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.92); border:none; display:grid; place-items:center; cursor:pointer; color:#64748B; font-size:15px; box-shadow:0 2px 8px rgba(0,0,0,0.15); transition:all 0.2s;">
              <i class="fa-regular fa-heart"></i>
            </button>
            
            <span style="position:absolute; bottom:10px; left:10px; background:#FEF3C7; color:#92400E; font-size:10.5px; font-weight:700; padding:3px 8px; border-radius:999px;">
              Nearby in <?= htmlspecialchars($item['city']) ?>
            </span>

            <span style="position:absolute; bottom:10px; right:10px; background:rgba(20,33,50,0.75); backdrop-filter:blur(4px); color:#fff; font-size:10.5px; font-weight:700; padding:3px 8px; border-radius:999px;">
              <i class="fa-solid fa-camera"></i> <?= $photoCount ?> Photos
            </span>
          </div>

          <!-- Body -->
          <div style="padding:16px; display:flex; flex-direction:column; flex:1;">
            
            <div style="font-size:12px; font-weight:700; color:#D95D39; margin-bottom:4px; display:flex; align-items:center; gap:5px;">
              <i class="fa-solid fa-location-dot"></i>
              <span>
                <?php
                  $locBits = [];
                  if (!empty($item['landmark_distance']) && trim($item['landmark_distance']) !== '') {
                    $locBits[] = '<strong>' . htmlspecialchars($item['landmark_distance']) . '</strong>';
                  }
                  $area = trim((string)($item['locality'] ?? $item['landmark'] ?? ''));
                  $city = trim((string)($item['city'] ?? ''));
                  if ($area !== '' && $city !== '' && strtolower($area) !== strtolower($city)) {
                    $locBits[] = htmlspecialchars($area . ', ' . $city);
                  } elseif ($area !== '') {
                    $locBits[] = htmlspecialchars($area);
                  } elseif ($city !== '') {
                    $locBits[] = htmlspecialchars($city);
                  } else {
                    $locBits[] = 'India';
                  }
                  echo implode(' • ', $locBits);
                ?>
              </span>
            </div>

            <h3 style="font-size:15px; font-weight:800; color:#142132; margin:0 0 8px; line-height:1.35;">
              <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>" style="color:inherit; text-decoration:none;">
                <?= htmlspecialchars($item['title']) ?>
              </a>
            </h3>

            <div style="display:flex; flex-wrap:wrap; gap:5px; margin-bottom:12px;">
              <?php if (($item['food_policy'] ?? '') === 'pure_veg'): ?>
                <span style="font-size:11px; font-weight:700; background:#ECFDF5; color:#065F46; padding:2px 7px; border-radius:4px;"><i class="fa-solid fa-leaf"></i> Veg</span>
              <?php endif; ?>
              <?php if (($item['gate_curfew'] ?? '') === 'no_curfew'): ?>
                <span style="font-size:11px; font-weight:700; background:#EFF6FF; color:#1E40AF; padding:2px 7px; border-radius:4px;"><i class="fa-regular fa-clock"></i> 24x7</span>
              <?php endif; ?>
              <?php if (($item['brokerage_type'] ?? '') === 'zero_brokerage'): ?>
                <span style="font-size:11px; font-weight:700; background:#F8FAFC; color:#0F172A; border:1px solid #E2E8F0; padding:2px 7px; border-radius:4px;">0 Brokerage</span>
              <?php endif; ?>
            </div>

            <div style="margin-top:auto; padding-top:10px; border-top:1px solid #F1ECE5; display:flex; justify-content:space-between; align-items:baseline;">
              <div>
                <span style="font-size:18px; font-weight:800; color:#142132;"><?= htmlspecialchars($item['price']) ?></span>
                <span style="font-size:12px; color:#64748B;"><?= htmlspecialchars($item['price_unit']) ?></span>
              </div>
              <span style="font-size:11px; font-weight:600; color:#059669;"><?= htmlspecialchars($item['deposit']) ?></span>
            </div>

            <div style="display:flex; gap:8px; margin-top:12px;">
              <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" style="flex:1; background:#22C55E; color:#fff; text-decoration:none; text-align:center; padding:8px 8px; border-radius:8px; font-weight:700; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:5px;">
                <i class="fa-brands fa-whatsapp"></i> WhatsApp
              </a>

              <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>" style="flex:1; background:#FAF7F2; border:1px solid #CBD5E1; color:#142132; text-decoration:none; text-align:center; padding:8px 8px; border-radius:8px; font-weight:700; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:5px;">
                View Details
              </a>

              <button type="button" onclick="shareProperty('<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>', '<?= htmlspecialchars($item['price']) ?>', '<?= htmlspecialchars(addslashes($item['landmark_distance'])) ?>', '<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>')" title="Share this property" style="width:36px; height:36px; border-radius:8px; background:#F8FAFC; border:1px solid #E2E8F0; color:#64748B; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; transition:all 0.2s;" onmouseover="this.style.background='#EEF2F6'; this.style.color='#142132';" onmouseout="this.style.background='#F8FAFC'; this.style.color='#64748B';">
                <i class="fa-solid fa-share-nodes"></i>
              </button>
            </div>

          </div>
        </article>
      <?php endforeach; ?>

    </div>

    <!-- 5. Smart WhatsApp Notification Card for Area Leads -->
    <div style="background:#ffffff; border-radius:16px; border:1px solid #EAE4DC; padding:22px; margin-top:36px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:18px; box-shadow:0 3px 16px rgba(0,0,0,0.03);">
      <div style="max-width:540px;">
        <div style="display:inline-flex; align-items:center; gap:6px; background:#EFF6FF; color:#1E40AF; padding:3px 10px; border-radius:999px; font-size:11.5px; font-weight:700; margin-bottom:6px;">
          <i class="fa-solid fa-bell"></i> Instant Availability Alert
        </div>
        <h4 style="font-size:16px; font-weight:800; color:#142132; margin:0 0 4px;">
          Looking for more verified properties near <?= htmlspecialchars($landmark ?: ($city !== 'all' ? $city : 'your location')) ?>?
        </h4>
        <p style="font-size:13px; color:#64748B; margin:0;">
          Get a free WhatsApp alert the moment a fresh verified flat, PG, or homestay is listed here.
        </p>
      </div>
      <form onsubmit="submitWhatsAppAlert(event)" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <input type="tel" id="alertPhoneInput" placeholder="Enter 10-digit WhatsApp number" required pattern="[0-9]{10}" style="height:42px; padding:0 14px; border-radius:8px; border:1.5px solid #CBD5E1; font-size:13px; outline:none; min-width:230px;" />
        <button type="submit" style="height:42px; padding:0 18px; border-radius:8px; background:#22C55E; color:#fff; border:none; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
          <i class="fa-brands fa-whatsapp"></i> Alert Me Free
        </button>
      </form>
    </div>

  </div>
</main>

<!-- Multi-Platform Social Share Modal (Google, Facebook, Instagram, WhatsApp, Copy Link) -->
<!-- Clean Minimalist Social Share Modal -->
<div class="rental-modal-backdrop" id="socialShareModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:999999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:20px; max-width:420px; width:100%; padding:22px; position:relative; box-shadow:0 20px 40px -10px rgba(0,0,0,0.16); border:1px solid #E2E8F0;">
    
    <!-- Modal Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <div>
        <h3 style="font-size:17px; font-weight:700; color:#0F172A; margin:0 0 2px;">Share property</h3>
        <p style="font-size:12px; color:#64748B; margin:0;">Send this listing to friends or family</p>
      </div>
      <button type="button" onclick="closeSocialShareModal()" aria-label="Close" style="width:32px; height:32px; border-radius:50%; background:#F1F5F9; border:none; font-size:18px; line-height:1; cursor:pointer; color:#64748B; display:grid; place-items:center; transition:background 0.15s;" onmouseover="this.style.background='#E2E8F0'" onmouseout="this.style.background='#F1F5F9'">&times;</button>
    </div>

    <!-- Compact Property Snippet -->
    <div id="sharePropertyPreview" style="display:flex; align-items:center; gap:12px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:10px 12px; margin-bottom:20px;">
      <img id="sharePreviewImg" src="" alt="Property" style="width:48px; height:48px; border-radius:8px; object-fit:cover; background:#E2E8F0; flex-shrink:0;" />
      <div style="flex:1; min-width:0;">
        <h4 id="sharePreviewTitle" style="font-size:13px; font-weight:700; color:#0F172A; margin:0 0 3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Property Title</h4>
        <div style="display:flex; align-items:center; gap:6px; font-size:12px;">
          <span id="sharePreviewPrice" style="font-weight:700; color:#059669;">₹12,000</span>
          <span style="color:#CBD5E1;">•</span>
          <span id="sharePreviewLocality" style="color:#64748B; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Locality</span>
        </div>
      </div>
    </div>

    <!-- Social Channels Row (WhatsApp, Facebook, Instagram, Google, X) -->
    <div style="margin-bottom:20px;">
      <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.7px; margin:0 0 12px;">Share to</p>
      
      <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
        
        <!-- 1. WhatsApp -->
        <a id="shareWaBtn" href="#" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
          <div style="width:48px; height:48px; border-radius:50%; background:#25D366; color:#fff; display:grid; place-items:center; font-size:22px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(37,211,102,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <i class="fa-brands fa-whatsapp"></i>
          </div>
          <span style="font-size:11.5px; font-weight:600; color:#475569;">WhatsApp</span>
        </a>

        <!-- 2. Facebook -->
        <a id="shareFbBtn" href="#" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
          <div style="width:48px; height:48px; border-radius:50%; background:#1877F2; color:#fff; display:grid; place-items:center; font-size:20px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(24,119,242,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <i class="fa-brands fa-facebook-f"></i>
          </div>
          <span style="font-size:11.5px; font-weight:600; color:#475569;">Facebook</span>
        </a>

        <!-- 3. Instagram -->
        <button type="button" onclick="shareToInstagram()" style="display:flex; flex-direction:column; align-items:center; gap:7px; background:none; border:none; padding:0; cursor:pointer; flex:1; min-width:0; font-family:inherit;">
          <div style="width:48px; height:48px; border-radius:50%; background:radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); color:#fff; display:grid; place-items:center; font-size:21px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(214,36,159,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <i class="fa-brands fa-instagram"></i>
          </div>
          <span style="font-size:11.5px; font-weight:600; color:#475569;">Instagram</span>
        </button>

        <!-- 4. Google -->
        <a id="shareGoogleBtn" href="#" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
          <div style="width:48px; height:48px; border-radius:50%; background:#EA4335; color:#fff; display:grid; place-items:center; font-size:19px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(234,67,53,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <i class="fa-brands fa-google"></i>
          </div>
          <span style="font-size:11.5px; font-weight:600; color:#475569;">Google</span>
        </a>

        <!-- 5. X -->
        <a id="shareXBtn" href="#" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
          <div style="width:48px; height:48px; border-radius:50%; background:#0F172A; color:#fff; display:grid; place-items:center; font-size:18px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(15,23,42,0.25);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
            <i class="fa-brands fa-x-twitter"></i>
          </div>
          <span style="font-size:11.5px; font-weight:600; color:#475569;">X</span>
        </a>

      </div>
    </div>

    <!-- 1-Tap Copy Link Box -->
    <div>
      <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.7px; margin:0 0 7px;">Copy link</p>
      <div style="display:flex; align-items:center; gap:8px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; padding:4px 4px 4px 12px;">
        <i class="fa-solid fa-link" style="color:#94A3B8; font-size:12px; flex-shrink:0;"></i>
        <input type="text" id="shareDirectUrlInput" readonly style="flex:1; background:transparent; border:none; outline:none; font-size:12.5px; color:#334155; min-width:0; text-overflow:ellipsis; overflow:hidden;" />
        <button type="button" id="copyShareLinkBtn" onclick="copyShareModalLink()" style="background:#0F172A; color:#fff; border:none; padding:8px 16px; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:5px; white-space:nowrap; transition:all 0.2s;">
          <i class="fa-regular fa-copy"></i> <span>Copy</span>
        </button>
      </div>
    </div>

    <!-- Device Native Share (Visible on supported mobile/desktop) -->
    <div id="deviceShareContainer" style="display:none; text-align:center; margin-top:12px; padding-top:10px; border-top:1px solid #F1F5F9;">
      <button type="button" onclick="triggerNativeShare()" style="background:transparent; border:none; color:#64748B; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; padding:4px 8px; transition:color 0.15s;" onmouseover="this.style.color='#0F172A'" onmouseout="this.style.color='#64748B'">
        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;"></i> More sharing options
      </button>
    </div>

  </div>
</div>

<script>
// Filter State
let currentFilterIntent = '<?= htmlspecialchars($intent) ?>';
let currentBhkFilter = 'all';
let showSavedOnly = false;
let currentTagFilters = {
  pure_veg: false,
  no_curfew: false,
  zero_brokerage: false,
  under_15k: false
};

// Wishlist Local Storage
function getSavedIds() {
  try {
    return JSON.parse(localStorage.getItem('pov_saved_rentals') || '[]');
  } catch (e) {
    return [];
  }
}

function updateWishlistBadge() {
  const saved = getSavedIds();
  const badge = document.getElementById('savedCountBadge');
  if (badge) badge.textContent = saved.length;

  // Update heart icons on cards
  document.querySelectorAll('.simple-card-item').forEach(card => {
    const id = parseInt(card.dataset.id, 10);
    const btn = card.querySelector('.wishlist-btn');
    if (btn) {
      const isSaved = saved.includes(id);
      btn.style.color = isSaved ? '#E11D48' : '#64748B';
      btn.innerHTML = isSaved ? '<i class="fa-solid fa-heart" style="color:#E11D48;"></i>' : '<i class="fa-regular fa-heart"></i>';
    }
  });
}

function toggleSimpleShortlist(id, title, btn, event) {
  if (event) event.stopPropagation();
  let saved = getSavedIds();
  const index = saved.indexOf(id);
  let isSaved = false;

  if (index > -1) {
    saved.splice(index, 1);
    isSaved = false;
    showToast(`Removed from saved list`);
  } else {
    saved.push(id);
    isSaved = true;
    showToast(`❤️ "${title}" saved to your shortlist!`);
  }

  localStorage.setItem('pov_saved_rentals', JSON.stringify(saved));
  updateWishlistBadge();

  if (showSavedOnly) {
    runInstantFilter();
  }
}

function toggleSavedOnly(btn) {
  showSavedOnly = !showSavedOnly;
  if (showSavedOnly) {
    btn.style.background = '#E11D48';
    btn.style.color = '#fff';
    btn.querySelector('i').style.color = '#fff';
  } else {
    btn.style.background = '#FFF1F2';
    btn.style.color = '#BE123C';
    btn.querySelector('i').style.color = '#E11D48';
  }
  runInstantFilter();
}

function filterSimpleIntent(type, btn) {
  currentFilterIntent = type;
  document.querySelectorAll('.s-pill').forEach(b => {
    b.style.background = '#fff';
    b.style.color = '#334155';
  });
  btn.style.background = '#142132';
  btn.style.color = '#fff';
  runInstantFilter();
}

function toggleBhkFilter(bhkType, btn) {
  if (currentBhkFilter === bhkType) {
    currentBhkFilter = 'all';
    btn.style.background = '#fff';
    btn.style.color = '#334155';
  } else {
    currentBhkFilter = bhkType;
    document.querySelectorAll('.s-pill-bhk').forEach(b => {
      b.style.background = '#fff';
      b.style.color = '#334155';
    });
    btn.style.background = '#142132';
    btn.style.color = '#fff';
  }
  runInstantFilter();
}

function toggleTag(tagKey, btn) {
  currentTagFilters[tagKey] = !currentTagFilters[tagKey];
  if (currentTagFilters[tagKey]) {
    btn.style.background = '#142132';
    btn.style.color = '#fff';
  } else {
    btn.style.background = '#fff';
    btn.style.color = '#334155';
  }
  runInstantFilter();
}

function toggleBudget(budgetKey, btn) {
  currentTagFilters.under_15k = !currentTagFilters.under_15k;
  if (currentTagFilters.under_15k) {
    btn.style.background = '#142132';
    btn.style.color = '#fff';
  } else {
    btn.style.background = '#fff';
    btn.style.color = '#334155';
  }
  runInstantFilter();
}

function runInstantFilter() {
  const cards = document.querySelectorAll('.simple-card-item');
  const savedIds = getSavedIds();
  let visibleCount = 0;

  cards.forEach(card => {
    const cardId = parseInt(card.dataset.id, 10);
    const cardIntent = card.dataset.intent || '';
    const cardPrice = parseFloat(card.dataset.price) || 0;
    const cardBhk = card.dataset.bhk || '';
    const cardFood = card.dataset.food || '';
    const cardCurfew = card.dataset.curfew || '';
    const cardBrokerage = card.dataset.brokerage || '';

    let match = true;

    // Show saved only check
    if (showSavedOnly && !savedIds.includes(cardId)) {
      match = false;
    }

    // Intent check
    if (currentFilterIntent !== 'all' && cardIntent !== currentFilterIntent) {
      match = false;
    }

    // BHK check
    if (currentBhkFilter !== 'all' && cardBhk !== currentBhkFilter) {
      match = false;
    }

    // Pure Veg
    if (currentTagFilters.pure_veg && cardFood !== 'pure_veg' && cardFood !== 'jain_friendly') {
      match = false;
    }

    // 24x7 No Curfew
    if (currentTagFilters.no_curfew && cardCurfew !== 'no_curfew') {
      match = false;
    }

    // Zero Brokerage
    if (currentTagFilters.zero_brokerage && cardBrokerage !== 'zero_brokerage') {
      match = false;
    }

    // Under 15k
    if (currentTagFilters.under_15k && cardPrice > 15000) {
      match = false;
    }

    if (match) {
      card.style.display = 'flex';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  const countEl = document.getElementById('simpleCountLabel');
  if (countEl) countEl.textContent = visibleCount;
}

function runInstantSort(criteria) {
  const container = document.getElementById('simpleCardsContainer');
  if (!container) return;

  const cards = Array.from(container.querySelectorAll('.simple-card-item'));
  const divider = container.querySelector('.nearby-section-divider');

  cards.sort((a, b) => {
    const priceA = parseFloat(a.dataset.price) || 0;
    const priceB = parseFloat(b.dataset.price) || 0;
    const ratingA = parseFloat(a.dataset.rating) || 0;
    const ratingB = parseFloat(b.dataset.rating) || 0;
    const isRelatedA = a.classList.contains('related-item') ? 1 : 0;
    const isRelatedB = b.classList.contains('related-item') ? 1 : 0;

    if (criteria === 'price_low') return priceA - priceB;
    if (criteria === 'price_high') return priceB - priceA;
    if (criteria === 'rating') return ratingB - ratingA;
    
    // Default: keep primary matches first, then related
    return isRelatedA - isRelatedB;
  });

  cards.forEach(card => {
    container.appendChild(card);
  });

  // Re-place divider before first related item if default sort
  if (divider && criteria === 'default') {
    const firstRelated = container.querySelector('.related-item');
    if (firstRelated) {
      container.insertBefore(divider, firstRelated);
    }
  }
}

function resetSimpleFilters() {
  currentFilterIntent = 'all';
  currentBhkFilter = 'all';
  showSavedOnly = false;
  currentTagFilters = { pure_veg: false, no_curfew: false, zero_brokerage: false, under_15k: false };

  document.querySelectorAll('.s-pill').forEach(b => {
    b.style.background = '#fff';
    b.style.color = '#334155';
  });
  const firstPill = document.querySelector('.s-pill');
  if (firstPill) {
    firstPill.style.background = '#142132';
    firstPill.style.color = '#fff';
  }

  document.querySelectorAll('.s-pill-bhk').forEach(b => {
    b.style.background = '#fff';
    b.style.color = '#334155';
  });

  document.querySelectorAll('.s-pill-tag').forEach(b => {
    b.style.background = '#fff';
    b.style.color = '#334155';
  });

  const savedBtn = document.getElementById('savedFilterBtn');
  if (savedBtn) {
    savedBtn.style.background = '#FFF1F2';
    savedBtn.style.color = '#BE123C';
    savedBtn.querySelector('i').style.color = '#E11D48';
  }

  runInstantFilter();
}

function shareCurrentSearch() {
  const currentUrl = window.location.href;
  const landmark = document.getElementById('searchLandmarkInput')?.value || '';
  const city = document.getElementById('searchCitySelect')?.value || '';
  const shareText = encodeURIComponent(`Check out verified accommodations in ${landmark || city} on POV Indian:\n${currentUrl}`);
  window.open(`https://wa.me/?text=${shareText}`, '_blank');
}

// Social Share Modal Logic (Google, Facebook, Instagram, WhatsApp, Copy Link)
let currentShareData = { title: '', url: '', price: '', locality: '', img: '' };

function openSocialShareModal(data) {
  if (!data) return;
  if (typeof data === 'string') {
    data = { title: arguments[0] || '', url: arguments[1] || window.location.href, price: arguments[2] || '', locality: arguments[3] || '', img: arguments[4] || '' };
  }
  
  // Guarantee full absolute URL (e.g. http://127.0.0.1:8000/rental-detail.php?id=...)
  let fullUrl = data.url || window.location.href;
  if (fullUrl.startsWith('/')) {
    fullUrl = window.location.origin + fullUrl;
  } else if (!fullUrl.startsWith('http')) {
    fullUrl = window.location.origin + '/' + fullUrl;
  }
  data.url = fullUrl;
  currentShareData = data;

  const modal = document.getElementById('socialShareModal');
  if (!modal) return;

  const titleEl = document.getElementById('sharePreviewTitle');
  const priceEl = document.getElementById('sharePreviewPrice');
  const locEl = document.getElementById('sharePreviewLocality');
  const imgEl = document.getElementById('sharePreviewImg');
  const inputEl = document.getElementById('shareDirectUrlInput');
  const copyBtn = document.getElementById('copyShareLinkBtn');

  if (titleEl) titleEl.textContent = data.title || 'Verified Property';
  if (priceEl) priceEl.textContent = data.price || 'Best Price';
  if (locEl) locEl.textContent = data.locality || 'Prime Location';
  if (imgEl) {
    if (data.img) {
      imgEl.src = data.img;
      imgEl.style.display = 'block';
    } else {
      imgEl.style.display = 'none';
    }
  }
  if (inputEl) inputEl.value = fullUrl;
  if (copyBtn) {
    copyBtn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copy</span>';
    copyBtn.style.background = '#0F172A';
  }

  // Pre-fill share URLs
  const shareText = `Found this verified property on POV Indian:\n${data.title}${data.price ? ' (' + data.price + ')' : ''} in ${data.locality || ''}\n${data.url}`;
  
  // WhatsApp
  const waBtn = document.getElementById('shareWaBtn');
  if (waBtn) waBtn.href = `https://api.whatsapp.com/send?text=${encodeURIComponent(shareText)}`;

  // Facebook
  const fbBtn = document.getElementById('shareFbBtn');
  if (fbBtn) fbBtn.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(data.url)}`;

  // Google (Gmail compose)
  const gBtn = document.getElementById('shareGoogleBtn');
  if (gBtn) {
    const emailSubject = encodeURIComponent(`Property on POV Indian: ${data.title}`);
    const emailBody = encodeURIComponent(`Hi,\n\nI found this verified accommodation on POV Indian:\n\n${data.title}\n${data.price ? 'Price: ' + data.price + '\n' : ''}${data.locality ? 'Location: ' + data.locality + '\n' : ''}\nView details:\n${data.url}\n\nShared via POVIndian.com`);
    gBtn.href = `https://mail.google.com/mail/?view=cm&fs=1&su=${emailSubject}&body=${emailBody}`;
  }

  // X (Twitter)
  const xBtn = document.getElementById('shareXBtn');
  if (xBtn) {
    xBtn.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent('Found this accommodation on POV Indian: ' + (data.title || ''))}&url=${encodeURIComponent(data.url)}`;
  }

  // Check device share support
  const deviceContainer = document.getElementById('deviceShareContainer');
  if (deviceContainer) {
    deviceContainer.style.display = navigator.share ? 'block' : 'none';
  }

  modal.style.display = 'flex';
}

function closeSocialShareModal() {
  const modal = document.getElementById('socialShareModal');
  if (modal) modal.style.display = 'none';
}

function copyShareModalLink() {
  const url = currentShareData.url || window.location.href;
  navigator.clipboard.writeText(url).then(() => {
    const copyBtn = document.getElementById('copyShareLinkBtn');
    if (copyBtn) {
      copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Copied!</span>';
      copyBtn.style.background = '#059669';
      setTimeout(() => {
        copyBtn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copy</span>';
        copyBtn.style.background = '#0F172A';
      }, 2500);
    }
    showToast('✅ Link copied to clipboard!');
  }).catch(() => {
    const input = document.getElementById('shareDirectUrlInput');
    if (input) {
      input.select();
      document.execCommand('copy');
      showToast('✅ Link copied to clipboard!');
    }
  });
}

function shareToInstagram() {
  const url = currentShareData.url || window.location.href;
  navigator.clipboard.writeText(url).then(() => {
    showToast('📸 Link copied! Opening Instagram...');
    setTimeout(() => {
      window.open('https://www.instagram.com/', '_blank');
    }, 600);
  }).catch(() => {
    window.open('https://www.instagram.com/', '_blank');
  });
}

function triggerNativeShare() {
  if (navigator.share) {
    navigator.share({
      title: currentShareData.title || 'POV Indian Property',
      text: `${currentShareData.title} - ${currentShareData.price} at ${currentShareData.locality}`,
      url: currentShareData.url
    }).catch(err => console.log('Share dismissed', err));
  }
}

function shareProperty(title, url, price, locality, img) {
  openSocialShareModal({
    title: title || 'Property on POV Indian',
    url: url || window.location.href,
    price: price || '',
    locality: locality || '',
    img: img || ''
  });
}

function submitWhatsAppAlert(e) {
  e.preventDefault();
  const phone = document.getElementById('alertPhoneInput').value;
  if (!phone || phone.length < 10) {
    alert('Please enter a valid 10-digit WhatsApp number');
    return;
  }
  showToast(`✅ Subscribed! You will receive WhatsApp alerts for new listings.`);
  document.getElementById('alertPhoneInput').value = '';
}

function showToast(msg) {
  let toast = document.getElementById('simpleToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'simpleToast';
    toast.style.cssText = 'position:fixed; bottom:24px; right:24px; background:#142132; color:#fff; padding:12px 20px; border-radius:999px; font-size:13.5px; font-weight:700; box-shadow:0 8px 24px rgba(0,0,0,0.25); z-index:99999; transform:translateY(80px); opacity:0; transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);';
    document.body.appendChild(toast);
  }
  toast.innerHTML = msg;
  toast.style.transform = 'translateY(0)';
  toast.style.opacity = '1';
  clearTimeout(window.__toastTimer);
  window.__toastTimer = setTimeout(() => {
    toast.style.transform = 'translateY(80px)';
    toast.style.opacity = '0';
  }, 3200);
}

// Close on backdrop click & ESC key
document.getElementById('socialShareModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeSocialShareModal();
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeSocialShareModal();
  }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  updateWishlistBadge();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
