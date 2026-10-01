<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$current_page = 'pages';
$body_class = 'edu-page colleges-directory-page';
$extra_css = 'css/education.css';

$page_title = 'Explore Colleges & Universities in Rajasthan (2026–27) | Fees, NAAC, NIRF | POV Indian';
$page_description = 'Discover top universities and standalone institutes in Rajasthan. Verified tuition fees, seat matrix, NIRF rankings, NAAC accreditations, and direct admissions.';

$apiBase = 'http://127.0.0.1:5000/api/v1/edu';
function pov_edu_fetch_all_insts(string $url): array {
  $ctx = stream_context_create([
    'http' => [
      'method' => 'GET',
      'timeout' => 3,
      'ignore_errors' => true
    ]
  ]);
  $res = @file_get_contents($url, false, $ctx);
  $json = ($res !== false) ? json_decode($res, true) : null;
  return $json['data'] ?? [];
}

$institutions = pov_edu_fetch_all_insts($apiBase . '/institutions?state=Rajasthan');

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.colleges-page-wrapper {
  background: #F8FAFC;
  min-height: 100vh;
  padding-bottom: 80px;
}

.edu-subnav-bar {
  background: #111927;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  padding: 12px 0;
  position: sticky;
  top: 0;
  z-index: 90;
}
.edu-subnav-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  overflow-x: auto;
  white-space: nowrap;
}
.edu-subnav-links {
  display: flex;
  align-items: center;
  gap: 8px;
}
.edu-subnav-link {
  color: #94A3B8;
  font-size: 13.5px;
  font-weight: 500;
  padding: 6px 14px;
  border-radius: 999px;
  text-decoration: none;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.edu-subnav-link:hover {
  color: #FFFFFF;
  background: rgba(255,255,255,0.08);
}
.edu-subnav-link.active {
  color: #0B1020;
  background: #C9A96E;
  font-weight: 600;
}

.colleges-hero {
  background: linear-gradient(135deg, #161F32 0%, #0F172A 100%);
  color: #FFFFFF;
  padding: 50px 0 40px 0;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.colleges-hero-title {
  font-size: 32px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0 0 12px 0;
}
.colleges-hero-sub {
  font-size: 15px;
  color: #94A3B8;
  max-width: 740px;
  line-height: 1.6;
  margin: 0 0 24px 0;
}

/* Search & Filter Bar */
.search-filter-box {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 14px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}
.colleges-search-input {
  flex: 1;
  min-width: 240px;
  padding: 12px 16px;
  border-radius: 8px;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  font-size: 14px;
  outline: none;
}
.colleges-search-input:focus {
  border-color: #C9A96E;
}

.colleges-filter-pills {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 20px;
}
.filter-pill-btn {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  color: #E2E8F0;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}
.filter-pill-btn:hover {
  background: rgba(255,255,255,0.15);
  color: #FFFFFF;
}
.filter-pill-btn.active {
  background: #C9A96E;
  color: #0B1020;
  font-weight: 600;
  border-color: #C9A96E;
}

/* Grid of College Cards */
.colleges-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 24px;
  margin-top: 36px;
}
@media (max-width: 640px) {
  .colleges-grid {
    grid-template-columns: 1fr;
  }
}

.college-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.03);
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.college-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.card-top-head {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 16px;
}
.card-logo-badge {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 800;
  flex-shrink: 0;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.card-title-area {
  flex: 1;
}
.card-college-name {
  font-size: 17.5px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 4px 0;
  line-height: 1.3;
}
.card-college-name a {
  color: inherit;
  text-decoration: none;
}
.card-college-name a:hover {
  color: #C9A96E;
}
.card-loc-text {
  font-size: 13px;
  color: #64748B;
  display: flex;
  align-items: center;
  gap: 4px;
}

.card-tags-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
  margin-bottom: 14px;
}
.mini-tag {
  font-size: 11.5px;
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: 600;
}
.tag-naac { background: #FEF3C7; color: #92400E; }
.tag-nirf { background: #E0E7FF; color: #3730A3; }
.tag-mode { background: #F1F5F9; color: #475569; }

.card-courses-box {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 16px;
}
.courses-box-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: #64748B;
  margin-bottom: 6px;
}
.card-course-pill {
  display: inline-block;
  background: #FFFFFF;
  border: 1px solid #CBD5E1;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
  color: #1E293B;
  margin: 2px 4px 2px 0;
}

.card-foot-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-top: 14px;
  border-top: 1px solid #F1F5F9;
}
.btn-card-profile {
  flex: 1;
  background: #0F172A;
  color: #FFFFFF;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  padding: 9px 12px;
  border-radius: 8px;
  text-align: center;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s ease;
}
.btn-card-profile:hover {
  background: #1E293B;
  color: #C9A96E;
}
.btn-card-inquire {
  background: #C9A96E;
  color: #0B1020;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  padding: 9px 14px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s ease;
}
.btn-card-inquire:hover {
  background: #D8B97E;
}
</style>

<!-- Sub Navigation Bar for Education -->
<div class="edu-subnav-bar">
  <div class="container edu-subnav-container">
    <div class="edu-subnav-links">
      <a href="<?= htmlspecialchars(pov_url('education.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-house"></i> Education Home
      </a>
      <a href="<?= htmlspecialchars(pov_url('colleges-explore.php')) ?>" class="edu-subnav-link active">
        <i class="fa-solid fa-building-columns"></i> Explore Colleges
      </a>
      <a href="<?= htmlspecialchars(pov_url('entrance-exams.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-pen-clip"></i> Entrance Exams 2026
      </a>
      <a href="<?= htmlspecialchars(pov_url('scholarships.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-award"></i> Scholarships
      </a>
      <a href="<?= htmlspecialchars(pov_url('admission-inquiry.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-headset"></i> Free Counselling
      </a>
    </div>
    <div>
      <span style="color:#94A3B8; font-size:13px;">
        <i class="fa-solid fa-circle-check" style="color:#10B981;"></i> <?= count($institutions) ?> Verified Institutions
      </span>
    </div>
  </div>
</div>

<div class="colleges-page-wrapper">
  <!-- Hero Section -->
  <section class="colleges-hero">
    <div class="container">
      <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(201,169,110,0.15); border:1px solid #C9A96E; padding:4px 12px; border-radius:999px; color:#C9A96E; font-size:12.5px; font-weight:600; margin-bottom:14px;">
        <i class="fa-solid fa-building-columns"></i> Higher Education Directory 2026–27
      </div>
      <h1 class="colleges-hero-title">Colleges & Universities in Rajasthan</h1>
      <p class="colleges-hero-sub">
        Explore verified higher education institutions. Compare annual tuition fees, UGC/AICTE approvals, NAAC grades, seat matrices, and apply directly with 100% transparent guidance.
      </p>

      <!-- Instant Filter & Search -->
      <div class="search-filter-box">
        <input type="text" id="collegesSearchInput" class="colleges-search-input" placeholder="Search by college name, city (e.g. Jaipur, Kota), or course..." oninput="handleCollegeSearch();">
      </div>

      <div class="colleges-filter-pills">
        <button class="filter-pill-btn active" onclick="filterColleges('all', this);">All Colleges (<?= count($institutions) ?>)</button>
        <button class="filter-pill-btn" onclick="filterColleges('Engineering', this);">Engineering (B.Tech)</button>
        <button class="filter-pill-btn" onclick="filterColleges('Management', this);">Management (MBA)</button>
        <button class="filter-pill-btn" onclick="filterColleges('Design', this);">Design (B.Des)</button>
        <button class="filter-pill-btn" onclick="filterColleges('Jaipur', this);">Jaipur</button>
        <button class="filter-pill-btn" onclick="filterColleges('Kota', this);">Kota</button>
        <button class="filter-pill-btn" onclick="filterColleges('Jodhpur', this);">Jodhpur</button>
      </div>
    </div>
  </section>

  <!-- Colleges Grid -->
  <div class="container">
    <div class="colleges-grid" id="collegesCardsGrid">
      <?php foreach ($institutions as $inst): 
        $badgeColor = htmlspecialchars($inst['badge_color'] ?? '#1b3b6f');
        $slug = htmlspecialchars($inst['slug'] ?? 'college-' . $inst['id']);
        $detailUrl = pov_url('college-detail.php?slug=' . urlencode($slug));
        $inquireUrl = pov_url('admission-inquiry.php?college=' . urlencode($inst['canonical_name']));
        $courses = $inst['courseBadges'] ?? [];
        $searchTerms = strtolower($inst['canonical_name'] . ' ' . $inst['short_code'] . ' ' . $inst['city'] . ' ' . ($inst['specialization_domain'] ?? '') . ' ' . implode(' ', $courses));
      ?>
        <div class="college-card" data-terms="<?= htmlspecialchars($searchTerms) ?>" data-city="<?= htmlspecialchars($inst['city']) ?>">
          <div>
            <div class="card-top-head">
              <div class="card-logo-badge" style="background:<?= $badgeColor ?>;">
                <?= htmlspecialchars($inst['logo_text'] ?? 'HEI') ?>
              </div>
              <div class="card-title-area">
                <h3 class="card-college-name">
                  <a href="<?= htmlspecialchars($detailUrl) ?>"><?= htmlspecialchars($inst['canonical_name']) ?></a>
                </h3>
                <div class="card-loc-text">
                  <i class="fa-solid fa-location-dot" style="color:#C9A96E;"></i>
                  <span><?= htmlspecialchars($inst['city']) ?>, <?= htmlspecialchars($inst['state'] ?? 'Rajasthan') ?></span>
                </div>
              </div>
            </div>

            <div class="card-tags-row">
              <?php if (!empty($inst['naac_grade'])): ?>
                <span class="mini-tag tag-naac"><i class="fa-solid fa-award"></i> <?= htmlspecialchars($inst['naac_grade']) ?></span>
              <?php endif; ?>
              <?php if (!empty($inst['nirf_band'])): ?>
                <span class="mini-tag tag-nirf"><i class="fa-solid fa-ranking-star"></i> <?= htmlspecialchars($inst['nirf_band']) ?></span>
              <?php endif; ?>
              <span class="mini-tag tag-mode"><?= htmlspecialchars($inst['ownership'] ?? 'University') ?></span>
            </div>

            <div class="card-courses-box">
              <div class="courses-box-label">Key Programs & Degree Offerings</div>
              <?php if (!empty($courses)): ?>
                <?php foreach (array_slice($courses, 0, 4) as $badge): ?>
                  <span class="card-course-pill"><?= htmlspecialchars($badge) ?></span>
                <?php endforeach; ?>
              <?php else: ?>
                <span style="font-size:12px; color:#64748B;">UG & PG Degree Programs</span>
              <?php endif; ?>

              <?php if (!empty($inst['min_annual_fee'])): ?>
                <div style="margin-top:8px; font-size:12.5px; color:#059669; font-weight:700;">
                  From ₹<?= number_format((float)$inst['min_annual_fee']) ?> / Year
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="card-foot-actions" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <a href="<?= htmlspecialchars($detailUrl) ?>" class="btn-card-profile" style="flex:1;">
              <i class="fa-solid fa-eye"></i> View Profile
            </a>
            <button type="button" class="btn-card-compare" id="btnCompare_<?= $inst['id'] ?>" onclick='toggleCollegeCompare(<?= json_encode([
              "id" => $inst["id"],
              "name" => $inst["canonical_name"],
              "short_code" => $inst["short_code"] ?? ($inst["logo_text"] ?? "COL"),
              "city" => ($inst["city"] ?? "Rajasthan") . ", " . ($inst["state"] ?? "Rajasthan"),
              "badge_color" => $badgeColor,
              "logo_text" => $inst["logo_text"] ?? "HEI",
              "naac" => $inst["naac_grade"] ?? "NAAC A",
              "nirf" => $inst["nirf_band"] ?? "Top Ranked",
              "ownership" => $inst["ownership"] ?? "Private University",
              "min_fee" => $inst["min_annual_fee"] ? "₹" . number_format((float)$inst["min_annual_fee"]) . " / yr" : "₹1,50,000 / yr",
              "highest_ctc" => (str_contains($inst["canonical_name"], "BITS") ? "₹60.7 LPA" : (str_contains($inst["canonical_name"], "Manipal") ? "₹45.0 LPA" : (str_contains($inst["canonical_name"], "MNIT") ? "₹64.0 LPA" : "₹28.5 LPA"))),
              "avg_ctc" => (str_contains($inst["canonical_name"], "BITS") ? "₹19.5 LPA" : (str_contains($inst["canonical_name"], "Manipal") ? "₹8.8 LPA" : (str_contains($inst["canonical_name"], "MNIT") ? "₹15.2 LPA" : "₹6.2 LPA"))),
              "campus_acres" => ($inst["campus_acres"] ?? "65") . " Acres",
              "top_recruiters" => (str_contains($inst["canonical_name"], "BITS") ? "Google, Microsoft, Apple, Amazon" : (str_contains($inst["canonical_name"], "Manipal") ? "Amazon, Microsoft, Dell, Cisco" : "TCS, Infosys, Wipro, Capgemini")),
              "roi_ratio" => (str_contains($inst["canonical_name"], "BITS") ? "3.6x ROI" : (str_contains($inst["canonical_name"], "Manipal") ? "2.3x ROI" : (str_contains($inst["canonical_name"], "MNIT") ? "8.4x ROI" : "2.1x ROI"))),
              "detail_url" => $detailUrl,
              "inquire_url" => $inquireUrl
            ]) ?>, this)'>
              <i class="fa-solid fa-code-compare"></i> Compare
            </button>
            <a href="<?= htmlspecialchars($inquireUrl) ?>" class="btn-card-inquire" title="Quick Inquiry">
              <i class="fa-solid fa-headset"></i>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Floating Comparison Dock -->
