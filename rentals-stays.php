<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$current_page = 'listings';
$page_title = 'Hotels, Rentals & Stays Across India | POV Indian';
$page_description = 'Landmark-first discovery for verified hotels, boutique homestays, student PGs, and 11-month family rentals across India.';
$body_class = 'category-landing rentals-page';
$extra_css = 'css/rentals.css';

// Read query params if any
$intent = trim($_GET['intent'] ?? 'all');
$city = trim($_GET['city'] ?? 'all');
$landmark = trim($_GET['landmark'] ?? '');
$budget = trim($_GET['budget'] ?? 'all');
$q = trim($_GET['q'] ?? '');

$rentals = pov_get_rentals($intent, $city, $landmark, $budget, $q);

// Helper to determine BHK / Room format
function pov_landing_bhk_type($item) {
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

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

<style>
/* 2-Column Editorial Hero (Matches User Design Screenshot) */
.rentals-hero-v2 {
  padding: 125px 0 45px;
  background: #FAF7F2;
  position: relative;
}
.rentals-hero-grid {
  display: grid;
  grid-template-columns: 1.15fr 1fr;
  gap: 48px;
  align-items: center;
}
@media (max-width: 991px) {
  .rentals-hero-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }
}
.hero-left-intro {
  max-width: 540px;
}
.hero-eyebrow-tag {
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: 1.2px;
  text-transform: uppercase;
  color: #D95D39;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.hero-editorial-title {
  font-family: var(--font-serif, 'Playfair Display', Georgia, serif);
  font-size: clamp(34px, 4.4vw, 54px);
  font-weight: 800;
  line-height: 1.14;
  color: #142132;
  margin: 0 0 16px;
  letter-spacing: -0.02em;
}
.hero-editorial-title .accent-terracotta {
  color: #D95D39;
  font-style: italic;
  font-weight: 600;
}
.hero-editorial-subtitle {
  font-size: clamp(14.5px, 1.25vw, 16.5px);
  line-height: 1.6;
  color: #64748B;
  margin: 0 0 24px;
}
.hero-trust-badges {
  display: flex;
  align-items: center;
  gap: 22px;
  flex-wrap: wrap;
}
.trust-badge-item {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 13.5px;
  font-weight: 600;
  color: #334155;
}
.trust-badge-item i {
  color: #10B981;
  font-size: 14px;
}
.trust-badge-item i.fa-clock {
  color: #64748B;
}

/* Right Clean Search Card */
.hero-search-card {
  background: #ffffff;
  border-radius: 24px;
  padding: 26px 28px;
  border: 1px solid #EAE4DC;
  box-shadow: 0 16px 40px rgba(26, 33, 48, 0.06);
}
.search-card-title {
  font-size: 17px;
  font-weight: 800;
  color: #142132;
  margin: 0 0 14px;
}
.intent-segmented-bar {
  display: flex;
  background: #F0EAE1;
  border-radius: 999px;
  padding: 4px;
  gap: 4px;
  margin-bottom: 12px;
}
.intent-seg-btn {
  flex: 1;
  border: none;
  background: transparent;
  padding: 8px 14px;
  border-radius: 999px;
  font-size: 13.5px;
  font-weight: 700;
  color: #78716C;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: center;
}
.intent-seg-btn:hover {
  color: #142132;
}
.intent-seg-btn.active {
  background: #ffffff;
  color: #D95D39;
  border: 1px solid #FED7AA;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}
.intent-hint-text {
  font-size: 12.5px;
  color: #64748B;
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 16px;
}
.intent-hint-text .hint-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #D95D39;
  display: inline-block;
}
.field-micro-label {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  color: #8C96A5;
  margin-bottom: 6px;
  display: block;
}
.clean-search-input-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  border: 1.5px solid #CBD5E1;
  border-radius: 12px;
  padding: 10px 14px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.clean-search-input-wrap:focus-within {
  border-color: #D95D39;
  box-shadow: 0 0 0 3px rgba(217, 93, 57, 0.12);
}
.clean-search-input-wrap i {
  color: #94A3B8;
  font-size: 14px;
}
.clean-search-input-wrap input {
  width: 100%;
  border: none;
  background: transparent;
  outline: none;
  font-size: 13.5px;
  font-weight: 500;
  color: #142132;
}
.form-filters-row {
  display: grid;
  grid-template-columns: 1.15fr 1.35fr auto;
  gap: 10px;
  align-items: center;
  margin-top: 12px;
}
@media (max-width: 560px) {
  .form-filters-row {
    grid-template-columns: 1fr;
  }
}
.filter-select-pill {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 1.5px solid #CBD5E1;
  border-radius: 12px;
  padding: 7px 12px;
  background: #ffffff;
  gap: 8px;
  height: 44px;
  box-sizing: border-box;
}
.filter-select-pill .pill-label {
  font-size: 11.5px;
  color: #8C96A5;
  font-weight: 600;
  white-space: nowrap;
}
.filter-select-pill select {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  font-weight: 700;
  color: #142132;
  width: 100%;
  cursor: pointer;
}
.pill-budget-wrap {
  display: flex;
  flex-direction: column;
  width: 100%;
}
.pill-budget-wrap .pill-label {
  font-size: 9.5px;
  color: #8C96A5;
  font-weight: 800;
  text-transform: uppercase;
}
.pill-budget-wrap select {
  border: none;
  background: transparent;
  outline: none;
  font-size: 12.5px;
  font-weight: 700;
  color: #142132;
  cursor: pointer;
  padding: 0;
  margin: 0;
}
.btn-clean-search {
  height: 44px;
  padding: 0 20px;
  background: #D95D39;
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  transition: all 0.2s ease;
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(217, 93, 57, 0.25);
}
.btn-clean-search:hover {
  background: #C2410C;
  transform: translateY(-1px);
}
.hero-try-pills {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 14px;
}
.try-label {
  font-size: 12px;
  color: #8C96A5;
  font-weight: 600;
}
.try-pill {
  border: 1px solid #CBD5E1;
  background: #ffffff;
  padding: 4px 11px;
  border-radius: 999px;
  font-size: 12px;
  color: #334155;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}
.try-pill:hover {
  border-color: #D95D39;
  color: #D95D39;
}

/* Unified Clean Search Box */
.clean-search-card {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #EAE4DC;
  padding: 12px 14px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.04);
  margin-bottom: 12px;
  position: relative;
}
.clean-search-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
}
.clean-search-input-wrap {
  flex: 2;
  min-width: 240px;
  position: relative;
}
.clean-search-input-wrap i.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #D95D39;
  font-size: 14px;
  pointer-events: none;
}
.clean-search-input-wrap input {
  width: 100%;
  height: 46px;
  padding: 8px 14px 8px 38px;
  border-radius: 10px;
  border: 1px solid #E2E8F0;
  font-size: 14px;
  outline: none;
  background: #FAF7F2;
  transition: border-color 0.2s, background 0.2s;
}
.clean-search-input-wrap input:focus {
  border-color: #D95D39;
  background: #fff;
}
.clean-select-wrap {
  flex: 1;
  min-width: 135px;
}
.clean-select-wrap select {
  width: 100%;
  height: 46px;
  padding: 0 12px;
  border-radius: 10px;
  border: 1px solid #E2E8F0;
  font-size: 13.5px;
  font-weight: 700;
  color: #334155;
  background: #FAF7F2;
  outline: none;
  cursor: pointer;
}
.clean-search-btn {
  height: 46px;
  padding: 0 24px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  background: #D95D39;
  color: #fff;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 3px 12px rgba(217,93,57,0.25);
  transition: background 0.2s;
}
.clean-search-btn:hover {
  background: #B54E28;
}

/* Autocomplete Dropdown Popover */
.autocomplete-popover {
  position: absolute;
  top: calc(100% + 6px);
  left: 14px;
  right: 14px;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #CBD5E1;
  box-shadow: 0 12px 30px rgba(0,0,0,0.12);
  z-index: 1000;
  display: none;
  overflow: hidden;
  max-height: 320px;
  overflow-y: auto;
}
.autocomplete-item {
  padding: 10px 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  border-bottom: 1px solid #F1F5F9;
  transition: background 0.15s;
}
.autocomplete-item:last-child {
  border-bottom: none;
}
.autocomplete-item:hover {
  background: #FFF5ED;
}
.autocomplete-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #F1F5F9;
  color: #D95D39;
  display: grid;
  place-items: center;
  font-size: 13px;
  flex-shrink: 0;
}
.autocomplete-text {
  flex: 1;
}
.autocomplete-text strong {
  display: block;
  font-size: 13.5px;
  color: #142132;
  margin-bottom: 1px;
}
.autocomplete-text span {
  font-size: 11.5px;
  color: #64748B;
}

