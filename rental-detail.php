<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 101;
$item = pov_get_rental_full_detail($id);

if (!$item) {
  $item = pov_get_rental_full_detail(101);
}

$page_no = (int)($item['page_no'] ?? 1);
$current_id = (int)($item['id'] ?? 101);

$page_title = $item['title'] . ' | POV Indian Rentals & Stays';
$page_description = $item['desc'];

// 5 Showcase Pages definition for quick switcher
$showcase_pages = [
  1 => ['id' => 101, 'label' => 'Page 1: Sunlit 2 BHK (Jaipur)', 'city' => 'Jaipur', 'url' => 'rental-page-1.php'],
  2 => ['id' => 102, 'label' => 'Page 2: Ananya PG (Kota)', 'city' => 'Kota', 'url' => 'rental-page-2.php'],
  3 => ['id' => 103, 'label' => 'Page 3: DLF Cyber Hub (Gurugram)', 'city' => 'Gurugram', 'url' => 'rental-page-3.php'],
  4 => ['id' => 104, 'label' => 'Page 4: Heritage Haveli (Udaipur)', 'city' => 'Udaipur', 'url' => 'rental-page-4.php'],
  5 => ['id' => 105, 'label' => 'Page 5: Urban Nest Studio (Bangalore)', 'city' => 'Bangalore', 'url' => 'rental-page-5.php'],
];

$current_share_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000') . ($_SERVER['REQUEST_URI'] ?? ('/rental-detail.php?id=' . $id));

