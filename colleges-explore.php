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

          <div class="card-foot-actions">
            <a href="<?= htmlspecialchars($detailUrl) ?>" class="btn-card-profile">
              <i class="fa-solid fa-eye"></i> View Profile & Fees
            </a>
            <a href="<?= htmlspecialchars($inquireUrl) ?>" class="btn-card-inquire">
              <i class="fa-solid fa-headset"></i> Inquire
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
let currentFilter = 'all';

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