/* Landmark Chips Row */
.landmarks-quick-row {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 8px;
  font-size: 12px;
  color: #64748B;
  margin-bottom: 22px;
}
.landmark-quick-tag {
  background: #FFF5ED;
  color: #D95D39;
  border: 1px solid #FED7AA;
  padding: 4px 10px;
  border-radius: 999px;
  font-weight: 600;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.15s ease;
}
.landmark-quick-tag:hover {
  background: #D95D39;
  color: #ffffff;
  border-color: #D95D39;
}

/* 1-Tap Filter Capsule Bar */
.quick-filter-strip {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 22px;
  padding: 7px 10px 7px 16px;
  background: #ffffff;
  border-radius: 999px;
  border: 1px solid #EAE4DC;
  box-shadow: 0 2px 12px rgba(0,0,0,0.03);
}
.quick-filter-pills-left {
  display: flex;
  align-items: center;
  gap: 7px;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
  flex: 1;
}
.quick-filter-pills-left::-webkit-scrollbar {
  display: none;
}
.filter-label-tag {
  font-size: 11px;
  font-weight: 800;
  color: #64748B;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  white-space: nowrap;
  margin-right: 4px;
}
.filter-v-divider {
  display: inline-block;
  width: 1px;
  height: 18px;
  background: #CBD5E1;
  margin: 0 3px;
  flex-shrink: 0;
}
.s-filter-chip {
  height: 33px;
  padding: 0 13px;
  border-radius: 999px;
  border: 1px solid #E2E8F0;
  background: #F8FAFC;
  color: #334155;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
  flex-shrink: 0;
  transition: all 0.15s ease;
}
.s-filter-chip:hover {
  background: #F1F5F9;
  border-color: #CBD5E1;
  color: #0F172A;
}
.s-filter-chip.active {
  background: #142132 !important;
  color: #ffffff !important;
  border-color: #142132 !important;
  box-shadow: 0 2px 8px rgba(20,33,50,0.2);
}
.s-filter-chip.saved-chip {
  background: #FFF1F2;
  color: #BE123C;
  border-color: #FECDD3;
}
.s-filter-chip.saved-chip.active {
  background: #E11D48 !important;
  color: #ffffff !important;
  border-color: #E11D48 !important;
}
.saved-count-pill {
  background: #FFE4E6;
  color: #9F1239;
  font-size: 10.5px;
  font-weight: 800;
  padding: 1px 6px;
  border-radius: 999px;
  margin-left: 2px;
}
.s-filter-chip.saved-chip.active .saved-count-pill {
  background: #9F1239;
  color: #ffffff;
}
.reset-filter-btn {
  height: 33px;
  padding: 0 12px;
  border-radius: 999px;
  border: 1px solid #FEE2E2;
  background: #FEF2F2;
  color: #DC2626;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  white-space: nowrap;
  flex-shrink: 0;
  transition: all 0.15s ease;
}
.reset-filter-btn:hover {
  background: #FEE2E2;
  color: #B91C1C;
}

/* Card Styling */
.landing-card-item {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #EAE4DC;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 3px 14px rgba(0,0,0,0.03);
  transition: transform 0.2s, box-shadow 0.2s;
}
.landing-card-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

/* Lightbox Modal */
.pov-lightbox-backdrop {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(11, 15, 23, 0.92);
  z-index: 100000;
  align-items: center;
  justify-content: center;
  padding: 16px;
  backdrop-filter: blur(4px);
}
.pov-lightbox-content {
  max-width: 780px;
  width: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.pov-lightbox-image-wrap {
  width: 100%;
  height: 440px;
  border-radius: 14px;
  overflow: hidden;
  background: #000;
  position: relative;
}
.pov-lightbox-image-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: opacity 0.2s ease;
}
.pov-lightbox-nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: rgba(255,255,255,0.85);
  border: none;
  font-size: 16px;
  color: #142132;
  cursor: pointer;
  display: grid;
  place-items: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  transition: all 0.15s;
}
.pov-lightbox-nav-btn:hover {
  background: #fff;
  transform: translateY(-50%) scale(1.05);
}
.pov-lightbox-prev { left: 14px; }
.pov-lightbox-next { right: 14px; }
.pov-lightbox-thumbs {
  display: flex;
  gap: 8px;
  justify-content: center;
  overflow-x: auto;
  padding: 4px 0;
}
.pov-lightbox-thumb {
  width: 60px;
  height: 44px;
  border-radius: 6px;
  overflow: hidden;
  cursor: pointer;
  opacity: 0.6;
  border: 2px solid transparent;
  transition: all 0.15s;
}
.pov-lightbox-thumb.active {
  opacity: 1;
  border-color: #D95D39;
}
.pov-lightbox-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>

<!-- 1. EDITORIAL HERO SECTION (2-Column Warm Layout Matching Screenshot) -->
<section class="rentals-hero-v2">
  <div class="container rentals-hero-grid">
    <!-- Left Column: Editorial Intro -->
    <div class="hero-left-intro">
      <div class="hero-eyebrow-tag">— YOUR NEXT PLACE, IN CONTEXT</div>
      <h1 class="hero-editorial-title">
        Find a place that fits<br>
        <span class="accent-terracotta">your life.</span>
      </h1>
      <p class="hero-editorial-subtitle">
        Search by the landmarks you already know. Fresh listings, honest distances, and local details for the way India actually moves.
      </p>
      <div class="hero-trust-badges">
        <span class="trust-badge-item"><i class="fa-solid fa-circle-check" style="color:#10B981;"></i> Verified owners</span>
        <span class="trust-badge-item"><i class="fa-regular fa-clock" style="color:#64748B;"></i> Fresh listings daily</span>
      </div>
    </div>

    <!-- Right Column: Clean Search Card -->
    <div class="hero-search-card">
      <h3 class="search-card-title">What are you looking for?</h3>
      
      <!-- Segmented Intent Switcher inside Card -->
      <div class="intent-segmented-bar" role="tablist">
        <button type="button" class="intent-seg-btn <?= ($intent === 'long_term' || $intent === 'all') ? 'active' : '' ?>" onclick="switchHeroIntent('long_term', this)">Rent</button>
        <button type="button" class="intent-seg-btn <?= $intent === 'pg_shared' ? 'active' : '' ?>" onclick="switchHeroIntent('pg_shared', this)">PG & Shared</button>
        <button type="button" class="intent-seg-btn <?= $intent === 'short_stay' ? 'active' : '' ?>" onclick="switchHeroIntent('short_stay', this)">Stays</button>
      </div>

      <div class="intent-hint-text" id="intentHintText">
        <span class="hint-dot"></span> <span id="hintTextLabel"><?= $intent === 'short_stay' ? 'Hotels & boutique homestays for trips' : ($intent === 'pg_shared' ? 'Student PGs, mess food & shared living' : 'Monthly homes, close to the places you know') ?></span>
      </div>

      <form action="<?= htmlspecialchars(pov_url('rental-search.php')) ?>" method="GET" class="hero-clean-form" id="rentalSearchForm">
        <input type="hidden" name="intent" id="formIntent" value="<?= htmlspecialchars($intent === 'all' ? 'long_term' : $intent) ?>" />

        <div class="form-field-block">
          <label class="field-micro-label">START WITH A PLACE YOU KNOW</label>
          <div class="clean-search-input-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="landmark" id="searchLandmark" placeholder="Area, landmark, college or station" value="<?= htmlspecialchars($landmark) ?>" />
          </div>
        </div>

        <div class="form-filters-row">
          <div class="filter-select-pill">
            <span class="pill-label">City</span>
            <select name="city" id="searchCity">
              <option value="all" <?= $city === 'all' ? 'selected' : '' ?>>All India</option>
              <option value="Pune" <?= (strcasecmp($city, 'Pune') === 0 || $city === 'all') ? 'selected' : '' ?>>Pune</option>
              <option value="Jaipur" <?= strcasecmp($city, 'Jaipur') === 0 ? 'selected' : '' ?>>Jaipur</option>
              <option value="Bangalore" <?= strcasecmp($city, 'Bangalore') === 0 ? 'selected' : '' ?>>Bangalore</option>
              <option value="Kota" <?= strcasecmp($city, 'Kota') === 0 ? 'selected' : '' ?>>Kota</option>
              <option value="Delhi NCR" <?= strcasecmp($city, 'Delhi NCR') === 0 ? 'selected' : '' ?>>Delhi NCR</option>
              <option value="Udaipur" <?= strcasecmp($city, 'Udaipur') === 0 ? 'selected' : '' ?>>Udaipur</option>
              <option value="Goa" <?= strcasecmp($city, 'Goa') === 0 ? 'selected' : '' ?>>Goa</option>
              <option value="Nashik" <?= strcasecmp($city, 'Nashik') === 0 ? 'selected' : '' ?>>Nashik</option>
              <option value="Lucknow" <?= strcasecmp($city, 'Lucknow') === 0 ? 'selected' : '' ?>>Lucknow</option>
              <option value="Kochi" <?= strcasecmp($city, 'Kochi') === 0 ? 'selected' : '' ?>>Kochi</option>
              <option value="Hyderabad" <?= strcasecmp($city, 'Hyderabad') === 0 ? 'selected' : '' ?>>Hyderabad</option>
            </select>
          </div>

          <div class="filter-select-pill">
            <div class="pill-budget-wrap">
              <span class="pill-label">Monthly budget</span>
              <select name="budget">
                <option value="all" <?= $budget === 'all' ? 'selected' : '' ?>>Any budget</option>
                <option value="under-10k" <?= $budget === 'under-10k' ? 'selected' : '' ?>>Under ₹10k</option>
                <option value="10k-20k" <?= $budget === '10k-20k' ? 'selected' : '' ?>>₹10k – ₹20k</option>
                <option value="20k-40k" <?= $budget === '20k-40k' ? 'selected' : '' ?>>₹20k – ₹40k</option>
                <option value="above-40k" <?= $budget === 'above-40k' ? 'selected' : '' ?>>₹40k+</option>
              </select>
            </div>
            <i class="fa-solid fa-sliders pill-icon"></i>
          </div>

          <button type="submit" class="btn-clean-search">
            <i class="fa-solid fa-magnifying-glass"></i> Search
          </button>
        </div>

        <div class="hero-try-pills">
          <span class="try-label">Try:</span>
          <button type="button" class="try-pill" onclick="applyQuickLandmark('Pune station')">near Pune station</button>
          <button type="button" class="try-pill" onclick="applyQuickLandmark('Hinjewadi')">near Hinjewadi</button>
          <button type="button" class="try-pill" onclick="applyQuickLandmark('Deccan Metro')">near Deccan Metro</button>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- 2. CURATED PICKS (Matching User Screenshot: "Places worth a closer look") -->