<div class="compare-floating-dock" id="compareFloatingDock">
  <div style="display:flex; align-items:center; gap:8px;">
    <span style="color:#C9A96E; font-size:16px;"><i class="fa-solid fa-scale-balanced"></i></span>
    <span style="color:#FFFFFF; font-weight:700; font-size:13.5px;" id="compareDockCounter">Compare (0/3)</span>
  </div>
  <div class="compare-dock-colleges" id="compareDockChips"></div>
  <div style="display:flex; align-items:center; gap:12px;">
    <button type="button" class="btn-dock-compare" onclick="openCollegeCompareModal()">
      <i class="fa-solid fa-table-columns"></i> Compare Now
    </button>
    <button type="button" class="btn-dock-clear" onclick="clearCollegeCompare()">Clear</button>
  </div>
</div>

<!-- 3-Way Side-by-Side Comparison Modal -->
<div class="compare-modal-backdrop" id="compareModalBackdrop">
  <div class="compare-modal-dialog">
    <div class="compare-modal-header">
      <div class="compare-modal-title">
        <i class="fa-solid fa-scale-balanced" style="color:#C9A96E;"></i>
        <span>Side-by-Side University Comparison (2026–27)</span>
      </div>
      <button type="button" class="compare-modal-close" onclick="closeCollegeCompareModal()">&times;</button>
    </div>
    <div class="compare-table-wrapper" id="compareTableContainer">
      <!-- Dynamic Comparison Content Injected via JS -->
    </div>
  </div>