$wa_clean_phone = preg_replace('/[^0-9]/', '', $item['phone'] ?? '+919829011223');
$wa_text = urlencode("Hello! I saw your listing '" . $item['title'] . "' (ID: #" . $item['id'] . ") on POV Indian near " . $item['landmark'] . ". Is this currently available for a visit?");
$wa_url = "https://wa.me/" . $wa_clean_phone . "?text=" . $wa_text;
$current_page = 'listings';
$body_class = 'category-landing rentals-page rental-detail-page';
$extra_css = 'css/rental-detail.css';

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

  <!-- Leaflet Map CSS for Real-Time Coordinates & Interactive Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <!-- Sub-Header: Breadcrumbs & Share/Save Actions -->
  <div class="rd-breadcrumbs-bar">
    <div class="container rd-breadcrumbs-flex">
      <nav class="rd-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php?city=' . urlencode(strtolower($item['city'])))) ?>">
          <?= htmlspecialchars($item['city_crumb'] ?? ($item['city'] . ' homes')) ?>
        </a>
        <span class="crumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php?landmark=' . urlencode($item['sub_crumb'] ?? ''))) ?>">
          <?= htmlspecialchars($item['sub_crumb'] ?? $item['locality']) ?>
        </a>
        <span class="crumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span class="crumb-current"><?= htmlspecialchars($item['short_title'] ?? $item['title']) ?></span>
      </nav>

      <div class="rd-top-actions">
        <button type="button" class="rd-btn-action" onclick="openShareModal()">
          <i class="fa-solid fa-share-nodes"></i> Share
        </button>
        <button type="button" class="rd-btn-action rd-btn-fav" aria-label="Save listing" onclick="toggleFavorite(this)">
          <i class="fa-regular fa-heart"></i>
        </button>
      </div>
    </div>
  </div>

  <main class="container" style="padding-top: 6px;">
    <!-- Photo Gallery (3 Photos Editorial Mosaic) -->
    <section class="rd-gallery-grid" aria-label="Photo Gallery">
      <div class="rd-gallery-main">
        <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . ($item['gallery'][0] ?? $item['img']))) ?>" 
             alt="<?= htmlspecialchars($item['title']) ?>" id="mainGalleryImg" />
        <span class="rd-gallery-badge-top">
          <i class="fa-solid fa-building"></i> <?= htmlspecialchars($item['category']) ?>
        </span>
        <span class="rd-gallery-verified-badge">
          <i class="fa-solid fa-shield-halved"></i> <?= htmlspecialchars($item['trust_badge']) ?>
        </span>
        <span class="rd-gallery-count">
          <i class="fa-regular fa-images"></i> 1 / <?= count($item['gallery'] ?? [1,2,3]) ?> photos
        </span>
      </div>

      <div class="rd-gallery-side">
        <div class="rd-gallery-thumb" onclick="swapPhoto(1)">
          <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . ($item['gallery'][1] ?? 'tourism_01.jpg'))) ?>" 
               alt="<?= htmlspecialchars($item['title']) ?> angle 2" />
        </div>
        <div class="rd-gallery-thumb" onclick="swapPhoto(2)">
          <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . ($item['gallery'][2] ?? 'hotel_01.jpg'))) ?>" 
               alt="<?= htmlspecialchars($item['title']) ?> angle 3" />
        </div>
      </div>
    </section>

    <!-- Main Content Layout (2 Columns) -->
    <div class="rd-main-layout">
      <!-- Left Column (Property Details) -->
      <div class="rd-left-col">
        <!-- Sub-pills -->
        <div class="rd-pills-row">
          <span class="rd-pill-walking">
            <i class="fa-solid fa-location-arrow"></i>
            <?= htmlspecialchars($item['walking_pill'] ?? ($item['landmark_distance'] . ' • ' . $item['landmark'])) ?>
          </span>
          <span class="rd-pill-fresh">
            <i class="fa-regular fa-clock"></i>
            <?= htmlspecialchars($item['updated_pill'] ?? $item['freshness']) ?>
          </span>
        </div>

        <!-- Heading -->
        <h1 class="rd-listing-title"><?= htmlspecialchars($item['title']) ?></h1>

        <!-- Location -->
        <div class="rd-listing-location-row">
          <i class="fa-solid fa-location-dot rd-loc-pin-icon"></i>
          <span>
            <?php
              $fullLoc = array_unique(array_filter([$item['locality'] ?? '', $item['city'] ?? '', $item['state'] ?? ''], fn($v) => trim((string)$v) !== ''));
              echo htmlspecialchars(implode(', ', $fullLoc) ?: 'India');
            ?>
          </span>
          <span class="rd-approx-badge">
            <i class="fa-solid fa-map-pin"></i> <?= htmlspecialchars($item['approx_pin_label'] ?? 'Approx. pin') ?>
          </span>
        </div>

        <!-- Price -->
        <div class="rd-price-row">
          <span class="rd-price-figure"><?= htmlspecialchars($item['price']) ?></span>
          <span class="rd-price-unit"><?= htmlspecialchars($item['price_unit']) ?></span>
          <span class="rd-total-movein-pill">
            <i class="fa-solid fa-circle-info" style="color:var(--rd-terracotta);"></i>
            <?= htmlspecialchars($item['cost']['total'] ?? $item['move_in_est']) ?>
          </span>
        </div>

        <!-- 4 Quick Specs Cards -->
        <div class="rd-quick-specs-grid">
          <?php foreach (($item['quick_specs'] ?? []) as $q): ?>
            <div class="rd-spec-card">
              <i class="<?= htmlspecialchars($q['icon']) ?> rd-spec-icon"></i>
              <span class="rd-spec-val"><?= htmlspecialchars($q['value']) ?></span>
              <span class="rd-spec-label"><?= htmlspecialchars($q['label']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- THE USEFUL DETAILS SECTION -->
        <section class="rd-details-section">
          <span class="rd-section-eyebrow">THE USEFUL DETAILS</span>
          <h2 class="rd-section-title"><?= htmlspecialchars($item['headline'] ?? 'Made for everyday living.') ?></h2>
          <p class="rd-section-sub"><?= htmlspecialchars($item['headline_sub'] ?? $item['desc']) ?></p>

          <!-- Interactive Tabs -->
          <div class="rd-tabs-header">
            <button type="button" class="rd-tab-btn is-active" onclick="switchDetailTab('overview', this)">Overview</button>
            <button type="button" class="rd-tab-btn" onclick="switchDetailTab('amenities', this)">Amenities</button>
            <button type="button" class="rd-tab-btn" onclick="switchDetailTab('terms', this)">Terms</button>
          </div>

          <!-- Tab Pane 1: Overview -->
          <div class="rd-tab-pane is-active" id="pane-overview">
            <div class="rd-specs-table-grid">
              <?php foreach (($item['specs'] ?? []) as $label => $val): ?>
                <div class="rd-specs-row">
                  <span class="rd-specs-label"><?= htmlspecialchars($label) ?></span>
                  <span class="rd-specs-value"><?= htmlspecialchars($val) ?></span>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="rd-alert-box">
              <i class="fa-solid fa-circle-exclamation"></i>
              <div>Details are based on the provider's latest confirmation. Ask for a physical or video visit before paying anything.</div>
            </div>
          </div>

          <!-- Tab Pane 2: Amenities -->
          <div class="rd-tab-pane" id="pane-amenities">
            <div class="rd-specs-table-grid" style="grid-template-columns: 1fr;">
              <?php foreach (($item['amenities_list'] ?? []) as $am): ?>
                <div class="rd-specs-row">
                  <span class="rd-specs-label"><i class="<?= htmlspecialchars($am['icon']) ?>' mr-2" style="color:var(--rd-terracotta);"></i> <?= htmlspecialchars($am['name']) ?></span>
                  <span class="rd-specs-value" style="color:#059669;"><i class="fa-solid fa-check"></i> Included</span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Tab Pane 3: Terms -->
          <div class="rd-tab-pane" id="pane-terms">
            <div class="rd-specs-table-grid">
              <?php foreach (($item['terms'] ?? []) as $tKey => $tVal): ?>
                <div class="rd-specs-row">
                  <span class="rd-specs-label"><?= htmlspecialchars($tKey) ?></span>
                  <span class="rd-specs-value"><?= htmlspecialchars($tVal) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>

        <!-- AT A GLANCE (Amenities grid) -->
        <section class="rd-amenities-section">
          <div class="rd-amenities-header">
            <div>
              <span class="rd-section-eyebrow">AT A GLANCE</span>
              <h2 class="rd-section-title" style="margin-bottom:0;">Room to settle in.</h2>
            </div>
            <button type="button" class="rd-btn-view-all" onclick="openAmenitiesModal()">
              View all <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>

          <div class="rd-amenities-chips-grid">
            <?php foreach (($item['amenities_list'] ?? []) as $amenity): ?>
              <div class="rd-amenity-chip">
                <i class="<?= htmlspecialchars($amenity['icon']) ?>"></i>
                <span><?= htmlspecialchars($amenity['name']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- LANDMARK-FIRST LOCATION -->
        <section class="rd-location-section">
          <span class="rd-section-eyebrow">LANDMARK-FIRST LOCATION</span>
          <h2 class="rd-section-title">Near the places you know.</h2>
          <p class="rd-section-sub">The exact entrance stays private. The neighbourhood, useful landmarks and walking context stay visible.</p>

          <?php 
            $propLat = (float)($item['latitude'] ?? 18.5132);
            $propLng = (float)($item['longitude'] ?? 73.8344);
            $gmapQuery = $propLat . ',' . $propLng;
            $gmapLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($gmapQuery);
            $gmapDirLink = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($gmapQuery);
          ?>

          <!-- Real-Time GPS & Coordinates Strip -->
          <div class="rd-location-meta-strip">
            <div class="rd-meta-coords-wrap">
              <span class="rd-coords-pill" title="Verified Geographical Coordinates (Latitude / Longitude)">
                <i class="fa-solid fa-compass" style="color:var(--rd-terracotta);"></i>
                <span>GPS: <strong id="valPropLat"><?= number_format($propLat, 4) ?>° N</strong>, <strong id="valPropLng"><?= number_format($propLng, 4) ?>° E</strong></span>
                <button type="button" class="rd-copy-coords-btn" onclick="copyPropertyCoordinates(<?= $propLat ?>, <?= $propLng ?>)" title="Copy Lat/Lng coordinates">
                  <i class="fa-regular fa-copy"></i>
                </button>
              </span>

              <span class="rd-geo-precision-badge">
                <i class="fa-solid fa-circle-check" style="color:#10B981;"></i> Verified Landmark Radius (±25m)
              </span>
            </div>

            <div class="rd-meta-actions-wrap">
              <button type="button" class="rd-btn-detect-gps" id="btnDetectCurrentLoc" onclick="detectUserCurrentLocation(<?= $propLat ?>, <?= $propLng ?>)">
                <i class="fa-solid fa-location-crosshairs"></i>
                <span>Detect My Current Location</span>
              </button>

              <div class="rd-map-view-switcher" role="tablist">
                <button type="button" class="rd-map-tab-btn is-active" id="btnTabInteractiveMap" onclick="switchMapDisplayMode('live')">
                  <i class="fa-solid fa-map-location-dot"></i> Live Map
                </button>
                <button type="button" class="rd-map-tab-btn" id="btnTabOverviewMap" onclick="switchMapDisplayMode('overview')">
                  <i class="fa-solid fa-compass-drafting"></i> Radar View
                </button>
              </div>
            </div>
          </div>

          <!-- Dynamic Live GPS Distance Banner -->
          <div id="rdLiveLocationBanner" class="rd-live-location-banner" style="display:none;">
            <div class="rd-live-banner-left">
              <span class="rd-live-pulse-dot"></span>
              <div class="rd-live-banner-text">
                <div class="rd-live-banner-title">
                  Your Current Location: <strong id="rdLiveDistValue">Calculating distance...</strong> from this property
                </div>
                <div class="rd-live-banner-sub" id="rdLiveDistSub">
                  Straight-line & estimated drive time based on live GPS coordinates.
                </div>
              </div>
            </div>
            <div class="rd-live-banner-actions">
              <a id="rdLiveDirectionsBtn" href="<?= htmlspecialchars($gmapDirLink) ?>" target="_blank" rel="noopener noreferrer" class="rd-btn-start-nav">
                <i class="fa-solid fa-diamond-turn-right"></i> Start Navigation ↗
              </a>
              <button type="button" onclick="closeLiveLocationBanner()" class="rd-btn-banner-close" aria-label="Close notification">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </div>

          <!-- Interactive / Stylized Map Card -->
          <div class="rd-map-card">
            <!-- Mode 1: Live Interactive OpenStreetMap / Leaflet Canvas -->
            <div id="rdLiveMapWrapper" class="rd-leaflet-wrap" style="position:relative;">
              <div id="rdLeafletMap"></div>

              <!-- Corner Pill: Locality Name -->
              <div class="rd-map-badge-corner" style="z-index:400;">
                <i class="fa-solid fa-location-dot" style="color:var(--rd-terracotta);"></i>
                <span>
                  <?php
                    $mLoc = array_unique(array_filter([$item['locality'] ?? '', $item['city'] ?? ''], fn($v) => trim((string)$v) !== ''));
                    echo htmlspecialchars(implode(', ', $mLoc) ?: 'India');
                  ?>
                </span>
              </div>

              <!-- Top-Right Action Button -->
              <div class="rd-map-top-actions" style="z-index:400;">
                <button type="button" class="rd-map-badge-btn rd-btn-live-loc" onclick="detectUserCurrentLocation(<?= $propLat ?>, <?= $propLng ?>)" title="Detect your real-time distance via GPS">
                  <i class="fa-solid fa-location-arrow"></i> Real-Time Location ↗
                </button>
              </div>

              <!-- Floating Center Action Pill (Matching Screenshot) -->
              <div style="position:absolute; bottom:18px; left:50%; transform:translateX(-50%); z-index:400; pointer-events:auto;">
                <a href="<?= htmlspecialchars($gmapLink) ?>" target="_blank" rel="noopener noreferrer" style="display:inline-flex; align-items:center; gap:8px; background:#FFFFFF; border:1px solid #CBD5E1; box-shadow:0 4px 16px rgba(0,0,0,0.14); padding:7px 18px; border-radius:999px; text-decoration:none; color:#0F172A; font-size:12px; font-weight:700;">
                  <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10B981;"></span>
                  <span>Real-Time Location · Open ↗</span>
                </a>
              </div>
            </div>

            <!-- Mode 2: Stylized SVG Radar View (Matching User Screenshot) -->
            <div id="rdOverviewMapWrapper" style="display:none;">
              <a href="<?= htmlspecialchars($gmapLink) ?>" target="_blank" rel="noopener noreferrer" class="rd-map-visual" title="Click to open real-time location on Google Maps">
                <svg class="rd-map-svg" viewBox="0 0 920 280" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                  <defs>
                    <filter id="simpleMapGlow" x="-20%" y="-20%" width="140%" height="140%">
                      <feDropShadow dx="0" dy="3" stdDeviation="4" flood-color="#D95D39" flood-opacity="0.3" />
                    </filter>
                    <filter id="simplePillShadow" x="-10%" y="-10%" width="120%" height="120%">
                      <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.08" />
                    </filter>
                  </defs>

                  <!-- Clean Neutral Ground -->
                  <rect width="920" height="280" fill="#F8F7F4" />

                  <!-- Smooth River Waterway at Bottom -->
                  <path d="M-10,215 C220,195 440,230 680,210 L930,205 L930,290 L-10,290 Z" fill="#E0F2FE" />
                  <path d="M-10,215 C220,195 440,230 680,210 L930,205" fill="none" stroke="#BAE6FD" stroke-width="2" />

                  <!-- Soft Green Park Patches -->
                  <rect x="50" y="30" width="140" height="60" rx="14" fill="#DCFCE7" />
                  <rect x="730" y="30" width="140" height="60" rx="14" fill="#DCFCE7" />

                  <!-- Minimalist White Roads -->
                  <rect x="-10" y="105" width="940" height="18" fill="#FFFFFF" />
                  <rect x="450" y="-10" width="18" height="300" fill="#FFFFFF" />
                  <rect x="220" y="-10" width="10" height="230" fill="#FFFFFF" />
                  <rect x="690" y="-10" width="10" height="230" fill="#FFFFFF" />
                  <path d="M460,115 L460,145 Q460,158 480,158 L510,158 Q530,158 530,145 L530,115" fill="none" stroke="#FFFFFF" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" />

                  <!-- Approximate Radius Zone -->
                  <circle cx="490" cy="135" r="44" fill="rgba(217, 93, 57, 0.10)" stroke="#D95D39" stroke-width="1.5" stroke-dasharray="4 4" />
                  <circle cx="490" cy="135" r="22" fill="rgba(217, 93, 57, 0.16)" />

                  <!-- Real-Time Location Floating Badge inside Map -->
                  <g transform="translate(385, 185)" filter="url(#simplePillShadow)">
                    <rect width="210" height="30" rx="15" fill="#FFFFFF" stroke="#E2E8F0" />
                    <circle cx="16" cy="15" r="5" fill="#059669" />
                    <text x="30" y="19" font-family="'Inter', system-ui, sans-serif" font-size="11" font-weight="700" fill="#0F172A">Real-Time Location · Open ↗</text>
                  </g>
                </svg>

                <!-- Corner Pill: Locality Name -->
                <div class="rd-map-badge-corner">
                  <i class="fa-solid fa-location-dot" style="color:var(--rd-terracotta);"></i>
                  <span>
                    <?php
                      $mLocFallback = array_unique(array_filter([$item['locality'] ?? '', $item['city'] ?? ''], fn($v) => trim((string)$v) !== ''));
                      echo htmlspecialchars(implode(', ', $mLocFallback) ?: 'India');
                    ?>
                  </span>
                </div>

                <!-- Top-Right Action Button -->
                <div class="rd-map-top-actions">
                  <span class="rd-map-badge-btn rd-btn-live-loc">
                    <i class="fa-solid fa-location-arrow"></i> Real-Time Location ↗
                  </span>
                </div>

                <!-- Center Pulsing Pin -->
                <div class="rd-map-radar-pin" style="top:48%; left:53%;">
                  <div class="rd-radar-wave"></div>
                  <div class="rd-pin-center"><i class="fa-solid fa-location-dot"></i></div>
                </div>

                <!-- Hover Hint Tooltip -->
                <div class="rd-map-hover-hint">
                  <i class="fa-solid fa-arrow-up-right-from-square"></i> Click to Open Real-Time Google Maps
                </div>
              </a>
            </div>

            <!-- Clean Bottom Info Bar (Matching Screenshot) -->
            <div class="rd-map-bottom-note">
              <a href="<?= htmlspecialchars($gmapLink) ?>" target="_blank" rel="noopener noreferrer" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-diamond-turn-right" style="color:var(--rd-terracotta); font-size:14px;"></i>
                <span><strong>Click map</strong> to open live real-time navigation & directions on Google Maps</span>
              </a>
              <div style="font-size:12px; color:#059669; font-weight:600; display:flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-shield-halved"></i> Exact entrance shared after visit confirmation
              </div>
            </div>
          </div>

          <!-- Landmarks Proximity List -->
          <div class="rd-landmarks-list">
            <?php foreach (($item['landmarks'] ?? []) as $lm): ?>
              <div class="rd-landmark-item">
                <div class="rd-lm-left">
                  <span class="rd-lm-dot" style="background-color: <?= htmlspecialchars($lm['color'] ?? '#E05A36') ?>;"></span>
                  <span><?= htmlspecialchars($lm['name']) ?></span>
                </div>
                <div class="rd-lm-right">
                  <strong><?= htmlspecialchars($lm['distance']) ?></strong>
                  <span><?= htmlspecialchars($lm['time']) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- STILL COMPARING? OTHER HOMES NEARBY -->
        <?php if (!empty($item['comparisons'])): ?>
          <section class="rd-comparing-section">
            <div class="rd-comparing-header">
              <div>
                <span class="rd-section-eyebrow">STILL COMPARING?</span>
                <h2 class="rd-section-title" style="margin-bottom:0;">Other homes nearby.</h2>
              </div>
              <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>" class="rd-btn-view-all">
                See all homes <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>

            <div class="rd-comp-grid">
              <?php foreach ($item['comparisons'] as $comp): ?>
                <a href="<?= htmlspecialchars(pov_url($comp['link'])) ?>" class="rd-comp-card">
                  <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . $comp['img'])) ?>" 
                       alt="<?= htmlspecialchars($comp['title']) ?>" class="rd-comp-img" loading="lazy" />
                  <div class="rd-comp-body">
                    <div>
                      <h4 class="rd-comp-title"><?= htmlspecialchars($comp['title']) ?></h4>
                      <div class="rd-comp-loc"><?= htmlspecialchars($comp['loc']) ?> • <?= htmlspecialchars($comp['specs']) ?></div>
                    </div>
                    <div class="rd-comp-price">
                      <?= htmlspecialchars($comp['price']) ?> <span><?= htmlspecialchars($comp['unit']) ?></span>
                    </div>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>
      </div>

      <!-- Right Column (Sticky Side Cards) -->
      <aside class="rd-sidebar-col">
        <!-- Card 1: Know your move-in cost -->
        <div class="rd-card-movein">
          <h3 class="rd-movein-title">Know your move-in cost</h3>
          <p class="rd-movein-sub">No surprise line items. Here's what the first month looks like.</p>

          <div class="rd-movein-rows">
            <div class="rd-movein-row">
              <span><?= htmlspecialchars($item['cost']['rent_label'] ?? 'First month rent') ?></span>
              <strong><?= htmlspecialchars($item['cost']['rent'] ?? $item['price']) ?></strong>
            </div>
            <div class="rd-movein-row">
              <span><?= htmlspecialchars($item['cost']['deposit_label'] ?? 'Refundable deposit') ?></span>
              <strong><?= htmlspecialchars($item['cost']['deposit'] ?? $item['deposit']) ?></strong>
            </div>
            <div class="rd-movein-row">
              <span><?= htmlspecialchars($item['cost']['maintenance_label'] ?? 'Monthly maintenance') ?></span>
              <strong><?= htmlspecialchars($item['cost']['maintenance'] ?? $item['maintenance']) ?></strong>
            </div>
            <div class="rd-movein-total-row">
              <span><?= htmlspecialchars($item['cost']['total_label'] ?? 'Estimated move-in total') ?></span>
              <strong class="rd-movein-total-figure"><?= htmlspecialchars($item['cost']['total'] ?? $item['move_in_est']) ?></strong>
            </div>
          </div>

          <a href="<?= htmlspecialchars(pov_url('schedule-visit.php?property_id=' . $item['id'])) ?>" class="rd-btn-schedule">
            <i class="fa-regular fa-calendar-check"></i> Pick a Visit Slot
          </a>

          <a href="<?= $wa_url ?>" target="_blank" rel="noopener noreferrer" class="rd-btn-whatsapp">
            <i class="fa-brands fa-whatsapp"></i> Ask safely on WhatsApp
          </a>

          <div class="rd-privacy-note">
            <i class="fa-solid fa-lock"></i>
            <span>Your number stays private until you choose to share it.</span>
          </div>
        </div>

        <!-- Card 2: Meet the Provider -->
        <div class="rd-card-provider">
          <span class="rd-sidebar-eyebrow">YOUR POINT OF CONTACT</span>
          <h3 class="rd-sidebar-title">Meet the provider.</h3>

          <div class="rd-provider-info-box">
            <div class="rd-provider-header">
              <div class="rd-provider-identity">
                <div class="rd-avatar-circle"><?= htmlspecialchars($item['provider']['initials'] ?? 'RS') ?></div>
                <div class="rd-provider-name-col">
                  <strong><?= htmlspecialchars($item['provider']['name'] ?? $item['provider_name']) ?></strong>
                  <span><?= htmlspecialchars($item['provider']['role'] ?? $item['provider_type']) ?> • <?= htmlspecialchars($item['provider']['reply_speed'] ?? 'Replies in ~20 min') ?></span>
                </div>
              </div>
              <button type="button" class="rd-btn-view-checks" onclick="openProviderModal()" aria-label="View verification checks">
                View checks
              </button>
            </div>

            <div class="rd-provider-checks-row" onclick="openProviderModal()" role="button" tabindex="0" title="Click to view full verification certificate" onkeydown="if(event.key==='Enter'||event.key===' ')openProviderModal()">
              <i class="fa-solid fa-circle-check"></i>
              <span><?= implode(' • ', $item['provider']['checks'] ?? ['Identity checked', 'Property evidence reviewed']) ?></span>
            </div>
          </div>

          <div class="rd-safer-hello-box">
            <div class="rd-safer-head">
              <i class="fa-solid fa-shield-cat"></i>
              <span>A safer first hello.</span>
            </div>
            <p class="rd-safer-text">We'll show what gets shared before you contact <?= htmlspecialchars(explode(' ', $item['provider']['name'] ?? $item['provider_name'])[0]) ?>. Keep the conversation on POVIndian until you feel comfortable.</p>
            <a href="javascript:void(0)" onclick="openSafetyTipsModal()" class="rd-safer-link">
              See safe contact steps <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
            </a>
          </div>
        </div>

        <!-- Card 3: Plan Your Visit -->
        <div class="rd-card-visit">
          <span class="rd-sidebar-eyebrow">VISIT WITH CONTEXT</span>
          <h3 class="rd-sidebar-title">Plan your visit.</h3>

          <div class="rd-visit-detail-row">
            <div class="rd-visit-label">
              <i class="fa-regular fa-clock"></i> Best time to visit
            </div>
            <div class="rd-visit-value">
              <?= htmlspecialchars($item['provider']['best_time'] ?? '10 AM – 6 PM') ?>
              <small><?= htmlspecialchars($item['provider']['best_days'] ?? 'Mon – Sat') ?></small>
            </div>
          </div>

          <div class="rd-visit-detail-row">
            <div class="rd-visit-label">
              <i class="fa-solid fa-location-dot"></i> Meeting point
            </div>
            <div class="rd-visit-value">
              <?= htmlspecialchars($item['provider']['meeting_point'] ?? 'Lobby entrance') ?>
              <small><?= htmlspecialchars($item['provider']['meeting_note'] ?? 'Exact pin after confirm') ?></small>
            </div>
          </div>

          <a href="<?= htmlspecialchars(pov_url('schedule-visit.php?property_id=' . $item['id'])) ?>" class="rd-btn-pick-slot" style="text-decoration:none;">
            <i class="fa-regular fa-calendar-days"></i> Pick a visit slot
          </a>
        </div>
      </aside>
    </div>
  </main>

  <!-- Modals -->

  <!-- 1. Schedule Visit Modal -->
  <div class="rd-modal-overlay" id="modalScheduleVisit" onclick="closeOnOverlay(event, 'modalScheduleVisit')">
    <div class="rd-modal-content">
      <button type="button" class="rd-modal-close" onclick="closeModal('modalScheduleVisit')" aria-label="Close">&times;</button>
      <h3 class="rd-modal-title">Schedule a Visit</h3>
      <p class="rd-modal-sub"><?= htmlspecialchars($item['title']) ?></p>

      <form onsubmit="handleVisitSubmit(event)">
        <input type="hidden" name="property_id" value="<?= $item['id'] ?>" />
        <div class="rd-form-group">
          <label class="rd-form-label">Preferred Date *</label>
          <input type="date" class="rd-form-input" name="preferred_date" id="visitDate" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" />
        </div>

        <div class="rd-form-group">
          <label class="rd-form-label">Preferred Time Slot *</label>
          <select class="rd-form-select" name="preferred_time" id="visitSlot" required>
            <option value="10:00 AM">10:00 AM</option>
            <option value="11:00 AM" selected>11:00 AM</option>
            <option value="12:00 PM">12:00 PM</option>
            <option value="02:00 PM">02:00 PM</option>
            <option value="03:00 PM">03:00 PM</option>
            <option value="04:00 PM">04:00 PM</option>
            <option value="05:00 PM">05:00 PM</option>
            <option value="06:00 PM">06:00 PM</option>
          </select>
        </div>

        <div class="rd-form-group">
          <label class="rd-form-label">Your Full Name *</label>
          <input type="text" class="rd-form-input" name="visitor_name" id="visitorName" placeholder="e.g. Rahul Sharma" required />
        </div>

        <div class="rd-form-group">
          <label class="rd-form-label">Phone / WhatsApp Number *</label>
          <input type="tel" class="rd-form-input" name="visitor_phone" id="visitorPhone" placeholder="10-digit mobile number" maxlength="10" required />
        </div>

        <div class="rd-form-group">
          <label class="rd-form-label">Number of Visitors</label>
          <select class="rd-form-select" name="visitor_count">
            <option value="1">1 Person</option>
            <option value="2" selected>2 People</option>
            <option value="3">3 People</option>
            <option value="4">4+ People</option>
          </select>
        </div>

        <button type="submit" class="rd-btn-submit-modal" id="modalVisitSubmitBtn">
          <i class="fa-regular fa-calendar-check"></i> Request Visit
        </button>

        <div style="text-align:center; margin-top:12px;">
          <a href="<?= htmlspecialchars(pov_url('schedule-visit.php?property_id=' . $item['id'])) ?>" style="font-size:12.5px; color:var(--rd-terracotta); text-decoration:none; font-weight:600;">
            Open Full Schedule Page <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- 2. Share Modal -->
  <div class="rd-modal-overlay" id="modalShare" onclick="closeOnOverlay(event, 'modalShare')">
    <div class="rd-modal-content">
      <button type="button" class="rd-modal-close" onclick="closeModal('modalShare')" aria-label="Close">&times;</button>
      <h3 class="rd-modal-title">Share this property</h3>
      <p class="rd-modal-sub">Copy the link or share with friends or family</p>

      <div class="rd-form-group">
        <label class="rd-form-label">Page Link</label>
        <div style="display:flex; gap:8px;">
          <input type="text" class="rd-form-input" id="shareUrlInput" readonly value="<?= htmlspecialchars($current_share_url) ?>" />
          <button type="button" class="rd-btn-action" onclick="copyShareUrl()" style="white-space:nowrap;">
            <i class="fa-regular fa-copy"></i> Copy
          </button>
        </div>
      </div>

      <div style="display:flex; gap:10px; margin-top:20px;">
        <a href="https://api.whatsapp.com/send?text=<?= urlencode($item['title'] . ' - ' . $current_share_url) ?>" 
           target="_blank" class="rd-btn-whatsapp" style="margin:0;">
          <i class="fa-brands fa-whatsapp"></i> Share on WhatsApp
        </a>
      </div>
    </div>
  </div>

  <!-- 3. All Amenities Modal -->
  <div class="rd-modal-overlay" id="modalAmenities" onclick="closeOnOverlay(event, 'modalAmenities')">
    <div class="rd-modal-content" style="max-width:560px;">
      <button type="button" class="rd-modal-close" onclick="closeModal('modalAmenities')" aria-label="Close">&times;</button>
      <h3 class="rd-modal-title">All Amenities & Inclusions</h3>
      <p class="rd-modal-sub"><?= htmlspecialchars($item['title']) ?></p>

      <div class="rd-amenities-chips-grid" style="grid-template-columns: 1fr 1fr; margin-top:16px;">
        <?php foreach (($item['amenities_list'] ?? []) as $am): ?>
          <div class="rd-amenity-chip">
            <i class="<?= htmlspecialchars($am['icon']) ?>"></i>
            <span><?= htmlspecialchars($am['name']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- 4. Provider Verification Checks Modal -->
  <div class="rd-modal-overlay" id="modalProviderChecks" onclick="closeOnOverlay(event, 'modalProviderChecks')">
    <div class="rd-modal-content" style="max-width:560px;">
      <button type="button" class="rd-modal-close" onclick="closeModal('modalProviderChecks')" aria-label="Close">&times;</button>
      
      <!-- Header -->
      <div style="margin-bottom:16px;">
        <span class="rd-checks-verified-badge">
          <i class="fa-solid fa-shield-check"></i> POVIndian Verified Partner
        </span>
        <h3 class="rd-modal-title" style="margin:8px 0 4px; font-size:22px;">Provider Verification Checks</h3>
        <p class="rd-modal-sub" style="margin-bottom:0; font-size:13px;">
          Multi-layer background vetting conducted by POVIndian Operations for transparent and secure direct rentals.
        </p>
      </div>

      <!-- Provider Snapshot Card -->
      <div class="rd-provider-modal-card">
        <div style="position:relative; flex-shrink:0;">
          <div class="rd-avatar-circle" style="width:48px; height:48px; font-size:18px;">
            <?= htmlspecialchars($item['provider']['initials'] ?? 'RS') ?>
          </div>
          <span style="position:absolute; bottom:-3px; right:-3px; width:18px; height:18px; background:#16A34A; color:#FFFFFF; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; border:2px solid #FFFFFF;" title="Verified Provider">
            <i class="fa-solid fa-check"></i>
          </span>
        </div>
        <div style="flex:1;">
          <div style="display:flex; align-items:center; gap:6px;">
            <strong style="font-size:16px; color:var(--rd-navy);">
              <?= htmlspecialchars($item['provider']['name'] ?? $item['provider_name']) ?>
            </strong>
            <i class="fa-solid fa-circle-check" style="color:#2563EB; font-size:13px;" title="POVIndian Verified Direct Host"></i>
          </div>
          <div style="font-size:12.5px; color:var(--rd-muted); margin-top:1px;">
            <?= htmlspecialchars($item['provider']['role'] ?? $item['provider_type']) ?> · <?= htmlspecialchars($item['locality'] ?? 'Civil Lines') ?>
          </div>
          <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:6px; font-size:11.5px;">
            <span style="color:#15803D; font-weight:700; background:#DCFCE7; padding:2px 8px; border-radius:999px;">
              <i class="fa-solid fa-bolt"></i> <?= htmlspecialchars($item['provider']['reply_speed'] ?? 'Replies in ~20 min') ?>
            </span>
            <span style="color:#1E40AF; font-weight:700; background:#EFF6FF; padding:2px 8px; border-radius:999px;">
              <i class="fa-solid fa-star"></i> 4.9 Host Rating
            </span>
          </div>
        </div>
      </div>

      <!-- Trust Metrics Bar -->
      <div class="rd-checks-metrics-grid">
        <div class="rd-metric-item">
          <div class="rd-metric-num">4 / 4</div>
          <div class="rd-metric-lbl">Checks Cleared</div>
        </div>
        <div class="rd-metric-item">
          <div class="rd-metric-num">0%</div>
          <div class="rd-metric-lbl">Brokerage Fee</div>
        </div>
        <div class="rd-metric-item">
          <div class="rd-metric-num">100%</div>
          <div class="rd-metric-lbl">Direct Contact</div>
        </div>
      </div>

      <!-- 4 Verification Checks -->
      <div class="rd-check-list">
        <!-- Check 1 -->
        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#EFF6FF; color:#2563EB;">
            <i class="fa-solid fa-id-card"></i>
          </div>
          <div class="rd-check-body">
            <div class="rd-check-title-row">
              <span class="rd-check-title">Government Photo ID & Aadhaar</span>
              <span class="rd-check-pill"><i class="fa-solid fa-check"></i> Verified</span>
            </div>
            <p class="rd-check-desc">Government photo identification authenticated via official database validation. Legal name and KYC records verified.</p>
            <div class="rd-check-meta">
              <i class="fa-solid fa-fingerprint"></i> UIDAI Database Verified · Identity Authentic
            </div>
          </div>
        </div>

        <!-- Check 2 -->
        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#FEF3C7; color:#D97706;">
            <i class="fa-solid fa-file-invoice"></i>
          </div>
          <div class="rd-check-body">
            <div class="rd-check-title-row">
              <span class="rd-check-title">Property Ownership & Utility Documentation</span>
              <span class="rd-check-pill"><i class="fa-solid fa-check"></i> Verified</span>
            </div>
            <p class="rd-check-desc">Recent electricity bill (Jaipur Discom) and municipal property tax evidence reviewed to confirm legal possession and occupancy.</p>
            <div class="rd-check-meta">
              <i class="fa-solid fa-bolt-lightning"></i> Utility Address Matched · Registry Evidence Approved
            </div>
          </div>
        </div>

        <!-- Check 3 -->
        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#DCFCE7; color:#16A34A;">
            <i class="fa-solid fa-mobile-screen"></i>
          </div>
          <div class="rd-check-body">
            <div class="rd-check-title-row">
              <span class="rd-check-title">Active Phone & WhatsApp Direct Line</span>
              <span class="rd-check-pill"><i class="fa-solid fa-check"></i> Verified</span>
            </div>
            <p class="rd-check-desc">Dedicated direct contact number (+91 <?= htmlspecialchars(substr(preg_replace('/\D/', '', $item['phone'] ?? '9829011223'), -10)) ?>) OTP-verified for tenant walkthrough coordination.</p>
            <div class="rd-check-meta">
              <i class="fa-solid fa-circle-check"></i> 2FA OTP Verified · Direct Line Active
            </div>
          </div>
        </div>

        <!-- Check 4 -->
        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#F3E8FF; color:#9333EA;">
            <i class="fa-solid fa-house-circle-check"></i>
          </div>
          <div class="rd-check-body">
            <div class="rd-check-title-row">
              <span class="rd-check-title">Physical Property Context & Site Audit</span>
              <span class="rd-check-pill"><i class="fa-solid fa-check"></i> Verified</span>
            </div>
            <p class="rd-check-desc">Building exterior, entrance landmarks, floor plan, and room photographs physically cross-checked against actual property premises.</p>
            <div class="rd-check-meta">
              <i class="fa-solid fa-location-dot"></i> Neighborhood Context Confirmed · Geotagged
            </div>
          </div>
        </div>
      </div>

      <!-- Safe Token Policy Alert -->
      <div class="rd-trust-banner-box">
        <i class="fa-solid fa-shield-halved" style="font-size:20px; color:#2563EB; flex-shrink:0;"></i>
        <div>
          <strong style="display:block; color:#1E3A8A; margin-bottom:2px;">Zero Advance Before Walkthrough:</strong>
          <span>POVIndian strictly advises tenants to physically visit the property and inspect documents before paying any advance token or deposit.</span>
        </div>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-top:16px; flex-wrap:wrap;">
        <button type="button" class="rd-btn-action" onclick="closeModal('modalProviderChecks')">Close</button>
        <a href="<?= htmlspecialchars(pov_url('schedule-visit.php?property_id=' . $item['id'])) ?>" class="rd-btn-pick-slot" style="margin:0; width:auto; padding:11px 22px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
          <i class="fa-regular fa-calendar-check"></i> Pick a Visit Slot
        </a>
      </div>
    </div>
  </div>

  <!-- 5. Safe Contact Steps Modal -->
  <div class="rd-modal-overlay" id="modalSafetyTips" onclick="closeOnOverlay(event, 'modalSafetyTips')">
    <div class="rd-modal-content" style="max-width:520px;">
      <button type="button" class="rd-modal-close" onclick="closeModal('modalSafetyTips')" aria-label="Close">&times;</button>
      
      <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
        <i class="fa-solid fa-shield-cat" style="color:#059669; font-size:20px;"></i>
        <h3 class="rd-modal-title" style="margin:0;">Safe Contact & Visit Guidelines</h3>
      </div>
      <p class="rd-modal-sub" style="margin-bottom:18px;">
        Follow these 4 simple steps to protect yourself and ensure a transparent rental experience.
      </p>

      <div class="rd-check-list">
        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#EFF6FF; color:#2563EB;">1</div>
          <div class="rd-check-body">
            <span class="rd-check-title" style="display:block; margin-bottom:2px;">Chat Safely on Verified Channels</span>
            <p class="rd-check-desc">Use POVIndian or the verified WhatsApp button. Never click unknown payment links sent on SMS.</p>
          </div>
        </div>

        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#FEF3C7; color:#D97706;">2</div>
          <div class="rd-check-body">
            <span class="rd-check-title" style="display:block; margin-bottom:2px;">Schedule a Physical Walkthrough</span>
            <p class="rd-check-desc">Always pick a visit slot first. Check sunlight, ventilation, locks, and water pressure in person.</p>
          </div>
        </div>

        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#FEE2E2; color:#DC2626;">3</div>
          <div class="rd-check-body">
            <span class="rd-check-title" style="display:block; margin-bottom:2px;">Never Pay Advance Before Visiting</span>
            <p class="rd-check-desc">Legitimate direct owners in India do not demand booking tokens before physical inspection.</p>
          </div>
        </div>

        <div class="rd-check-card">
          <div class="rd-check-icon-wrap" style="background:#DCFCE7; color:#16A34A;">4</div>
          <div class="rd-check-body">
            <span class="rd-check-title" style="display:block; margin-bottom:2px;">Request Written Rental Agreement</span>
            <p class="rd-check-desc">Sign a formal 11-month lease agreement mentioning security deposit refund terms before moving in.</p>
          </div>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end;">
        <button type="button" class="rd-btn-pick-slot" onclick="closeModal('modalSafetyTips')" style="margin:0; width:auto; padding:10px 20px;">
          I Understand
        </button>
      </div>
    </div>
  </div>

  <!-- Clean Minimalist Social Share Modal -->
  <div class="rd-modal-overlay" id="modalShare" onclick="closeOnOverlay(event, 'modalShare')">
    <div class="rd-modal-content" style="max-width:420px; padding:22px; border-radius:20px; box-shadow:0 20px 40px -10px rgba(0,0,0,0.16); border:1px solid #E2E8F0;">
      
      <!-- Modal Header -->
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
          <h3 style="font-size:17px; font-weight:700; color:#0F172A; margin:0 0 2px;">Share listing</h3>
          <p style="font-size:12px; color:#64748B; margin:0;">Send this listing to friends or family</p>
        </div>
        <button type="button" class="rd-modal-close" onclick="closeModal('modalShare')" aria-label="Close" style="position:static; width:32px; height:32px; border-radius:50%; background:#F1F5F9; border:none; font-size:18px; line-height:1; cursor:pointer; color:#64748B; display:grid; place-items:center; transition:background 0.15s;" onmouseover="this.style.background='#E2E8F0'" onmouseout="this.style.background='#F1F5F9'">&times;</button>
      </div>

      <!-- Compact Property Snippet -->
      <div style="display:flex; align-items:center; gap:12px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px; padding:10px 12px; margin-bottom:20px;">
        <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . ($item['gallery'][0] ?? $item['img'] ?? ''))) ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>" style="width:48px; height:48px; border-radius:8px; object-fit:cover; background:#E2E8F0; flex-shrink:0;" />
        <div style="flex:1; min-width:0;">
          <h4 style="font-size:13px; font-weight:700; color:#0F172A; margin:0 0 3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($item['title'] ?? 'Listing') ?></h4>
          <div style="display:flex; align-items:center; gap:6px; font-size:12px;">
            <span style="font-weight:700; color:#059669;"><?= htmlspecialchars($item['price'] ?? '') ?></span>
            <span style="color:#CBD5E1;">•</span>
            <span style="color:#64748B; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($item['locality'] ?? '') ?></span>
          </div>
        </div>
      </div>

      <!-- Social Channels Row (WhatsApp, Facebook, Instagram, Google, X) -->
      <div style="margin-bottom:20px;">
        <p style="font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.7px; margin:0 0 12px;">Share to</p>
        
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
          
          <!-- 1. WhatsApp -->
          <a href="https://api.whatsapp.com/send?text=<?= urlencode('Found this verified listing on POV Indian: ' . ($item['title'] ?? '') . ' (' . ($item['price'] ?? '') . ' in ' . ($item['locality'] ?? '') . ')\n' . $current_share_url) ?>" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
            <div style="width:48px; height:48px; border-radius:50%; background:#25D366; color:#fff; display:grid; place-items:center; font-size:22px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(37,211,102,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
              <i class="fa-brands fa-whatsapp"></i>
            </div>
            <span style="font-size:11.5px; font-weight:600; color:#475569;">WhatsApp</span>
          </a>

          <!-- 2. Facebook -->
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($current_share_url) ?>" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
            <div style="width:48px; height:48px; border-radius:50%; background:#1877F2; color:#fff; display:grid; place-items:center; font-size:20px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(24,119,242,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
              <i class="fa-brands fa-facebook-f"></i>
            </div>
            <span style="font-size:11.5px; font-weight:600; color:#475569;">Facebook</span>
          </a>

          <!-- 3. Instagram -->
          <button type="button" onclick="shareDetailToInstagram()" style="display:flex; flex-direction:column; align-items:center; gap:7px; background:none; border:none; padding:0; cursor:pointer; flex:1; min-width:0; font-family:inherit;">
            <div style="width:48px; height:48px; border-radius:50%; background:radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%); color:#fff; display:grid; place-items:center; font-size:21px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(214,36,159,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
              <i class="fa-brands fa-instagram"></i>
            </div>
            <span style="font-size:11.5px; font-weight:600; color:#475569;">Instagram</span>
          </button>

          <!-- 4. Google -->
          <a href="https://mail.google.com/mail/?view=cm&fs=1&su=<?= urlencode('Property on POV Indian: ' . ($item['title'] ?? '')) ?>&body=<?= urlencode("Hi,\n\nI found this verified listing on POV Indian:\n" . ($item['title'] ?? '') . "\nPrice: " . ($item['price'] ?? '') . "\nLocation: " . ($item['locality'] ?? '') . "\n\nView details: " . $current_share_url) ?>" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
            <div style="width:48px; height:48px; border-radius:50%; background:#EA4335; color:#fff; display:grid; place-items:center; font-size:19px; transition:transform 0.18s, box-shadow 0.18s; box-shadow:0 4px 10px rgba(234,67,53,0.28);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
              <i class="fa-brands fa-google"></i>
            </div>
            <span style="font-size:11.5px; font-weight:600; color:#475569;">Google</span>
          </a>

          <!-- 5. X -->
          <a href="https://twitter.com/intent/tweet?text=<?= urlencode('Found this property on POV Indian: ' . ($item['title'] ?? '')) ?>&url=<?= urlencode($current_share_url) ?>" target="_blank" rel="noopener noreferrer" style="display:flex; flex-direction:column; align-items:center; gap:7px; text-decoration:none; flex:1; min-width:0;">
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
          <input type="text" id="detailShareUrlInput" readonly value="<?= htmlspecialchars($current_share_url) ?>" style="flex:1; background:transparent; border:none; outline:none; font-size:12.5px; color:#334155; min-width:0; text-overflow:ellipsis; overflow:hidden;" />
          <button type="button" id="copyDetailShareBtn" onclick="copyDetailShareLink()" style="background:#0F172A; color:#fff; border:none; padding:8px 16px; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:5px; white-space:nowrap; transition:all 0.2s;">
            <i class="fa-regular fa-copy"></i> <span>Copy</span>
          </button>
        </div>
      </div>

      <!-- Device Native Share (Visible on supported mobile/desktop) -->
      <div id="detailDeviceShareContainer" style="display:none; text-align:center; margin-top:12px; padding-top:10px; border-top:1px solid #F1F5F9;">
        <button type="button" onclick="triggerDetailNativeShare()" style="background:transparent; border:none; color:#64748B; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; padding:4px 8px; transition:color 0.15s;" onmouseover="this.style.color='#0F172A'" onmouseout="this.style.color='#64748B'">
          <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;"></i> More sharing options
        </button>
      </div>

    </div>
  </div>

    </div>
  </div>

  <!-- Toast Notification -->
  <div class="rd-toast" id="rdToast">
    <i class="fa-solid fa-circle-check" style="color:#10B981;"></i>
    <span id="toastMsg">Action completed!</span>
  </div>

  <!-- Minimalist Light Footer -->
  <footer style="background:#FAF8F5; border-top:1px solid var(--rd-border); padding:36px 0 24px; margin-top:60px;">
    <div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
      <div style="font-size:13px; color:var(--rd-muted);">
        © <?= date('Y') ?> POV Indian Rentals & Stays. Warm, landmark-first Indian accommodation layer.
      </div>
      <div style="display:flex; gap:18px; font-size:13px;">
        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>" style="color:var(--rd-navy); text-decoration:none; font-weight:600;">Rentals & Stays</a>
        <a href="<?= htmlspecialchars(pov_url('terms.php')) ?>" style="color:var(--rd-muted); text-decoration:none;">Terms</a>
        <a href="<?= htmlspecialchars(pov_url('privacy.php')) ?>" style="color:var(--rd-muted); text-decoration:none;">Privacy</a>
      </div>
    </div>
  </footer>

  <script>
    // Tab switching
    function switchDetailTab(tabId, btn) {
      document.querySelectorAll('.rd-tab-btn').forEach(b => b.classList.remove('is-active'));
      document.querySelectorAll('.rd-tab-pane').forEach(p => p.classList.remove('is-active'));
      btn.classList.add('is-active');
      const pane = document.getElementById('pane-' + tabId);
      if (pane) pane.classList.add('is-active');
    }

    // Modal controls
    function openVisitModal() {
      openModal('modalScheduleVisit');
    }
    function openShareModal() {
      const container = document.getElementById('detailDeviceShareContainer');
      if (container) {
        container.style.display = navigator.share ? 'block' : 'none';
      }
      openModal('modalShare');
    }
    function copyDetailShareLink() {
      const input = document.getElementById('detailShareUrlInput');
      const url = input ? input.value : window.location.href;
      navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('copyDetailShareBtn');
        if (btn) {
          btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Copied!</span>';
          btn.style.background = '#059669';
          setTimeout(() => {
            btn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copy</span>';
            btn.style.background = '#0F172A';
          }, 2500);
        }
        showToast('✅ Link copied to clipboard!');
      }).catch(() => {
        if (input) {
          input.select();
          document.execCommand('copy');
          showToast('✅ Link copied to clipboard!');
        }
      });
    }
    function triggerDetailNativeShare() {
      if (navigator.share) {
        navigator.share({
          title: <?= json_encode($item['title'] ?? 'Listing') ?>,
          text: <?= json_encode(($item['title'] ?? '') . ' - ' . ($item['price'] ?? '') . ' in ' . ($item['locality'] ?? '')) ?>,
          url: window.location.href
        }).catch(err => console.log('Share dismissed', err));
      }
    }
    function shareDetailToInstagram() {
      const url = window.location.href;
      navigator.clipboard.writeText(url).then(() => {
        showToast('📸 Listing link copied! Opening Instagram...');
        setTimeout(() => {
          window.open('https://www.instagram.com/', '_blank');
        }, 600);
      }).catch(() => {
        window.open('https://www.instagram.com/', '_blank');
      });
    }
    function openAmenitiesModal() {
      openModal('modalAmenities');
    }
    function openProviderModal() {
      openModal('modalProviderChecks');
    }
    function openSafetyTipsModal() {
      openModal('modalSafetyTips');
    }
    function openModal(id) {
      const el = document.getElementById(id);
      if (el) {
        el.classList.add('is-open');
        document.body.style.overflow = 'hidden';
      }
    }
    function closeModal(id) {
      const el = document.getElementById(id);
      if (el) {
        el.classList.remove('is-open');
        if (!document.querySelector('.rd-modal-overlay.is-open')) {
          document.body.style.overflow = '';
        }
      }
    }
    function closeOnOverlay(e, id) {
      if (e.target.id === id) {
        closeModal(id);
      }
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        document.querySelectorAll('.rd-modal-overlay.is-open').forEach(m => {
          m.classList.remove('is-open');
        });
        document.body.style.overflow = '';
      }
    });

    // Favorite / Wishlist toggle
    function toggleFavorite(btn) {
      btn.classList.toggle('is-saved');
      if (btn.classList.contains('is-saved')) {
        showToast('Saved to your POV Indian wishlist! ❤️');
      } else {
        showToast('Removed from wishlist');
      }
    }

    // Copy URL
    function copyShareUrl() {
      const input = document.getElementById('shareUrlInput');
      input.select();
      navigator.clipboard.writeText(input.value).then(() => {
        showToast('Link copied to clipboard! 📋');
      });
    }

    // Handle Visit submission
    function handleVisitSubmit(e) {
      e.preventDefault();
      const form = e.target;
      const btn = document.getElementById('modalVisitSubmitBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
      }

      const fd = new FormData(form);
      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=create')) ?>', {
        method: 'POST',
        body: fd
      })
      .then(res => res.json())
      .then(data => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-regular fa-calendar-check"></i> Request Visit';
        }
        closeModal('modalScheduleVisit');
        if (data.success) {
          showToast('✓ Visit request submitted! Redirecting to My Visits...');
          setTimeout(() => {
            window.location.href = '<?= htmlspecialchars(pov_url('my-visits.php')) ?>';
          }, 1200);
        } else {
          alert(data.message || 'Unable to schedule visit.');
        }
      })
      .catch(err => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-regular fa-calendar-check"></i> Request Visit';
        }
        alert('Network error while scheduling visit.');
      });
    }

    // Photo swapping
    function swapPhoto(idx) {
      const main = document.getElementById('mainGalleryImg');
      const sideImgs = document.querySelectorAll('.rd-gallery-side img');
      if (sideImgs[idx - 1] && main) {
        const temp = main.src;
        main.src = sideImgs[idx - 1].src;
        sideImgs[idx - 1].src = temp;
      }
    }

    // Toast utility
    function showToast(msg) {
      const toast = document.getElementById('rdToast');
      const toastMsg = document.getElementById('toastMsg');
      toastMsg.innerText = msg;
      toast.classList.add('is-show');
      setTimeout(() => {
        toast.classList.remove('is-show');
      }, 3500);
    }
  </script>

  <!-- Leaflet Map JS Library -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <script>
    // Real-Time Location & GPS Engine
    const propLatitude = <?= json_encode($propLat) ?>;
    const propLongitude = <?= json_encode($propLng) ?>;
    const propTitleText = <?= json_encode($item['title'] ?? 'Verified Property') ?>;
    const propLocalityText = <?= json_encode(($item['locality'] ?? '') . ', ' . ($item['city'] ?? '')) ?>;
    const propLandmarksList = <?= json_encode($item['landmarks'] ?? []) ?>;

    let userCurrentLat = null;
    let userCurrentLng = null;
    let userMarker = null;
    let routeLine = null;
    let leafletMapInstance = null;

    function copyPropertyCoordinates(lat, lng) {
      const text = `${lat}, ${lng}`;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
          showToast(`📍 Coordinates copied: ${text}`);
        }).catch(() => {
          showToast(`📍 Coordinates: ${text}`);
        });
      } else {
        showToast(`📍 Coordinates: ${text}`);
      }
    }

    function detectUserCurrentLocation(propLat, propLng) {
      const btn = document.getElementById('btnDetectCurrentLoc');
      if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Detecting GPS...';
        btn.classList.add('is-locating');
      }

      if (!navigator.geolocation) {
        if (btn) {
          btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> GPS Not Supported';
          btn.classList.remove('is-locating');
        }
        showToast('Geolocation is not supported by your browser.');
        return;
      }

      navigator.geolocation.getCurrentPosition(
        function(position) {
          userCurrentLat = position.coords.latitude;
          userCurrentLng = position.coords.longitude;

          if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-circle-check" style="color:#10B981;"></i> Current Location Active';
            btn.classList.remove('is-locating');
          }

          // Calculate distance using Haversine formula
          const distKm = calculateDistanceKm(userCurrentLat, userCurrentLng, propLat, propLng);
          let distText = '';
          let driveEst = '';
          if (distKm < 1) {
            const meters = Math.round(distKm * 1000);
            distText = `${meters} meters`;
            driveEst = `~${Math.max(2, Math.round(meters / 80))} min walk`;
          } else {
            distText = `${distKm.toFixed(1)} km`;
            driveEst = `~${Math.max(3, Math.round(distKm * 2.2))} min drive`;
          }

          // Show Banner
          const banner = document.getElementById('rdLiveLocationBanner');
          const valEl = document.getElementById('rdLiveDistValue');
          const subEl = document.getElementById('rdLiveDistSub');
          const navBtn = document.getElementById('rdLiveDirectionsBtn');

          if (valEl) valEl.textContent = `${distText} (${driveEst})`;
          if (subEl) subEl.textContent = `Your GPS: Lat ${userCurrentLat.toFixed(4)}, Lng ${userCurrentLng.toFixed(4)} → Destination Lat ${propLat.toFixed(4)}, Lng ${propLng.toFixed(4)}`;
          if (navBtn) {
            navBtn.href = `https://www.google.com/maps/dir/?api=1&origin=${userCurrentLat},${userCurrentLng}&destination=${propLat},${propLng}`;
          }
          if (banner) banner.style.display = 'flex';

          showToast(`📍 Your location detected! ${distText} from this property.`);

          // Ensure Live Map view is active
          switchMapDisplayMode('live');

          // Update Leaflet Map if active
          if (leafletMapInstance && window.L) {
            if (userMarker) leafletMapInstance.removeLayer(userMarker);
            if (routeLine) leafletMapInstance.removeLayer(routeLine);

            const userPinIcon = L.divIcon({
              className: 'rd-leaflet-user-pin',
              html: '<div style="width:22px; height:22px; background:#2563EB; border:3px solid #fff; border-radius:50%; box-shadow:0 0 0 6px rgba(37,99,235,0.3); transform:translate(-50%,-50%);"></div>',
              iconSize: [22, 22],
              iconAnchor: [0, 0]
            });

            userMarker = L.marker([userCurrentLat, userCurrentLng], { icon: userPinIcon })
              .addTo(leafletMapInstance)
              .bindPopup(`<strong>Your Current Location</strong><br><span style="font-size:12px; color:#2563EB; font-weight:700;">Distance: ${distText} (${driveEst})</span>`)
              .openPopup();

            routeLine = L.polyline([[userCurrentLat, userCurrentLng], [propLat, propLng]], {
              color: '#2563EB',
              weight: 3,
              dashArray: '6, 8',
              opacity: 0.85
            }).addTo(leafletMapInstance);

            leafletMapInstance.fitBounds([[userCurrentLat, userCurrentLng], [propLat, propLng]], {
              padding: [50, 50]
            });
          }
        },
        function(error) {
          if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Detect My Current Location';
            btn.classList.remove('is-locating');
          }
          let errText = 'Please enable location permissions in your browser.';
          if (error.code === error.TIMEOUT) errText = 'Location detection timed out.';
          showToast(`⚠️ ${errText}`);
        },
        { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 }
      );
    }

    function calculateDistanceKm(lat1, lon1, lat2, lon2) {
      const R = 6371; // Earth's radius in km
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return R * c;
    }

    function closeLiveLocationBanner() {
      const banner = document.getElementById('rdLiveLocationBanner');
      if (banner) banner.style.display = 'none';
    }

    function switchMapDisplayMode(mode) {
      const liveWrap = document.getElementById('rdLiveMapWrapper');
      const overviewWrap = document.getElementById('rdOverviewMapWrapper');
      const btnLive = document.getElementById('btnTabInteractiveMap');
      const btnOverview = document.getElementById('btnTabOverviewMap');

      if (mode === 'live') {
        if (liveWrap) liveWrap.style.display = 'block';
        if (overviewWrap) overviewWrap.style.display = 'none';
        if (btnLive) btnLive.classList.add('is-active');
        if (btnOverview) btnOverview.classList.remove('is-active');
        if (leafletMapInstance) {
          setTimeout(() => { leafletMapInstance.invalidateSize(); }, 120);
        }
      } else {
        if (liveWrap) liveWrap.style.display = 'none';
        if (overviewWrap) overviewWrap.style.display = 'block';
        if (btnLive) btnLive.classList.remove('is-active');
        if (btnOverview) btnOverview.classList.add('is-active');
      }
    }

    // Initialize Leaflet Map
    function initLeafletMap() {
      const mapEl = document.getElementById('rdLeafletMap');
      if (!mapEl || !window.L) return;

      try {
        leafletMapInstance = L.map('rdLeafletMap', {
          center: [propLatitude, propLongitude],
          zoom: 15,
          zoomControl: false,
          scrollWheelZoom: false
        });

        // Top-left zoom control
        L.control.zoom({ position: 'bottomright' }).addTo(leafletMapInstance);

        // OpenStreetMap Carto tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '© OpenStreetMap contributors'
        }).addTo(leafletMapInstance);

        // 300m Privacy radius circle (as per PRD)
        L.circle([propLatitude, propLongitude], {
          color: '#D95D39',
          fillColor: '#D95D39',
          fillOpacity: 0.14,
          radius: 280,
          weight: 2,
          dashArray: '5, 6'
        }).addTo(leafletMapInstance);

        // Custom Terracotta Property Marker with radar pulse
        const propMarkerIcon = L.divIcon({
          className: 'rd-leaflet-prop-pin',
          html: '<div style="position:relative; transform:translate(-50%,-50%); cursor:pointer;">' +
                  '<div style="width:38px; height:38px; border-radius:50%; background:#D95D39; display:grid; place-items:center; color:#fff; font-size:16px; box-shadow:0 6px 18px rgba(217,93,57,0.45); border:3px solid #fff;">' +
                    '<i class="fa-solid fa-house"></i>' +
                  '</div>' +
                  '<div style="position:absolute; top:-2px; left:-2px; width:42px; height:42px; border-radius:50%; border:2px dashed #D95D39; animation:rdRadarPulse 2.8s infinite ease-out;"></div>' +
                '</div>',
          iconSize: [38, 38],
          iconAnchor: [0, 0]
        });

        L.marker([propLatitude, propLongitude], { icon: propMarkerIcon })
          .addTo(leafletMapInstance)
          .bindPopup(`<strong>${propTitleText}</strong><br><span style="font-size:12px; color:#64748B;">${propLocalityText}</span><br><span style="font-size:11.5px; font-weight:700; color:#D95D39;">GPS: ${propLatitude.toFixed(4)}° N, ${propLongitude.toFixed(4)}° E</span>`)
          .openPopup();

        // Plot nearby landmarks
        if (Array.isArray(propLandmarksList)) {
          propLandmarksList.forEach((lm, idx) => {
            const offsets = [
              [0.0035, 0.0025],
              [-0.0030, -0.0035],
              [0.0020, -0.0040],
              [-0.0038, 0.0030]
            ];
            const off = offsets[idx % offsets.length];
            const lmLat = propLatitude + off[0];
            const lmLng = propLongitude + off[1];

            const lmIcon = L.divIcon({
              className: 'rd-leaflet-lm-pin',
              html: `<div style="background:#FFFFFF; border:1px solid #CBD5E1; box-shadow:0 2px 8px rgba(0,0,0,0.12); border-radius:999px; padding:3px 8px; font-size:11px; font-weight:700; color:#1E293B; display:inline-flex; align-items:center; gap:4px; transform:translate(-50%,-50%); white-space:nowrap;">
                       <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:${lm.color || '#D95D39'};"></span>
                       <span>${lm.name}</span>
                     </div>`,
              iconSize: [120, 24],
              iconAnchor: [0, 0]
            });

            L.marker([lmLat, lmLng], { icon: lmIcon })
              .addTo(leafletMapInstance)
              .bindPopup(`<strong>${lm.name}</strong><br><span style="color:#059669; font-weight:600; font-size:12px;">${lm.distance || ''} • ${lm.time || ''}</span>`);
          });
        }
      } catch (e) {
        console.warn('Leaflet map init notice:', e);
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      initLeafletMap();
    });
  </script>
</body>
</html>