<section class="curated-section">
  <div class="container">
    <div class="curated-header">
      <div class="curated-header-left">
        <span class="curated-eyebrow">PICKED FOR <?= strtoupper(htmlspecialchars($city === 'all' ? 'PUNE & ACROSS INDIA' : $city)) ?></span>
        <h2 class="curated-title">Places worth a closer look.</h2>
        <p class="curated-sub">A small, considered set of homes and stays — not an endless scroll.</p>
      </div>
      <a href="<?= htmlspecialchars(pov_url('rental-search.php?city=' . urlencode($city === 'all' ? 'Pune' : $city))) ?>" class="curated-see-all">See all in <?= htmlspecialchars($city === 'all' ? 'Pune' : $city) ?> <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <div class="curated-cards-grid">
      <!-- Card 1: Quiet 1 BHK near Deccan Gymkhana -->
      <article class="curated-card">
        <div class="curated-media">
          <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=109')) ?>" style="display:block; width:100%; height:100%;">
            <img src="<?= htmlspecialchars(pov_url('assets/img/listings/realestate_03.webp')) ?>" alt="Quiet 1 BHK near Deccan Gymkhana" loading="lazy" />
          </a>
          <span class="curated-badge-pill">Fresh this morning</span>
          <button type="button" class="curated-fav-btn" aria-label="Save to wishlist" onclick="toggleLandingWishlist(109, this, event)"><i class="fa-regular fa-heart"></i></button>
        </div>
        <div class="curated-body">
          <h3 class="curated-card-title">
            <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=109')) ?>" style="color:inherit; text-decoration:none;">Quiet 1 BHK near Deccan Gymkhana</a>
          </h3>
          <div class="curated-location"><i class="fa-solid fa-location-dot"></i> Prabhat Road, Pune</div>
          <div class="curated-price-row">
            <strong>₹18,500</strong> <span>/ month</span>
          </div>
          <div class="curated-specs">1 bed • 1 bath • 680 sq ft</div>
          <div class="curated-proximity"><i class="fa-solid fa-bolt"></i> 8 min walk to Deccan Metro</div>
        </div>
      </article>

      <!-- Card 2: Bright room in a 3-bed shared home -->
      <article class="curated-card">
        <div class="curated-media">
          <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=106')) ?>" style="display:block; width:100%; height:100%;">
            <img src="<?= htmlspecialchars(pov_url('assets/img/listings/college_01.jpg')) ?>" alt="Bright room in a 3-bed shared home" loading="lazy" />
          </a>
          <span class="curated-badge-pill">Verified owner</span>
          <button type="button" class="curated-fav-btn" aria-label="Save to wishlist" onclick="toggleLandingWishlist(106, this, event)"><i class="fa-regular fa-heart"></i></button>
        </div>
        <div class="curated-body">
          <h3 class="curated-card-title">
            <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=106')) ?>" style="color:inherit; text-decoration:none;">Bright room in a 3-bed shared home</a>
          </h3>
          <div class="curated-location"><i class="fa-solid fa-location-dot"></i> College Road, Nashik</div>
          <div class="curated-price-row">
            <strong>₹7,800</strong> <span>/ month</span>
          </div>
          <div class="curated-specs">Private room • Wi-Fi • Meals optional</div>
          <div class="curated-proximity"><i class="fa-solid fa-bolt"></i> Near BYK College & City Centre Mall</div>
        </div>
      </article>

      <!-- Card 3: Courtyard stay in the old city -->
      <article class="curated-card">
        <div class="curated-media">
          <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=101')) ?>" style="display:block; width:100%; height:100%;">
            <img src="<?= htmlspecialchars(pov_url('assets/img/listings/hotel_01.jpg')) ?>" alt="Courtyard stay in the old city" loading="lazy" />
          </a>
          <span class="curated-badge-pill">Popular for weekends</span>
          <button type="button" class="curated-fav-btn" aria-label="Save to wishlist" onclick="toggleLandingWishlist(101, this, event)"><i class="fa-regular fa-heart"></i></button>
        </div>
        <div class="curated-body">
          <h3 class="curated-card-title">
            <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=101')) ?>" style="color:inherit; text-decoration:none;">Courtyard stay in the old city</a>
          </h3>
          <div class="curated-location"><i class="fa-solid fa-location-dot"></i> Bapu Bazaar, Jaipur</div>
          <div class="curated-price-row">
            <strong>₹2,950</strong> <span>/ night</span>
          </div>
          <div class="curated-specs">2 guests • Breakfast • Self check-in</div>
          <div class="curated-proximity"><i class="fa-solid fa-bolt"></i> 12 min to Hawa Mahal by auto</div>
        </div>
      </article>
    </div>
  </div>
</section>

