<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$current_page = 'pages';
$body_class = 'edu-page scholarships-hub-page';
$extra_css = 'css/education.css';

$page_title = 'Higher Education Scholarships 2026–27 | Govt & Private Financial Aid | POV Indian';
$page_description = 'Find top verified scholarships for undergraduate and postgraduate students in India. National Scholarship Portal (NSP), Reliance Foundation, AICTE Pragati, state merit-cum-means assistance.';

$apiBase = 'http://127.0.0.1:5000/api/v1/edu';
function pov_edu_fetch_scholarships(string $url): array {
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

$scholarships = pov_edu_fetch_scholarships($apiBase . '/scholarships');

if (empty($scholarships)) {
  // Rich fallback dataset
  $scholarships = [
    [
      'id' => 1,
      'title' => 'National Scholarship Portal (NSP) - Central Sector Scheme',
      'slug' => 'nsp-central-sector',
      'provider' => 'Department of Higher Education, Ministry of Education, Govt of India',
      'amount' => '₹12,000 – ₹20,000 / Year',
      'eligibility' => 'Students above 80th percentile in Class 12th board exams with family income under ₹4.5 Lakh per annum pursuing regular degree courses.',
      'target_group' => 'General / Merit-cum-Means',
      'category' => 'Means',
      'deadline' => '2026-10-31',
      'application_mode' => 'Online via NSP Portal',
      'portal_url' => 'https://scholarships.gov.in'
    ],
    [
      'id' => 2,
      'title' => 'Reliance Foundation Undergraduate Scholarships 2026',
      'slug' => 'reliance-foundation-ug',
      'provider' => 'Reliance Foundation',
      'amount' => 'Up to ₹2,00,000 over Degree Duration',
      'eligibility' => 'First-year full-time undergraduate students with min 60% in Class 12 and annual household income up to ₹15 Lakhs (preference under ₹2.5L).',
      'target_group' => 'All UG Streams (Engineering, Medicine, Arts, Commerce)',
      'category' => 'Merit',
      'deadline' => '2026-10-15',
      'application_mode' => 'Online Aptitude Test & Direct Application',
      'portal_url' => 'https://www.scholarships.reliancefoundation.org'
    ],
    [
      'id' => 3,
      'title' => 'AICTE Pragati Scholarship Scheme for Girl Students',
      'slug' => 'aicte-pragati',
      'provider' => 'All India Council for Technical Education (AICTE)',
      'amount' => '₹50,000 / Year + Contingency Allowance',
      'eligibility' => 'Girl students admitted to 1st year AICTE-approved Degree / Diploma engineering institutions. Family income up to ₹8 Lakh per annum.',
      'target_group' => 'Female Students in Technical & Engineering Degrees',
      'category' => 'Girls in STEM',
      'deadline' => '2026-11-15',
      'application_mode' => 'Online via National Scholarship Portal',
      'portal_url' => 'https://scholarships.gov.in'
    ],
    [
      'id' => 4,
      'title' => 'Rajasthan Chief Minister Higher Education Scholarship',
      'slug' => 'rajasthan-cm-higher-education',
      'provider' => 'Department of College Education, Govt of Rajasthan',
      'amount' => '₹5,000 / Year (₹500/month for 10 months)',
      'eligibility' => 'Rajasthan domicile students with min 60% in Class 12 RBSE board, merit list within first 1,00,000 students, family income up to ₹2.5 Lakh.',
      'target_group' => 'State Domicile Undergraduate Students',
      'category' => 'Means',
      'deadline' => '2026-11-30',
      'application_mode' => 'Online via Rajasthan SSO Portal',
      'portal_url' => 'https://sso.rajasthan.gov.in'
    ]
  ];
}

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.scholarships-page-wrapper {
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

.scholar-hero {
  background: linear-gradient(135deg, #161F32 0%, #0F172A 100%);
  color: #FFFFFF;
  padding: 50px 0 40px 0;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.scholar-hero-title {
  font-size: 32px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0 0 12px 0;
}
.scholar-hero-sub {
  font-size: 15px;
  color: #94A3B8;
  max-width: 760px;
  line-height: 1.6;
  margin: 0 0 24px 0;
}

/* Category Filter Bar */
.scholar-filter-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 24px;
}
.scholar-filter-btn {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.15);
  color: #E2E8F0;
  padding: 7px 16px;
  border-radius: 999px;
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}
.scholar-filter-btn:hover {
  background: rgba(255,255,255,0.15);
  color: #FFFFFF;
}
.scholar-filter-btn.active {
  background: #C9A96E;
  color: #0B1020;
  font-weight: 600;
  border-color: #C9A96E;
}

/* Scholarships Grid */
.scholar-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 24px;
  margin-top: 36px;
}
@media (max-width: 640px) {
  .scholar-cards-grid {
    grid-template-columns: 1fr;
  }
}

.scholar-card {
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
.scholar-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.07);
}

.scholar-card-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}
.scholar-amount-pill {
  background: #ECFDF5;
  color: #065F46;
  font-weight: 700;
  font-size: 14px;
  padding: 4px 12px;
  border-radius: 999px;
  border: 1px solid #A7F3D0;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.scholar-cat-tag {
  font-size: 11.5px;
  color: #64748B;
  background: #F1F5F9;
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: 600;
}

.scholar-name-title {
  font-size: 18.5px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 6px 0;
  line-height: 1.35;
}
.scholar-provider-line {
  font-size: 13px;
  color: #64748B;
  margin-bottom: 14px;
  display: flex;
  align-items: flex-start;
  gap: 6px;
}
.scholar-provider-line i {
  color: #C9A96E;
  margin-top: 3px;
}

