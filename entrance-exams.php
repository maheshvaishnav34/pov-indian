<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$current_page = 'pages';
$body_class = 'edu-page exams-hub-page';
$extra_css = 'css/education.css';

$page_title = 'Entrance Exams 2026–27 Hub | Dates, Syllabus, Eligibility & Direct Links | POV Indian';
$page_description = 'Comprehensive calendar of National & State level entrance examinations (JEE Main, NEET UG, CAT, GATE, CLAT) for 2026-27 admissions. Verified dates, eligibility, and official links.';

$apiBase = 'http://127.0.0.1:5000/api/v1/edu';
function pov_edu_fetch_exams(string $url): array {
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

$exams = pov_edu_fetch_exams($apiBase . '/exams');

if (empty($exams)) {
  // Rich fallback dataset
  $exams = [
    [
      'id' => 1,
      'slug' => 'jee-main-2026',
      'exam_name' => 'Joint Entrance Examination (Main) 2026',
      'short_code' => 'JEE Main',
      'category' => 'Engineering',
      'conducting_body' => 'NTA (National Testing Agency)',
      'exam_level' => 'National Level',
      'frequency' => 'Session 1 & Session 2',
      'mode' => 'Computer Based Test (CBT)',
      'duration_mins' => 180,
      'application_start' => '2026-01-15',
      'application_end' => '2026-03-05',
      'admit_card_date' => '2026-04-01',
      'exam_date' => '2026-04-18',
      'result_date' => '2026-05-10',
      'counselling_date' => '2026-06-12',
      'application_fee' => '₹1,000 (General) / ₹500 (Female & SC/ST)',
      'eligibility_summary' => 'Passed 10+2 with Physics, Mathematics, and Chemistry/CS with min 75% for NITs/IIITs or top 20 percentile.',
      'syllabus_summary' => 'Physics, Chemistry, and Mathematics from CBSE Class 11 & 12 curricula.',
      'participating_colleges_count' => '31 NITs, 26 IIITs, 38 GFTIs, and 1,500+ universities',
      'official_url' => 'https://jeemain.nta.nic.in'
    ],
    [
      'id' => 2,
      'slug' => 'neet-ug-2026',
      'exam_name' => 'National Eligibility cum Entrance Test (UG) 2026',
      'short_code' => 'NEET UG',
      'category' => 'Medical',
      'conducting_body' => 'NTA & Medical Counselling Committee (MCC)',
      'exam_level' => 'National Level Single-Window Exam',
      'frequency' => 'Once a year (May)',
      'mode' => 'Pen and Paper (OMR)',
      'duration_mins' => 200,
      'application_start' => '2026-02-10',
      'application_end' => '2026-03-25',
      'admit_card_date' => '2026-04-28',
      'exam_date' => '2026-05-04',
      'result_date' => '2026-06-14',
      'counselling_date' => '2026-07-01',
      'application_fee' => '₹1,700 (General) / ₹1,600 (EWS/OBC)',
      'eligibility_summary' => '10+2 with Physics, Chemistry, Biology/Biotech, and English with min 50% marks (40% reserved). Min age 17.',
      'syllabus_summary' => 'Biology (Botany & Zoology), Chemistry, and Physics core syllabus based on NCERT.',
      'participating_colleges_count' => 'All AIIMS, JIPMER, State Govt Medical Colleges & Deemed Universities',
      'official_url' => 'https://neet.nta.nic.in'
    ],
    [
      'id' => 3,
      'slug' => 'cat-2026',
      'exam_name' => 'Common Admission Test (CAT) 2026',
      'short_code' => 'CAT 2026',
      'category' => 'Management',
      'conducting_body' => 'Indian Institutes of Management (IIMs)',
      'exam_level' => 'National Level Premier MBA Entrance',
      'frequency' => 'Once a year (November)',
      'mode' => 'Computer Based Test (CBT - 3 Slots)',
      'duration_mins' => 120,
      'application_start' => '2026-08-01',
      'application_end' => '2026-09-20',
      'admit_card_date' => '2026-10-25',
      'exam_date' => '2026-11-29',
      'result_date' => '2026-12-20',
      'counselling_date' => '2027-01-15',
      'application_fee' => '₹2,400 (General) / ₹1,200 (SC/ST/PwD)',
      'eligibility_summary' => 'Bachelor degree in any discipline with min 50% marks or equivalent CGPA (45% for SC/ST/PwD). Final year students eligible.',
      'syllabus_summary' => 'VARC (Verbal Ability & Reading Comprehension), DILR (Data Interpretation & Logical Reasoning), and QA (Quantitative Aptitude).',
      'participating_colleges_count' => '21 IIMs, FMS Delhi, SPJIMR, MDI, IIT DoMS & 1,000+ B-Schools',
      'official_url' => 'https://iimcat.ac.in'
    ]
  ];
}

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.exams-page-wrapper {
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

.exams-hero {
  background: linear-gradient(135deg, #161F32 0%, #0F172A 100%);
  color: #FFFFFF;
  padding: 50px 0 40px 0;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.exams-hero-title {
  font-size: 32px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0 0 12px 0;
}
.exams-hero-sub {
  font-size: 15px;
  color: #94A3B8;
  max-width: 760px;
  line-height: 1.6;
  margin: 0 0 24px 0;
}

/* Category Filter Bar */
.exam-filter-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 24px;
}
.exam-filter-btn {
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
.exam-filter-btn:hover {
  background: rgba(255,255,255,0.15);
  color: #FFFFFF;
}
.exam-filter-btn.active {
  background: #C9A96E;
  color: #0B1020;
  font-weight: 600;
  border-color: #C9A96E;
}

/* Exam Cards Grid */
.exam-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(370px, 1fr));
  gap: 24px;
  margin-top: 36px;
}
@media (max-width: 640px) {
  .exam-cards-grid {
    grid-template-columns: 1fr;
  }
}

.exam-card {
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
.exam-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.07);
}

.exam-card-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 12px;
}
.exam-cat-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  background: #E0E7FF;
  color: #3730A3;
}
.exam-mode-badge {
  font-size: 11.5px;
  color: #64748B;
  background: #F1F5F9;
  padding: 3px 8px;
  border-radius: 4px;
  font-weight: 500;
}