<main class="simple-rentals-landing" style="padding-top:10px; padding-bottom:70px; background:#FAF7F2; min-height:60vh;">
  <div class="container">

    <!-- 5. Sleek 1-Tap Capsule Filter Strip -->
    <div class="quick-filter-strip">
      <div class="quick-filter-pills-left">
        <span class="filter-label-tag">
          <i class="fa-solid fa-sliders" style="color:#D95D39; font-size:12px;"></i> Filter:
        </span>

        <button type="button" class="s-filter-chip s-landing-bhk" onclick="toggleLandingBhk('1bhk', this)">1 BHK / Studio</button>
        <button type="button" class="s-filter-chip s-landing-bhk" onclick="toggleLandingBhk('2bhk', this)">2 BHK</button>
        <button type="button" class="s-filter-chip s-landing-bhk" onclick="toggleLandingBhk('single', this)">Single Room</button>
        <button type="button" class="s-filter-chip s-landing-bhk" onclick="toggleLandingBhk('sharing', this)">Sharing Bed</button>

        <span class="filter-v-divider"></span>

        <button type="button" class="s-filter-chip s-quick-pill" onclick="toggleQuickLandingTag('pure_veg', this)">
          <i class="fa-solid fa-leaf" style="color:#059669;"></i> Pure Veg
        </button>

        <button type="button" class="s-filter-chip s-quick-pill" onclick="toggleQuickLandingTag('no_curfew', this)">
          <i class="fa-regular fa-clock" style="color:#2563EB;"></i> 24x7 Open
        </button>

        <button type="button" class="s-filter-chip s-quick-pill" onclick="toggleQuickLandingTag('zero_brokerage', this)">
          <i class="fa-solid fa-handshake-simple" style="color:#0F172A;"></i> Zero Brokerage
        </button>

        <button type="button" class="s-filter-chip s-quick-pill" onclick="toggleQuickLandingTag('under_15k', this)">
          Under ₹15k
        </button>

        <button type="button" id="landingSavedFilterBtn" onclick="toggleLandingSavedOnly(this)" class="s-filter-chip saved-chip">
          <i class="fa-solid fa-heart" style="color:#E11D48;"></i> Saved <span class="saved-count-pill" id="landingSavedBadge">0</span>
        </button>
      </div>

      <button type="button" class="reset-filter-btn" onclick="resetLandingFilters()" title="Reset all filters">
        <i class="fa-solid fa-rotate-left"></i> Reset
      </button>
    </div>

    <!-- 6. Results Header Row (PRD-Aligned: Sort + 'I Need a Property' + 'List Property') -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
      <div>
        <h2 style="font-family:var(--font-serif, Georgia); font-size:22px; font-weight:800; color:#142132; margin:0 0 2px;">
          Verified Accommodations
        </h2>
        <p style="margin:0; font-size:13.5px; color:#64748B;">
          Showing <strong style="color:#142132;" id="landingCountLabel"><?= count($rentals) ?></strong> verified properties across India
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <!-- Sort Dropdown -->
        <div style="display:flex; align-items:center; gap:6px;">
          <span style="font-size:12px; font-weight:700; color:#64748B;"><i class="fa-solid fa-arrow-down-short-wide"></i> Sort:</span>
          <select id="landingSortSelect" onchange="runLandingSort(this.value)" style="height:36px; padding:0 8px; border-radius:8px; border:1px solid #CBD5E1; font-size:12.5px; font-weight:700; color:#334155; background:#fff; outline:none; cursor:pointer;">
            <option value="default">Recommended</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="rating">Top Rated (4.8+)</option>
          </select>
        </div>

        <!-- PRD Section 17: 'I Need a Property' MVP Demand-Side Differentiator -->
        <button type="button" onclick="openRequirementModal()" style="height:36px; padding:0 14px; border-radius:8px; background:#FFF5ED; border:1px solid #FED7AA; color:#C2410C; font-size:12.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
          <i class="fa-solid fa-bullhorn"></i> Post Requirement (Free)
        </button>

        <!-- PRD Section 11: Owner / Agent Self-Serve Listing -->
        <button type="button" onclick="openOwnerListingModal()" style="height:36px; padding:0 14px; border-radius:8px; background:#142132; border:none; color:#fff; font-size:12.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
          <i class="fa-solid fa-plus"></i> List Property Free
        </button>
      </div>
    </div>

    <!-- 7. Spacious 3-Column Modern Cards Grid (PRD Card Anatomy) -->
    <div class="simple-cards-grid" id="landingCardsContainer" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:22px;">
      <?php if (empty($rentals)): ?>
        <div style="grid-column:1/-1; background:#ffffff; border-radius:16px; border:1px dashed #CBD5E1; padding:48px 20px; text-align:center;">
          <i class="fa-solid fa-house-chimney-crack" style="font-size:36px; color:#94a3b8; margin-bottom:12px;"></i>
          <h3 style="font-size:18px; font-weight:800; color:#142132; margin:0 0 6px;">No accommodations found</h3>
          <p style="font-size:13.5px; color:#64748B; max-width:400px; margin:0 auto 16px;">Try adjusting your filters or post your exact requirement so local verified owners can connect directly.</p>
          <button type="button" onclick="openRequirementModal()" class="btn" style="padding:8px 18px; font-size:13px; background:#D95D39; color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer;">
            <i class="fa-solid fa-bullhorn"></i> Post Your Requirement
          </button>
        </div>
      <?php else: ?>
        <?php foreach ($rentals as $item): ?>
          <?php 
            $waMessage = urlencode("Hello! I saw your listing '" . $item['title'] . "' (ID: #" . $item['id'] . ") on POV Indian near " . $item['landmark'] . ". Is this currently available?");
            $waUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $item['phone']) . "?text=" . $waMessage;
            $bhkType = pov_landing_bhk_type($item);
            $photoCount = 5 + ($item['id'] % 5);
          ?>
          <article class="simple-card-item landing-card-item" 
                   data-id="<?= $item['id'] ?>"
                   data-intent="<?= htmlspecialchars($item['rental_type']) ?>"
                   data-price="<?= $item['price_num'] ?>"
                   data-rating="<?= $item['rating'] ?? '4.8' ?>"
                   data-bhk="<?= $bhkType ?>"
                   data-food="<?= htmlspecialchars($item['food_policy'] ?? '') ?>"
                   data-curfew="<?= htmlspecialchars($item['gate_curfew'] ?? '') ?>"
                   data-brokerage="<?= htmlspecialchars($item['brokerage_type'] ?? '') ?>">
            
            <!-- Image with Badges & Clickable Lightbox Preview -->
            <div style="position:relative; height:195px; background:#e2e8f0; overflow:hidden;">
              <a href="javascript:void(0)" onclick="openPhotoLightbox('<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>', <?= $item['id'] ?>)" style="display:block; width:100%; height:100%;">
                <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy" style="width:100%; height:100%; object-fit:cover;" />
              </a>
              
              <!-- Category Badge -->
              <span style="position:absolute; top:10px; left:10px; background:rgba(20,33,50,0.85); color:#fff; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px;">
                <?= htmlspecialchars($item['category']) ?>
              </span>

              <!-- Shortlist Wishlist Button -->
              <button type="button" class="wishlist-btn" onclick="toggleLandingShortlist(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>', this, event)" aria-label="Save to shortlist" style="position:absolute; top:10px; right:10px; width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,0.92); border:none; display:grid; place-items:center; cursor:pointer; color:#64748B; font-size:15px; box-shadow:0 2px 8px rgba(0,0,0,0.15); transition:all 0.2s;">
                <i class="fa-regular fa-heart"></i>
              </button>
              
              <!-- Trust Verification Badge (PRD Section 18) -->
              <?php if ($item['trust_badge'] === 'POV Verified'): ?>
                <span style="position:absolute; bottom:10px; left:10px; background:#ECFDF5; color:#065F46; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px; border:1px solid #A7F3D0;">
                  <i class="fa-solid fa-circle-check"></i> POV Verified
                </span>
              <?php else: ?>
                <span style="position:absolute; bottom:10px; left:10px; background:#EFF6FF; color:#1E40AF; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px; border:1px solid #BFDBFE;">
                  <i class="fa-solid fa-file-shield"></i> Title Checked
                </span>
              <?php endif; ?>

              <!-- Photo Count Badge (Opens Gallery Lightbox on Click) -->
              <button type="button" onclick="openPhotoLightbox('<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>', <?= $item['id'] ?>)" style="position:absolute; bottom:10px; right:10px; background:rgba(20,33,50,0.8); backdrop-filter:blur(4px); color:#fff; font-size:10.5px; font-weight:700; padding:3px 8px; border-radius:999px; border:none; cursor:pointer;">
                <i class="fa-solid fa-camera"></i> <?= $photoCount ?> Photos
              </button>
            </div>

            <!-- Body Content -->
            <div style="padding:16px; display:flex; flex-direction:column; flex:1;">
              
              <!-- Landmark Proximity & Locality (PRD Landmark-First Principle) -->
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

              <!-- Bharat Lifestyle Rules (PRD Food / Curfew / Brokerage) -->
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

              <!-- Transparent Price & Move-in Deposit (PRD Section 11) -->
              <div style="margin-top:auto; padding-top:10px; border-top:1px solid #F1ECE5; display:flex; justify-content:space-between; align-items:baseline;">
                <div>
                  <span style="font-size:18px; font-weight:800; color:#142132;"><?= htmlspecialchars($item['price']) ?></span>
                  <span style="font-size:12px; color:#64748B;"><?= htmlspecialchars($item['price_unit']) ?></span>
                </div>
                <span style="font-size:11px; font-weight:600; color:#059669;"><?= htmlspecialchars($item['deposit']) ?></span>
              </div>

              <!-- Dual Actions: WhatsApp Enquiry + In-Page Instant Visit / Details -->
              <div style="display:flex; gap:8px; margin-top:12px;">
                <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" style="flex:1; background:#22C55E; color:#fff; text-decoration:none; text-align:center; padding:8px 8px; border-radius:8px; font-weight:700; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:5px;">
                  <i class="fa-brands fa-whatsapp"></i> WhatsApp
                </a>

                <?php if ($item['rental_type'] === 'long_term' || $item['rental_type'] === 'pg_shared'): ?>
                  <button type="button" onclick="openScheduleVisitModal(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars($item['price']) ?>', '<?= htmlspecialchars(addslashes($item['landmark_distance'])) ?>', '<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>')" style="flex:1; background:#FFF5ED; border:1px solid #FED7AA; color:#C2410C; text-align:center; padding:8px 8px; border-radius:8px; font-weight:700; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:5px; cursor:pointer;">
                    <i class="fa-regular fa-calendar-check"></i> Visit
                  </button>
                <?php else: ?>
                  <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>" style="flex:1; background:#FAF7F2; border:1px solid #CBD5E1; color:#142132; text-decoration:none; text-align:center; padding:8px 8px; border-radius:8px; font-weight:700; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:5px;">
                    View Details
                  </a>
                <?php endif; ?>

                <button type="button" onclick="shareLandingProperty('<?= htmlspecialchars(addslashes($item['title'])) ?>', '<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $item['id'])) ?>', '<?= htmlspecialchars($item['price']) ?>', '<?= htmlspecialchars(addslashes($item['landmark_distance'])) ?>', '<?= htmlspecialchars(pov_url('assets/img/listings/' . $item['img'])) ?>')" title="Share this property" style="width:36px; height:36px; border-radius:8px; background:#F8FAFC; border:1px solid #E2E8F0; color:#64748B; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; transition:all 0.2s;" onmouseover="this.style.background='#EEF2F6'; this.style.color='#142132';" onmouseout="this.style.background='#F8FAFC'; this.style.color='#64748B';">
                  <i class="fa-solid fa-share-nodes"></i>
                </button>
              </div>

            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- 8. How It Works: Your 3-Step Accommodation Journey (Matching User Screenshot) -->
    <div id="how-it-works" style="margin-top:70px; padding-top:40px; border-top:1px solid #EAE4DC;">
      <div style="text-align:center; margin-bottom:40px;">
        <span style="font-size:13px; font-weight:700; color:#EA580C; text-transform:uppercase; letter-spacing:0.8px; display:inline-block; margin-bottom:6px;">
          How It Works
        </span>
        <h2 style="font-size:32px; font-weight:800; color:#142132; margin:0 0 8px; letter-spacing:-0.5px;">
          Your 3–Step Accommodation Journey
        </h2>
        <p style="font-size:15px; color:#64748B; margin:0; font-weight:500;">
          Find a place that fits <span style="color:#C85A32; font-style:italic; font-family:serif;">your life.</span>
        </p>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:32px; text-align:center; margin-bottom:60px;">
        <!-- Step 01 -->
        <div style="display:flex; flex-direction:column; align-items:center;">
          <div style="width:48px; height:48px; border-radius:50%; background:#FFF5ED; color:#EA580C; display:grid; place-items:center; font-size:16px; font-weight:800; margin-bottom:16px;">
            01
          </div>
          <h3 style="font-size:18px; font-weight:800; color:#142132; margin:0 0 8px;">Choose a Destination</h3>
          <p style="font-size:14px; color:#64748B; line-height:1.5; margin:0; max-width:280px;">Explore stays by city, landmark, or vibe.</p>
        </div>

        <!-- Step 02 -->
        <div style="display:flex; flex-direction:column; align-items:center;">
          <div style="width:48px; height:48px; border-radius:50%; background:#FFF5ED; color:#EA580C; display:grid; place-items:center; font-size:16px; font-weight:800; margin-bottom:16px;">
            02
          </div>
          <h3 style="font-size:18px; font-weight:800; color:#142132; margin:0 0 8px;">Read Verified Reviews</h3>
          <p style="font-size:14px; color:#64748B; line-height:1.5; margin:0; max-width:280px;">Learn what travellers loved about the property.</p>
        </div>

        <!-- Step 03 -->
        <div style="display:flex; flex-direction:column; align-items:center;">
          <div style="width:48px; height:48px; border-radius:50%; background:#FFF5ED; color:#EA580C; display:grid; place-items:center; font-size:16px; font-weight:800; margin-bottom:16px;">
            03
          </div>
          <h3 style="font-size:18px; font-weight:800; color:#142132; margin:0 0 8px;">Book or Connect Directly</h3>
          <p style="font-size:14px; color:#64748B; line-height:1.5; margin:0; max-width:280px;">Reach out to the host or property with verified details.</p>
        </div>
      </div>
    </div>

    <!-- 9. For Hotel Owners, Landlords & Wardens (Matching User Screenshot) -->
    <div id="for-owners" style="padding-top:40px; padding-bottom:20px; border-top:1px solid #EAE4DC;">
      <div style="max-width:920px;">
        <span style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.8px; color:#0284C7; display:block; margin-bottom:8px;">
          FOR HOTEL OWNERS, LANDLORDS & WARDENS
        </span>
        <h3 style="font-size:24px; font-weight:800; color:#142132; margin:0 0 10px; letter-spacing:-0.3px;">
          Own a Hotel, Homestay, Rental Flat or PG in India?
        </h3>
        <p style="font-size:14.5px; color:#475569; line-height:1.6; margin:0 0 18px;">
          List directly or let our assisted onboarding team handle everything via WhatsApp. Zero brokerage deduction, genuine verified leads, and automated visit coordination.
        </p>

        <!-- Perks list with dark circle checkmarks -->
        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:24px;">
          <div style="display:flex; align-items:center; gap:9px; font-size:14px; font-weight:600; color:#1E293B;">
            <i class="fa-solid fa-circle-check" style="color:#0F172A; font-size:15px;"></i> Free basic listing with verified badge
          </div>
          <div style="display:flex; align-items:center; gap:9px; font-size:14px; font-weight:600; color:#1E293B;">
            <i class="fa-solid fa-circle-check" style="color:#0F172A; font-size:15px;"></i> Landmark-first address mapping
          </div>
          <div style="display:flex; align-items:center; gap:9px; font-size:14px; font-weight:600; color:#1E293B;">
            <i class="fa-solid fa-circle-check" style="color:#0F172A; font-size:15px;"></i> Resident privacy protected (approx pins)
          </div>
          <div style="display:flex; align-items:center; gap:9px; font-size:14px; font-weight:600; color:#1E293B;">
            <i class="fa-solid fa-circle-check" style="color:#0F172A; font-size:15px;"></i> Qualified student, guest & family inquiries
          </div>
        </div>

        <!-- Assisted Listing via WhatsApp -->
        <div style="margin-top:16px;">
          <h4 style="font-size:15.5px; font-weight:800; color:#142132; margin:0 0 6px;">
            Assisted Listing via WhatsApp
          </h4>
          <p style="font-size:13.5px; color:#64748B; margin:0 0 12px; line-height:1.5;">
            Send your property photos, rent & landmark details directly. Our team reviews & publishes in 2 hours.
          </p>
          <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px;">
            <a href="https://wa.me/919876543210?text=<?= urlencode('Hello POV Indian, I want to list my accommodation property.') ?>" 
               target="_blank" rel="noopener noreferrer" 
               style="display:inline-flex; align-items:center; gap:8px; font-size:14.5px; font-weight:700; color:#142132; text-decoration:none; padding:8px 0;">
              <i class="fa-brands fa-whatsapp" style="color:#22C55E; font-size:20px;"></i> WhatsApp to List: +91 98765 43210
            </a>
            <button type="button" onclick="openOwnerListingModal()" style="background:#FAF7F2; border:1px solid #CBD5E1; color:#142132; padding:7px 14px; border-radius:8px; font-size:12.5px; font-weight:700; cursor:pointer;">
              Submit Online Form
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Modal 1: PRD Section 17 'I Need a Property' (Requirement Posting System) -->
<div class="rental-modal-backdrop" id="requirementModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:16px; max-width:540px; width:100%; padding:24px; position:relative; max-height:90vh; overflow-y:auto; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <button type="button" onclick="closeRequirementModal()" style="position:absolute; right:16px; top:16px; background:none; border:none; font-size:22px; cursor:pointer; color:#64748B;">&times;</button>
    
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
      <div style="width:40px; height:40px; border-radius:10px; background:#FFF5ED; color:#EA580C; display:grid; place-items:center; font-size:18px;">
        <i class="fa-solid fa-bullhorn"></i>
      </div>
      <div>
        <h3 style="font-size:18px; font-weight:800; color:#142132; margin:0 0 2px;">Post Your Exact Requirement</h3>
        <p style="font-size:12.5px; color:#64748B; margin:0;">Can't find a place near your landmark? Verified owners will contact you directly.</p>
      </div>
    </div>

    <form id="requirementForm" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>" method="POST" style="display:flex; flex-direction:column; gap:12px;">
      <input type="hidden" name="form_type" value="requirement-post" />
      <input type="hidden" name="redirect" value="rentals-stays.php" />

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Looking For</label>
          <select name="requirement_type" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;">
            <option value="Long-Term Flat (1/2/3 BHK)">Long-Term Flat (1/2/3 BHK)</option>
            <option value="Student PG / Hostel">Student PG / Hostel</option>
            <option value="Daily/Weekly Stay (Hotel/Homestay)">Daily/Weekly Stay</option>
            <option value="Independent House / Villa">Independent House / Villa</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Target City</label>
          <input type="text" name="city" placeholder="e.g. Pune, Kota, Jaipur" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
      </div>

      <div>
        <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Preferred Landmark or Institute</label>
        <input type="text" name="landmark_pref" placeholder="e.g. Near Deccan Metro, Near Allen Sangyan, Near Infosys Circle" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        <div style="font-size:11px; color:#64748B; margin-top:2px;">Crucial for matching properties within 500m to 1.5km walking distance.</div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Max Budget (₹ / mo or night)</label>
          <input type="text" name="budget_max" placeholder="e.g. ₹15,000 / mo" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Move-in / Check-in Date</label>
          <input type="date" name="move_in_date" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Your Name</label>
          <input type="text" name="seeker_name" placeholder="Full Name" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">WhatsApp Number</label>
          <input type="tel" name="phone" placeholder="10-digit mobile number" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
      </div>

      <div>
        <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Specific Preferences (Optional)</label>
        <textarea name="notes" rows="2" placeholder="e.g. Pure veg mess, 24x7 gate open, single occupancy room." style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none; resize:none;"></textarea>
      </div>

      <button type="submit" style="height:44px; margin-top:4px; background:#D95D39; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
        <i class="fa-solid fa-paper-plane"></i> Submit Requirement (Free)
      </button>
    </form>
  </div>