.scholar-eligibility-box {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  padding: 12px;
  font-size: 13px;
  color: #334155;
  line-height: 1.5;
  margin-bottom: 14px;
}

.scholar-deadline-tag {
  font-size: 12.5px;
  color: #92400E;
  background: #FEF3C7;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 16px;
}

.scholar-card-foot {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-top: 14px;
  border-top: 1px solid #F1F5F9;
}
.btn-scholar-apply {
  flex: 1;
  background: #0F172A;
  color: #FFFFFF;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  padding: 9px 14px;
  border-radius: 8px;
  text-align: center;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  transition: all 0.2s ease;
}
.btn-scholar-apply:hover {
  background: #1E293B;
  color: #C9A96E;
}
.btn-scholar-inquire {
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
.btn-scholar-inquire:hover {
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
      <a href="<?= htmlspecialchars(pov_url('colleges-explore.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-building-columns"></i> Explore Colleges
      </a>
      <a href="<?= htmlspecialchars(pov_url('entrance-exams.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-pen-clip"></i> Entrance Exams 2026
      </a>
      <a href="<?= htmlspecialchars(pov_url('scholarships.php')) ?>" class="edu-subnav-link active">
        <i class="fa-solid fa-award"></i> Scholarships
      </a>
      <a href="<?= htmlspecialchars(pov_url('admission-inquiry.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-headset"></i> Free Counselling
      </a>
    </div>
    <div>
      <span style="color:#94A3B8; font-size:13px;">
        <i class="fa-solid fa-hand-holding-dollar" style="color:#C9A96E;"></i> 100% Free Financial Aid Guidance
      </span>
    </div>
  </div>
</div>

<div class="scholarships-page-wrapper">
  <!-- Hero Section -->
  <section class="scholar-hero">
    <div class="container">
      <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(201,169,110,0.15); border:1px solid #C9A96E; padding:4px 12px; border-radius:999px; color:#C9A96E; font-size:12.5px; font-weight:600; margin-bottom:14px;">
        <i class="fa-solid fa-award"></i> Financial Aid & Merit Grants 2026–27
      </div>
      <h1 class="scholar-hero-title">Scholarships & Financial Aid</h1>
      <p class="scholar-hero-sub">
        Explore verified government and philanthropic scholarships for higher education. Filter by merit, household income, girls in technical education, and state domicile to finance your university education with zero financial stress.
      </p>

      <!-- Category Filter Pills -->
      <div class="scholar-filter-bar">
        <button class="scholar-filter-btn active" onclick="filterScholarships('all', this);">All Schemes (<?= count($scholarships) ?>)</button>
        <button class="scholar-filter-btn" onclick="filterScholarships('Merit', this);">Merit Based</button>
        <button class="scholar-filter-btn" onclick="filterScholarships('Means', this);">Means / Income Based</button>
        <button class="scholar-filter-btn" onclick="filterScholarships('Girls in STEM', this);">Girls in STEM</button>
        <button class="scholar-filter-btn" onclick="filterScholarships('State', this);">State Domicile</button>
      </div>
    </div>
  </section>

  <!-- Scholarships Grid -->
  <div class="container">
    <div class="scholar-cards-grid" id="scholarGridContainer">
      <?php foreach ($scholarships as $sch): 
        $target = htmlspecialchars($sch['target_group'] ?? 'Undergraduate');
        $cat = htmlspecialchars($sch['category'] ?? 'Merit');
      ?>
        <div class="scholar-card" data-category="<?= $cat ?> <?= $target ?>">
          <div>
            <div class="scholar-card-top">
              <span class="scholar-amount-pill">
                <i class="fa-solid fa-indian-rupee-sign"></i> <?= htmlspecialchars($sch['amount']) ?>
              </span>
              <span class="scholar-cat-tag"><?= $cat ?></span>
            </div>

            <h3 class="scholar-name-title"><?= htmlspecialchars($sch['title']) ?></h3>
            <div class="scholar-provider-line">
              <i class="fa-solid fa-building-columns"></i>
              <span><?= htmlspecialchars($sch['provider'] ?? 'Higher Education Body') ?></span>
            </div>

            <div class="scholar-eligibility-box">
              <strong>Eligibility:</strong> <?= htmlspecialchars($sch['eligibility'] ?? 'Enrolled full-time in recognized university.') ?>
            </div>

            <div class="scholar-deadline-tag">
              <i class="fa-solid fa-clock"></i> Deadline: <?= !empty($sch['deadline']) ? date('M d, Y', strtotime($sch['deadline'])) : 'Application Open' ?>
            </div>
          </div>

          <div class="scholar-card-foot">
            <?php if (!empty($sch['portal_url'])): ?>
              <a href="<?= htmlspecialchars($sch['portal_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn-scholar-apply">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Apply on Portal
              </a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars(pov_url('admission-inquiry.php?scholarship=' . urlencode($sch['title']))) ?>" class="btn-scholar-inquire">
              <i class="fa-solid fa-hand-sparkles"></i> Guidance
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function filterScholarships(category, btn) {
  document.querySelectorAll('.scholar-filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const cards = document.querySelectorAll('.scholar-card');
  cards.forEach(card => {
    const cardCat = card.getAttribute('data-category') || '';
    if (category === 'all' || cardCat.toLowerCase().includes(category.toLowerCase())) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