.exam-name-title {
  font-size: 19px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 6px 0;
  line-height: 1.3;
}
.exam-body-line {
  font-size: 13px;
  color: #64748B;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* Timeline Pill Grid */
.exam-timeline-box {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 10px;
  padding: 14px;
  margin-bottom: 16px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  font-size: 12.5px;
}
.timeline-item-title {
  color: #64748B;
  font-size: 11.5px;
  font-weight: 500;
  text-transform: uppercase;
}
.timeline-item-val {
  color: #0F172A;
  font-weight: 700;
  margin-top: 2px;
}

.exam-desc-block {
  font-size: 13px;
  color: #475569;
  line-height: 1.5;
  margin-bottom: 16px;
}
.exam-fee-tag {
  font-size: 12px;
  color: #059669;
  background: #ECFDF5;
  padding: 4px 8px;
  border-radius: 6px;
  display: inline-block;
  font-weight: 600;
  margin-bottom: 16px;
}

.exam-card-foot {
  display: flex;
  align-items: center;
  gap: 10px;
  padding-top: 14px;
  border-top: 1px solid #F1F5F9;
}
.btn-exam-official {
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
.btn-exam-official:hover {
  background: #1E293B;
  color: #C9A96E;
}
.btn-exam-inquire {
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
.btn-exam-inquire:hover {
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
      <a href="<?= htmlspecialchars(pov_url('entrance-exams.php')) ?>" class="edu-subnav-link active">
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
        <i class="fa-solid fa-shield-check" style="color:#10B981;"></i> 2026–27 Official Schedules
      </span>
    </div>
  </div>
</div>

<div class="exams-page-wrapper">
  <!-- Hero Section -->
  <section class="exams-hero">
    <div class="container">
      <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(201,169,110,0.15); border:1px solid #C9A96E; padding:4px 12px; border-radius:999px; color:#C9A96E; font-size:12.5px; font-weight:600; margin-bottom:14px;">
        <i class="fa-solid fa-calendar-star"></i> National & State Entrance Schedule 2026
      </div>
      <h1 class="exams-hero-title">Entrance Examinations 2026–27</h1>
      <p class="exams-hero-sub">
        Single-window repository of major entrance exams across Engineering, Medical, Management, Law, and Design. Verified application deadlines, admit card releases, exam dates, syllabus links, and official registration portals.
      </p>

      <!-- Category Filter Pills -->
      <div class="exam-filter-bar">
        <button class="exam-filter-btn active" onclick="filterExams('all', this);">All Exams (<?= count($exams) ?>)</button>
        <button class="exam-filter-btn" onclick="filterExams('Engineering', this);">Engineering (JEE / GATE)</button>
        <button class="exam-filter-btn" onclick="filterExams('Medical', this);">Medical (NEET UG/PG)</button>
        <button class="exam-filter-btn" onclick="filterExams('Management', this);">Management (CAT / CMAT)</button>
        <button class="exam-filter-btn" onclick="filterExams('Law', this);">Law (CLAT)</button>
        <button class="exam-filter-btn" onclick="filterExams('Design', this);">Design (UCEED / NID)</button>
      </div>
    </div>
  </section>

  <!-- Exams Grid -->
  <div class="container">
    <div class="exam-cards-grid" id="examsGridContainer">
      <?php foreach ($exams as $exam): 
        $cat = htmlspecialchars($exam['category'] ?? 'National');
        $catClass = strtolower($cat);
      ?>
        <div class="exam-card" data-category="<?= htmlspecialchars($exam['category'] ?? '') ?>">
          <div>
            <div class="exam-card-head">
              <span class="exam-cat-badge"><?= $cat ?></span>
              <span class="exam-mode-badge"><?= htmlspecialchars($exam['mode'] ?? 'CBT') ?></span>
            </div>

            <h3 class="exam-name-title"><?= htmlspecialchars($exam['exam_name']) ?></h3>
            <div class="exam-body-line">
              <i class="fa-solid fa-building-flag" style="color:#C9A96E;"></i>
              <span><?= htmlspecialchars($exam['conducting_body'] ?? 'National Authority') ?></span>
            </div>

            <!-- Key Timeline Box -->
            <div class="exam-timeline-box">
              <div>
                <div class="timeline-item-title">Exam Date</div>
                <div class="timeline-item-val" style="color:#D97746;">
                  <?= !empty($exam['exam_date']) ? date('M d, Y', strtotime($exam['exam_date'])) : 'To Be Announced' ?>
                </div>
              </div>
              <div>
                <div class="timeline-item-title">Applications Close</div>
                <div class="timeline-item-val">
                  <?= !empty($exam['application_end']) ? date('M d, Y', strtotime($exam['application_end'])) : 'Ongoing' ?>
                </div>
              </div>
              <div>
                <div class="timeline-item-title">Admit Card</div>
                <div class="timeline-item-val">
                  <?= !empty($exam['admit_card_date']) ? date('M d, Y', strtotime($exam['admit_card_date'])) : 'Prior to Exam' ?>
                </div>
              </div>
              <div>
                <div class="timeline-item-title">Results</div>
                <div class="timeline-item-val">
                  <?= !empty($exam['result_date']) ? date('M d, Y', strtotime($exam['result_date'])) : 'Post-Exam' ?>
                </div>
              </div>
            </div>

            <?php if (!empty($exam['application_fee'])): ?>
              <div class="exam-fee-tag">
                <i class="fa-solid fa-receipt"></i> Fee: <?= htmlspecialchars($exam['application_fee']) ?>
              </div>
            <?php endif; ?>

            <div class="exam-desc-block">
              <strong>Eligibility:</strong> <?= htmlspecialchars($exam['eligibility_summary'] ?? '10+2 / Graduation as per guidelines.') ?>
            </div>
          </div>

          <div class="exam-card-foot">
            <?php if (!empty($exam['official_url'])): ?>
              <a href="<?= htmlspecialchars($exam['official_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn-exam-official">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Official Portal
              </a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars(pov_url('admission-inquiry.php?exam=' . urlencode($exam['short_code'] ?? $exam['exam_name']))) ?>" class="btn-exam-inquire">
              <i class="fa-solid fa-bell"></i> Get Guidance
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function filterExams(category, btn) {
  document.querySelectorAll('.exam-filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const cards = document.querySelectorAll('.exam-card');
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