</div>

<!-- Modal 2: PRD Section 16 In-Page Schedule Visit Instant Modal -->
<div class="rental-modal-backdrop" id="scheduleVisitModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:16px; max-width:500px; width:100%; padding:22px; position:relative; max-height:90vh; overflow-y:auto; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <button type="button" onclick="closeScheduleVisitModal()" style="position:absolute; right:16px; top:16px; background:none; border:none; font-size:22px; cursor:pointer; color:#64748B;">&times;</button>
    
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
      <div style="width:38px; height:38px; border-radius:10px; background:#FFF5ED; color:#C2410C; display:grid; place-items:center; font-size:18px;">
        <i class="fa-regular fa-calendar-check"></i>
      </div>
      <div>
        <h3 style="font-size:17px; font-weight:800; color:#142132; margin:0 0 2px;">Schedule Free Physical Visit</h3>
        <p style="font-size:12px; color:#64748B; margin:0;">Zero token money required. Inspect before deciding.</p>
      </div>
    </div>

    <!-- Selected Property Preview Box -->
    <div style="display:flex; align-items:center; gap:12px; background:#FAF7F2; border:1px solid #EAE4DC; border-radius:10px; padding:10px; margin-bottom:14px;">
      <img id="visitPropImg" src="" alt="Property" style="width:50px; height:50px; border-radius:8px; object-fit:cover;" />
      <div style="flex:1; overflow:hidden;">
        <h4 id="visitPropTitle" style="font-size:13.5px; font-weight:800; color:#142132; margin:0 0 2px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;"></h4>
        <div style="font-size:12px; color:#D95D39; font-weight:700;" id="visitPropMeta"></div>
      </div>
    </div>

    <form onsubmit="handleVisitSubmit(event)" style="display:flex; flex-direction:column; gap:12px;">
      <input type="hidden" id="visitPropIdHidden" value="" />

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Preferred Date</label>
          <input type="date" id="visitDateInput" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Time Slot</label>
          <select id="visitSlotSelect" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;">
            <option value="Morning (10 AM – 1 PM)">Morning (10 AM – 1 PM)</option>
            <option value="Afternoon (1 PM – 4 PM)">Afternoon (1 PM – 4 PM)</option>
            <option value="Evening (4 PM – 7:30 PM)">Evening (4 PM – 7:30 PM)</option>
          </select>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Your Name</label>
          <input type="text" id="visitorNameInput" placeholder="Full Name" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">WhatsApp Number</label>
          <input type="tel" id="visitorPhoneInput" placeholder="10-digit number" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
      </div>

      <div>
        <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Special Request for Owner (Optional)</label>
        <input type="text" id="visitNotesInput" placeholder="e.g. Coming with father to inspect parking and mess" style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
      </div>

      <button type="submit" style="height:44px; margin-top:4px; background:#D95D39; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
        <i class="fa-regular fa-calendar-check"></i> Confirm Free Physical Walkthrough
      </button>
    </form>
  </div>