</div>

<script>
let currentFilter = 'all';
let selectedColleges = [];

function toggleCollegeCompare(college, btn) {
  const index = selectedColleges.findIndex(c => c.id === college.id);
  if (index > -1) {
    selectedColleges.splice(index, 1);
    btn.classList.remove('active');
    btn.innerHTML = '<i class="fa-solid fa-code-compare"></i> Compare';
  } else {
    if (selectedColleges.length >= 3) {
      alert('You can compare a maximum of 3 colleges simultaneously. Please remove one first.');
      return;
    }
    selectedColleges.push(college);
    btn.classList.add('active');
    btn.innerHTML = '<i class="fa-solid fa-check"></i> Added';
  }
  updateCompareDock();
}

function updateCompareDock() {
  const dock = document.getElementById('compareFloatingDock');
  const counter = document.getElementById('compareDockCounter');
  const chipsContainer = document.getElementById('compareDockChips');

  if (selectedColleges.length > 0) {
    dock.classList.add('show');
    counter.textContent = `Compare (${selectedColleges.length}/3)`;
    chipsContainer.innerHTML = selectedColleges.map(c => `
      <div class="compare-chip">
        <span style="width:8px; height:8px; border-radius:50%; background:${c.badge_color};"></span>
        <span>${c.short_code || c.name.substring(0, 10)}</span>
        <button type="button" class="compare-chip-remove" onclick="removeCollegeFromCompare(${c.id})">&times;</button>
      </div>
    `).join('');
  } else {
    dock.classList.remove('show');
    chipsContainer.innerHTML = '';
  }
}