</div>

<!-- Modal 3: In-Page Photo Gallery Lightbox Preview -->
<div class="pov-lightbox-backdrop" id="photoLightboxModal">
  <div class="pov-lightbox-content">
    <div style="display:flex; justify-content:space-between; align-items:center; color:#fff;">
      <div>
        <h4 id="lightboxTitle" style="font-size:16px; font-weight:800; margin:0; color:#fff;">Property Gallery</h4>
        <span id="lightboxCounter" style="font-size:12px; color:#94A3B8;">Photo 1 of 4</span>
      </div>
      <button type="button" onclick="closePhotoLightbox()" style="background:none; border:none; color:#fff; font-size:26px; cursor:pointer;">&times;</button>
    </div>

    <!-- Main Active Photo -->
    <div class="pov-lightbox-image-wrap">
      <img id="lightboxMainImg" src="" alt="Property Preview" />
      <button type="button" class="pov-lightbox-nav-btn pov-lightbox-prev" onclick="prevLightboxPhoto()"><i class="fa-solid fa-chevron-left"></i></button>
      <button type="button" class="pov-lightbox-nav-btn pov-lightbox-next" onclick="nextLightboxPhoto()"><i class="fa-solid fa-chevron-right"></i></button>
    </div>

    <!-- Thumbnails Strip -->
    <div class="pov-lightbox-thumbs" id="lightboxThumbContainer"></div>
  </div>
</div>

<!-- Modal 4: PRD Section 11 Owner Self-Serve Listing Modal -->
<div class="rental-modal-backdrop" id="ownerListingModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:16px; max-width:520px; width:100%; padding:24px; position:relative; max-height:90vh; overflow-y:auto; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <button type="button" onclick="closeOwnerListingModal()" style="position:absolute; right:16px; top:16px; background:none; border:none; font-size:22px; cursor:pointer; color:#64748B;">&times;</button>
    
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
      <div style="width:38px; height:38px; border-radius:10px; background:#ECFDF5; color:#059669; display:grid; place-items:center; font-size:18px;">
        <i class="fa-solid fa-house-chimney-medical"></i>
      </div>
      <div>
        <h3 style="font-size:17px; font-weight:800; color:#142132; margin:0 0 2px;">List Your Property (Free)</h3>
        <p style="font-size:12.5px; color:#64748B; margin:0;">Zero brokerage. Reviewed and verified within 24 hours.</p>
      </div>
    </div>

    <form id="ownerListingForm" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>" method="POST" style="display:flex; flex-direction:column; gap:12px;">
      <input type="hidden" name="form_type" value="list-rental" />
      <input type="hidden" name="redirect" value="rentals-stays.php" />
      
      <div>
        <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Property Type</label>
        <select name="listing_type" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;">
          <option value="11-Month Rental Flat / Builder Floor">11-Month Rental Flat / Builder Floor</option>
          <option value="Student PG / Executive Hostel">Student PG / Executive Hostel</option>
          <option value="Boutique Hotel / Heritage Stay">Boutique Hotel / Heritage Stay</option>
          <option value="Homestay (Rooms with Host)">Homestay (Rooms with Host)</option>
          <option value="Serviced Apartment">Serviced Apartment</option>
        </select>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">City</label>
          <input type="text" name="city" placeholder="e.g. Pune, Jaipur" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Rent / Night (₹)</label>
          <input type="text" name="price" placeholder="e.g. ₹15,000 / mo" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
      </div>

      <div>
        <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Property Headline</label>
        <input type="text" name="title" placeholder="e.g. Sunrise 2BHK Floor or Krishna Boys PG" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
      </div>

      <div>
        <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Nearest Landmark & Locality</label>
        <input type="text" name="landmark" placeholder="e.g. Near Deccan Metro, Prabhat Road" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Owner / Host Name</label>
          <input type="text" name="owner_name" placeholder="Your Name" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
        <div>
          <label style="font-size:12px; font-weight:700; color:#334155; display:block; margin-bottom:4px;">WhatsApp Number</label>
          <input type="tel" name="phone" placeholder="10-digit mobile" required style="width:100%; height:40px; padding:0 10px; border-radius:8px; border:1px solid #CBD5E1; font-size:13px; outline:none;" />
        </div>
      </div>

      <button type="submit" style="height:44px; margin-top:6px; background:#D95D39; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:14px; cursor:pointer;">
        Submit Property for Verification
      </button>
    </form>
  </div>
</div>

<!-- Modal 5: Clean Minimalist Social Share Modal -->
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
let landingCurrentIntent = '<?= htmlspecialchars($intent) ?>';
let landingCurrentBhk = 'all';
let landingShowSavedOnly = false;
let landingTagFilters = {
  pure_veg: false,
  no_curfew: false,
  zero_brokerage: false,
  under_15k: false
};

function switchHeroIntent(type, btn) {
  document.querySelectorAll('.intent-seg-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  
  const formIntent = document.getElementById('formIntent');
  if (formIntent) formIntent.value = type;

  const hint = document.getElementById('hintTextLabel');
  if (hint) {
    if (type === 'short_stay') {
      hint.textContent = 'Hotels & boutique homestays for trips';
    } else if (type === 'pg_shared') {
      hint.textContent = 'Student PGs, mess food & shared living';
    } else {
      hint.textContent = 'Monthly homes, close to the places you know';
    }
  }

  landingCurrentIntent = type;
  runInstantLandingFilter();
}

function applyQuickLandmark(landmark) {
  const lmInput = document.getElementById('searchLandmark') || document.getElementById('landingLandmarkInput');
  if (lmInput) lmInput.value = landmark;
  const form = document.getElementById('rentalSearchForm') || document.getElementById('mainRentalSearchForm');
  if (form) form.submit();
}

// Lightbox Gallery Logic
const galleryPool = [
  '<?= htmlspecialchars(pov_url('assets/img/listings/realestate_03.webp')) ?>',
  '<?= htmlspecialchars(pov_url('assets/img/listings/realestate_01.webp')) ?>',
  '<?= htmlspecialchars(pov_url('assets/img/listings/hotel_01.jpg')) ?>',
  '<?= htmlspecialchars(pov_url('assets/img/listings/college_01.jpg')) ?>'
];
let currentLightboxIndex = 0;
let currentLightboxImages = [];

function openPhotoLightbox(title, mainImgUrl, propId) {
  currentLightboxImages = [mainImgUrl, ...galleryPool.filter(u => u !== mainImgUrl).slice(0, 3)];
  currentLightboxIndex = 0;

  document.getElementById('lightboxTitle').textContent = title;
  updateLightboxView();

  const m = document.getElementById('photoLightboxModal');
  if (m) m.style.display = 'flex';
}

function updateLightboxView() {
  const imgEl = document.getElementById('lightboxMainImg');
  const counterEl = document.getElementById('lightboxCounter');
  const thumbWrap = document.getElementById('lightboxThumbContainer');

  if (imgEl) imgEl.src = currentLightboxImages[currentLightboxIndex];
  if (counterEl) counterEl.textContent = `Photo ${currentLightboxIndex + 1} of ${currentLightboxImages.length}`;

  if (thumbWrap) {
    thumbWrap.innerHTML = '';
    currentLightboxImages.forEach((url, i) => {
      const t = document.createElement('div');
      t.className = `pov-lightbox-thumb ${i === currentLightboxIndex ? 'active' : ''}`;
      t.innerHTML = `<img src="${url}" alt="Thumb" />`;
      t.onclick = () => {
        currentLightboxIndex = i;
        updateLightboxView();
      };
      thumbWrap.appendChild(t);
    });
  }
}

function prevLightboxPhoto() {
  currentLightboxIndex = (currentLightboxIndex - 1 + currentLightboxImages.length) % currentLightboxImages.length;
  updateLightboxView();
}
function nextLightboxPhoto() {
  currentLightboxIndex = (currentLightboxIndex + 1) % currentLightboxImages.length;
  updateLightboxView();
}
function closePhotoLightbox() {
  const m = document.getElementById('photoLightboxModal');
  if (m) m.style.display = 'none';
}

// In-Page Schedule Visit Modal Logic
function openScheduleVisitModal(id, title, price, distance, imgUrl) {
  document.getElementById('visitPropIdHidden').value = id;
  document.getElementById('visitPropTitle').textContent = title;
  document.getElementById('visitPropMeta').textContent = `${price} • ${distance}`;
  document.getElementById('visitPropImg').src = imgUrl;

  // Set default date to tomorrow
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  const dateInput = document.getElementById('visitDateInput');
  if (dateInput) dateInput.value = tomorrow.toISOString().split('T')[0];

  const m = document.getElementById('scheduleVisitModal');
  if (m) m.style.display = 'flex';
}

function closeScheduleVisitModal() {
  const m = document.getElementById('scheduleVisitModal');
  if (m) m.style.display = 'none';
}

function handleVisitSubmit(e) {
  e.preventDefault();
  const name = document.getElementById('visitorNameInput').value;
  const phone = document.getElementById('visitorPhoneInput').value;
  const title = document.getElementById('visitPropTitle').textContent;
  const slot = document.getElementById('visitSlotSelect').value;

  closeScheduleVisitModal();
  showLandingToast(`✅ Visit Requested! The owner of "${title}" will confirm your ${slot} walkthrough on WhatsApp.`);
}

// Wishlist Storage Helper
function getLandingSavedIds() {
  try {
    return JSON.parse(localStorage.getItem('pov_saved_rentals') || '[]');
  } catch (e) {
    return [];
  }
}

function updateLandingWishlistBadge() {
  const saved = getLandingSavedIds();
  const badge = document.getElementById('landingSavedBadge');
  if (badge) badge.textContent = saved.length;

  document.querySelectorAll('.landing-card-item').forEach(card => {
    const id = parseInt(card.dataset.id, 10);
    const btn = card.querySelector('.wishlist-btn');
    if (btn) {
      const isSaved = saved.includes(id);
      btn.style.color = isSaved ? '#E11D48' : '#64748B';
      btn.innerHTML = isSaved ? '<i class="fa-solid fa-heart" style="color:#E11D48;"></i>' : '<i class="fa-regular fa-heart"></i>';
    }
  });

  document.querySelectorAll('.curated-fav-btn').forEach(btn => {
    const onclickStr = btn.getAttribute('onclick') || '';
    const match = onclickStr.match(/toggleLandingWishlist\((\d+)/);
    if (match) {
      const id = parseInt(match[1], 10);
      const isSaved = saved.includes(id);
      btn.classList.toggle('active', isSaved);
      const icon = btn.querySelector('i');
      if (icon) {
        icon.className = isSaved ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
        icon.style.color = isSaved ? '#EF4444' : '';
      }
    }
  });
}

function toggleLandingWishlist(id, btn, event) {
  if (event) event.stopPropagation();
  let saved = getLandingSavedIds();
  const index = saved.indexOf(id);
  const icon = btn.querySelector('i');

  if (index > -1) {
    saved.splice(index, 1);
    btn.classList.remove('active');
    if (icon) {
      icon.className = 'fa-regular fa-heart';
      icon.style.color = '';
    }
    showLandingToast('Removed from saved list');
  } else {
    saved.push(id);
    btn.classList.add('active');
    if (icon) {
      icon.className = 'fa-solid fa-heart';
      icon.style.color = '#EF4444';
    }
    showLandingToast('❤️ Saved to your wishlist!');
  }

  localStorage.setItem('pov_saved_rentals', JSON.stringify(saved));
  updateLandingWishlistBadge();
}

function toggleLandingShortlist(id, title, btn, event) {
  if (event) event.stopPropagation();
  let saved = getLandingSavedIds();
  const index = saved.indexOf(id);

  if (index > -1) {
    saved.splice(index, 1);
    showLandingToast(`Removed from saved list`);
  } else {
    saved.push(id);
    showLandingToast(`❤️ "${title}" saved to your shortlist!`);
  }

  localStorage.setItem('pov_saved_rentals', JSON.stringify(saved));
  updateLandingWishlistBadge();

  if (landingShowSavedOnly) {
    runInstantLandingFilter();
  }
}

function toggleLandingSavedOnly(btn) {
  landingShowSavedOnly = !landingShowSavedOnly;
  if (landingShowSavedOnly) {
    btn.style.background = '#E11D48';
    btn.style.color = '#fff';
    btn.querySelector('i').style.color = '#fff';
  } else {
    btn.style.background = '#FFF1F2';
    btn.style.color = '#BE123C';
    btn.querySelector('i').style.color = '#E11D48';
  }
  runInstantLandingFilter();
}

function selectLandingIntent(type, btn) {
  landingCurrentIntent = type;
  document.querySelectorAll('.intent-tab-btn').forEach(b => {
    b.classList.remove('active');
  });
  btn.classList.add('active');
  
  const formIntentInput = document.getElementById('landingSearchIntent');
  if (formIntentInput) formIntentInput.value = type;

  runInstantLandingFilter();
}

function toggleLandingBhk(bhkType, btn) {
  if (landingCurrentBhk === bhkType) {
    landingCurrentBhk = 'all';
    btn.classList.remove('active');
  } else {
    landingCurrentBhk = bhkType;
    document.querySelectorAll('.s-landing-bhk').forEach(b => {
      b.classList.remove('active');
    });
    btn.classList.add('active');
  }
  runInstantLandingFilter();
}

function toggleQuickLandingTag(tagKey, btn) {
  landingTagFilters[tagKey] = !landingTagFilters[tagKey];
  if (landingTagFilters[tagKey]) {
    btn.classList.add('active');
  } else {
    btn.classList.remove('active');
  }
  runInstantLandingFilter();
}

function runInstantLandingFilter() {
  const cards = document.querySelectorAll('.landing-card-item');
  const savedIds = getLandingSavedIds();
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

    if (landingShowSavedOnly && !savedIds.includes(cardId)) {
      match = false;
    }
    if (landingCurrentIntent !== 'all' && cardIntent !== landingCurrentIntent) {
      match = false;
    }
    if (landingCurrentBhk !== 'all' && cardBhk !== landingCurrentBhk) {
      match = false;
    }
    if (landingTagFilters.pure_veg && cardFood !== 'pure_veg' && cardFood !== 'jain_friendly') {
      match = false;
    }
    if (landingTagFilters.no_curfew && cardCurfew !== 'no_curfew') {
      match = false;
    }
    if (landingTagFilters.zero_brokerage && cardBrokerage !== 'zero_brokerage') {
      match = false;
    }
    if (landingTagFilters.under_15k && cardPrice > 15000) {
      match = false;
    }

    if (match) {
      card.style.display = 'flex';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  const countEl = document.getElementById('landingCountLabel');
  if (countEl) countEl.textContent = visibleCount;
}

function runLandingSort(criteria) {
  const container = document.getElementById('landingCardsContainer');
  if (!container) return;

  const cards = Array.from(container.querySelectorAll('.landing-card-item'));

  cards.sort((a, b) => {
    const priceA = parseFloat(a.dataset.price) || 0;
    const priceB = parseFloat(b.dataset.price) || 0;
    const ratingA = parseFloat(a.dataset.rating) || 0;
    const ratingB = parseFloat(b.dataset.rating) || 0;

    if (criteria === 'price_low') return priceA - priceB;
    if (criteria === 'price_high') return priceB - priceA;
    if (criteria === 'rating') return ratingB - ratingA;
    return 0;
  });

  cards.forEach(card => container.appendChild(card));
}

function resetLandingFilters() {
  landingCurrentIntent = 'all';
  landingCurrentBhk = 'all';
  landingShowSavedOnly = false;
  landingTagFilters = { pure_veg: false, no_curfew: false, zero_brokerage: false, under_15k: false };

  document.querySelectorAll('.intent-tab-btn').forEach(b => b.classList.remove('active'));
  const firstTab = document.querySelector('.intent-tab-btn');
  if (firstTab) firstTab.classList.add('active');

  document.querySelectorAll('.s-filter-chip').forEach(b => b.classList.remove('active'));

  const savedBtn = document.getElementById('landingSavedFilterBtn');
  if (savedBtn) {
    savedBtn.style.background = '#FFF1F2';
    savedBtn.style.color = '#BE123C';
    savedBtn.querySelector('i').style.color = '#E11D48';
  }

  const formIntentInput = document.getElementById('formIntent') || document.getElementById('landingSearchIntent');
  if (formIntentInput) formIntentInput.value = 'all';
  runInstantLandingFilter();
}

function quickFillAndSearch(landmark, city) {
  const lmInput = document.getElementById('searchLandmark') || document.getElementById('landingLandmarkInput');
  const citySelect = document.getElementById('searchCity') || document.getElementById('landingCitySelect');
  if (lmInput) lmInput.value = landmark;
  if (citySelect) citySelect.value = city;
  const form = document.getElementById('rentalSearchForm') || document.getElementById('mainRentalSearchForm');
  if (form) form.submit();
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
    const emailBody = encodeURIComponent(`Hi,\n\nI found this verified property on POV Indian:\n\n${data.title}\n${data.price ? 'Price: ' + data.price + '\n' : ''}${data.locality ? 'Location: ' + data.locality + '\n' : ''}\nView property:\n${data.url}\n\nShared via POVIndian.com`);
    gBtn.href = `https://mail.google.com/mail/?view=cm&fs=1&su=${emailSubject}&body=${emailBody}`;
  }

  // X (Twitter)
  const xBtn = document.getElementById('shareXBtn');
  if (xBtn) {
    xBtn.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent('Found this property on POV Indian: ' + (data.title || ''))}&url=${encodeURIComponent(data.url)}`;
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
    showLandingToast('✅ Link copied to clipboard!');
  }).catch(() => {
    const input = document.getElementById('shareDirectUrlInput');
    if (input) {
      input.select();
      document.execCommand('copy');
      showLandingToast('✅ Link copied to clipboard!');
    }
  });
}

function shareToInstagram() {
  const url = currentShareData.url || window.location.href;
  navigator.clipboard.writeText(url).then(() => {
    showLandingToast('📸 Link copied! Opening Instagram...');
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

function shareLandingProperty(title, url, price, locality, img) {
  openSocialShareModal({
    title: title || 'Property on POV Indian',
    url: url || window.location.href,
    price: price || '',
    locality: locality || '',
    img: img || ''
  });
}

function showLandingToast(msg) {
  let toast = document.getElementById('landingToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'landingToast';
    toast.style.cssText = 'position:fixed; bottom:24px; right:24px; background:#142132; color:#fff; padding:12px 20px; border-radius:999px; font-size:13.5px; font-weight:700; box-shadow:0 8px 24px rgba(0,0,0,0.25); z-index:999999; transform:translateY(80px); opacity:0; transition:all 0.3s cubic-bezier(0.16, 1, 0.3, 1);';
    document.body.appendChild(toast);
  }
  toast.innerHTML = msg;
  toast.style.transform = 'translateY(0)';
  toast.style.opacity = '1';
  clearTimeout(window.__landingToastTimer);
  window.__landingToastTimer = setTimeout(() => {
    toast.style.transform = 'translateY(80px)';
    toast.style.opacity = '0';
  }, 3200);
}

// Modal open/close helpers
function openRequirementModal() {
  const m = document.getElementById('requirementModal');
  if (m) m.style.display = 'flex';
}
function closeRequirementModal() {
  const m = document.getElementById('requirementModal');
  if (m) m.style.display = 'none';
}

function openOwnerListingModal() {
  const m = document.getElementById('ownerListingModal');
  if (m) m.style.display = 'flex';
}
function closeOwnerListingModal() {
  const m = document.getElementById('ownerListingModal');
  if (m) m.style.display = 'none';
}

// Close on backdrop click & ESC key
document.getElementById('requirementModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeRequirementModal();
});
document.getElementById('ownerListingModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeOwnerListingModal();
});
document.getElementById('scheduleVisitModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeScheduleVisitModal();
});
document.getElementById('photoLightboxModal')?.addEventListener('click', function(e) {
  if (e.target === this) closePhotoLightbox();
});
document.getElementById('socialShareModal')?.addEventListener('click', function(e) {
  if (e.target === this) closeSocialShareModal();
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeRequirementModal();
    closeOwnerListingModal();
    closeScheduleVisitModal();
    closePhotoLightbox();
    closeSocialShareModal();
  }
  if (document.getElementById('photoLightboxModal')?.style.display === 'flex') {
    if (e.key === 'ArrowLeft') prevLightboxPhoto();
    if (e.key === 'ArrowRight') nextLightboxPhoto();
  }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  updateLandingWishlistBadge();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