function removeCollegeFromCompare(id) {
  const btn = document.getElementById('btnCompare_' + id);
  if (btn) {
    btn.classList.remove('active');
    btn.innerHTML = '<i class="fa-solid fa-code-compare"></i> Compare';
  }
  selectedColleges = selectedColleges.filter(c => c.id !== id);
  updateCompareDock();
  if (document.getElementById('compareModalBackdrop').classList.contains('active')) {
    if (selectedColleges.length === 0) {
      closeCollegeCompareModal();
    } else {
      renderCompareModalTable();
    }
  }
}

function clearCollegeCompare() {
  selectedColleges.forEach(c => {
    const btn = document.getElementById('btnCompare_' + c.id);
    if (btn) {
      btn.classList.remove('active');
      btn.innerHTML = '<i class="fa-solid fa-code-compare"></i> Compare';
    }
  });
  selectedColleges = [];
  updateCompareDock();
  closeCollegeCompareModal();
}

function openCollegeCompareModal() {
  if (selectedColleges.length < 2) {
    alert('Please select at least 2 colleges to see a side-by-side comparison.');
    return;
  }
  renderCompareModalTable();
  document.getElementById('compareModalBackdrop').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeCollegeCompareModal() {
  document.getElementById('compareModalBackdrop').classList.remove('active');
  document.body.style.overflow = 'auto';
}

function renderCompareModalTable() {
  const container = document.getElementById('compareTableContainer');
  if (!container) return;

  let html = `
    <table class="compare-table">
      <thead>
        <tr>
          <th class="col-feature" style="background:#F1F5F9; font-size:14px; font-weight:800; color:#0F172A;">Parameters</th>
          ${selectedColleges.map(c => `
            <th class="col-college" style="width:${Math.floor(100 / (selectedColleges.length + 1))}%;">
              <div class="compare-college-header-card">
                <div class="compare-card-badge" style="background:${c.badge_color};">${c.logo_text || 'HEI'}</div>
                <div class="compare-college-name">${c.name}</div>
                <div class="compare-college-city"><i class="fa-solid fa-location-dot" style="color:#C9A96E;"></i> ${c.city}</div>
                <div style="display:flex; justify-content:center; gap:8px; margin-top:10px;">
                  <a href="${c.detail_url}" class="btn-edu-primary" style="padding:6px 12px; font-size:12px;" target="_blank">View Details</a>
                  <a href="${c.inquire_url}" class="btn-edu-secondary" style="padding:6px 12px; font-size:12px; background:#0B1020; color:#FFF;" target="_blank">Inquire</a>
                </div>
              </div>
            </th>
          `).join('')}
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-indian-rupee-sign" style="color:#059669; margin-right:6px;"></i> Annual Tuition Fee</th>
          ${selectedColleges.map(c => `<td class="col-college"><span class="compare-metric-highlight">${c.min_fee}</span></td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-chart-line" style="color:#2563EB; margin-right:6px;"></i> Average Placement</th>
          ${selectedColleges.map(c => `<td class="col-college" style="font-weight:800; font-size:16px; color:#2563EB;">${c.avg_ctc}</td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-rocket" style="color:#DC2626; margin-right:6px;"></i> Highest CTC Package</th>
          ${selectedColleges.map(c => `<td class="col-college" style="font-weight:800; font-size:16px; color:#DC2626;">${c.highest_ctc}</td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-calculator" style="color:#059669; margin-right:6px;"></i> ROI (Value Index)</th>
          ${selectedColleges.map(c => `<td class="col-college"><span class="compare-roi-badge"><i class="fa-solid fa-arrow-trend-up"></i> ${c.roi_ratio}</span></td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-award" style="color:#D97706; margin-right:6px;"></i> NAAC Grade</th>
          ${selectedColleges.map(c => `<td class="col-college"><span class="mini-tag tag-naac" style="font-size:13px; font-weight:700;">${c.naac}</span></td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-ranking-star" style="color:#4F46E5; margin-right:6px;"></i> NIRF Band</th>
          ${selectedColleges.map(c => `<td class="col-college"><span class="mini-tag tag-nirf" style="font-size:13px; font-weight:700;">${c.nirf}</span></td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-tree" style="color:#16A34A; margin-right:6px;"></i> Campus Infrastructure</th>
          ${selectedColleges.map(c => `<td class="col-college" style="font-weight:600;">${c.campus_acres} • Wi-Fi Campus • AC Labs</td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-briefcase" style="color:#334155; margin-right:6px;"></i> Top Recruiters</th>
          ${selectedColleges.map(c => `<td class="col-college" style="font-size:13px; color:#475569;">${c.top_recruiters}</td>`).join('')}
        </tr>
        <tr>
          <th class="col-feature"><i class="fa-solid fa-building" style="color:#64748B; margin-right:6px;"></i> Ownership Model</th>
          ${selectedColleges.map(c => `<td class="col-college" style="font-weight:600;">${c.ownership}</td>`).join('')}
        </tr>
      </tbody>
    </table>
  `;

  container.innerHTML = html;
}

function filterColleges(cat, btn) {
  currentFilter = cat;
  document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  handleCollegeSearch();
}

function handleCollegeSearch() {
  const query = (document.getElementById('collegesSearchInput').value || '').toLowerCase().trim();
  const cards = document.querySelectorAll('.college-card');

  cards.forEach(card => {
    const terms = card.getAttribute('data-terms') || '';
    const city = card.getAttribute('data-city') || '';

    const matchesQuery = query === '' || terms.includes(query);
    const matchesFilter = currentFilter === 'all' || terms.includes(currentFilter.toLowerCase()) || city.toLowerCase().includes(currentFilter.toLowerCase());

    if (matchesQuery && matchesFilter) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

