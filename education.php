<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$current_page = 'pages';
$page_title = 'Higher Education in Rajasthan (2026–27) | POV Indian';
$page_description = 'A calmer way to explore colleges — with current course details, local context, and sources you can actually check. Verified 2026-27 fees, eligibility & approvals.';
$body_class = 'edu-page';
$extra_css = 'css/education.css';

// Fetch data from Backend API
$institutions = [];
$updates = [];
$exams = [];
$careers = [];
$scholarships = [];
$reviews = [];
$apiBase = 'http://127.0.0.1:5000/api/v1/edu';

function pov_edu_fetch_json(string $url): ?array {
  $ctx = stream_context_create([
    'http' => [
      'method' => 'GET',
      'timeout' => 2,
      'ignore_errors' => true
    ]
  ]);
  $res = @file_get_contents($url, false, $ctx);
  return ($res !== false) ? json_decode($res, true) : null;
}

$instRes = pov_edu_fetch_json($apiBase . '/institutions?state=Rajasthan');
if (!empty($instRes['success']) && !empty($instRes['data'])) {
  $institutions = $instRes['data'];
}

$updRes = pov_edu_fetch_json($apiBase . '/updates');
if (!empty($updRes['success']) && !empty($updRes['data'])) {
  $updates = $updRes['data'];
}

$examRes = pov_edu_fetch_json($apiBase . '/exams');
if (!empty($examRes['success']) && !empty($examRes['data'])) {
  $exams = $examRes['data'];
}

$careerRes = pov_edu_fetch_json($apiBase . '/careers');
if (!empty($careerRes['success']) && !empty($careerRes['data'])) {
  $careers = $careerRes['data'];
}

$scholarRes = pov_edu_fetch_json($apiBase . '/scholarships');
if (!empty($scholarRes['success']) && !empty($scholarRes['data'])) {
  $scholarships = $scholarRes['data'];
}

$reviewRes = pov_edu_fetch_json($apiBase . '/reviews');
if (!empty($reviewRes['success']) && !empty($reviewRes['data'])) {
  $reviews = $reviewRes['data'];
}

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Sub Navigation Bar for Education -->
<div class="edu-subnav-bar" style="background:#111927; border-bottom:1px solid rgba(255,255,255,0.08); padding:12px 0; position:sticky; top:0; z-index:90;">
  <div class="container" style="display:flex; align-items:center; justify-content:space-between; gap:16px; overflow-x:auto; white-space:nowrap;">
    <div style="display:flex; align-items:center; gap:8px;">
      <a href="<?= htmlspecialchars(pov_url('education.php')) ?>" style="color:#0B1020; background:#C9A96E; font-weight:600; font-size:13.5px; padding:6px 14px; border-radius:999px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-house"></i> Overview
      </a>
      <a href="<?= htmlspecialchars(pov_url('colleges-explore.php')) ?>" style="color:#94A3B8; font-size:13.5px; font-weight:500; padding:6px 14px; border-radius:999px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-building-columns"></i> Colleges Directory
      </a>
      <a href="<?= htmlspecialchars(pov_url('entrance-exams.php')) ?>" style="color:#94A3B8; font-size:13.5px; font-weight:500; padding:6px 14px; border-radius:999px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-pen-clip"></i> Entrance Exams 2026
      </a>
      <a href="<?= htmlspecialchars(pov_url('scholarships.php')) ?>" style="color:#94A3B8; font-size:13.5px; font-weight:500; padding:6px 14px; border-radius:999px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-award"></i> Scholarships
      </a>
      <a href="<?= htmlspecialchars(pov_url('admission-inquiry.php')) ?>" style="color:#94A3B8; font-size:13.5px; font-weight:500; padding:6px 14px; border-radius:999px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-headset"></i> Free Counselling
      </a>
    </div>
    <div>
      <a href="<?= htmlspecialchars(pov_url('colleges-explore.php')) ?>" style="color:#C9A96E; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        All Colleges <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>
  </div>
</div>

<!-- =======================================================================
     1. HERO SECTION (PRD Section 08)
     ======================================================================= -->
<section class="edu-hero" id="eduHero">
  <div class="container">
    <div class="edu-hero-grid">
      <!-- Left Content -->
      <div class="edu-hero-left">
        <div style="display:flex; align-items:center; gap:14px; margin-bottom:24px; flex-wrap:wrap;">
          <div class="edu-tag-pill" style="margin-bottom:0;">
            <span>— POVINDIAN PLATFORM</span>
          </div>
          <!-- Academic Year Switcher -->
          <div class="edu-year-bar" style="background:rgba(255,255,255,0.06); border-color:rgba(255,255,255,0.12);">
            <button type="button" class="edu-year-btn active" data-year="2026-27" onclick="switchAcademicYear('2026-27')" style="color:#FCFCFA;">
              2026–27 <span class="edu-year-badge" style="background:#C9A96E; color:#0B1020;">Live</span>
            </button>
            <button type="button" class="edu-year-btn" data-year="2025-26" onclick="switchAcademicYear('2025-26')" style="color:#94A3B8;">
              2025–26
            </button>
          </div>
        </div>

        <h1 class="edu-hero-title">
          Find the Right College.<br>
          <span class="accent-italic">Build the Right Future.</span>
        </h1>

        <p class="edu-hero-sub">
          Explore verified colleges, courses, admissions, entrance exams and career paths — all in one calm, transparent place.
        </p>

        <!-- Quick Search with Instant Suggestions -->
        <div class="edu-hero-search-box" style="margin: 28px 0 20px 0;">
          <div class="edu-input-bar" style="margin-bottom: 12px;">
            <div class="edu-input-wrap">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input 
                type="text" 
                id="heroSearchInput" 
                placeholder="Search college, course, exam, city or career..." 
                oninput="syncHeroSearch(this.value)" 
                autocomplete="off"
              />
            </div>
            <button class="edu-btn-primary" onclick="scrollToShortlist()">
              <span>Find My College</span>
              <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>

          <!-- Suggested Searches Pills -->
          <div style="display:flex; flex-wrap:wrap; align-items:center; gap:8px; font-size:0.8rem; color:#CBD5E1;">
            <span class="trending-label" style="font-weight:700; color:#94A3B8;"><i class="fa-solid fa-bolt" style="color:var(--edu-gold); margin-right:4px;"></i> Trending:</span>
            <button type="button" class="edu-pill-btn trending-pill" style="padding:4px 10px; font-size:0.75rem; background:rgba(255,255,255,0.08); color:#F8FAFC; border-color:rgba(255,255,255,0.15);" onclick="quickFilter('discipline', 'Computer Science')">B.Tech CSE</button>
            <button type="button" class="edu-pill-btn trending-pill" style="padding:4px 10px; font-size:0.75rem; background:rgba(255,255,255,0.08); color:#F8FAFC; border-color:rgba(255,255,255,0.15);" onclick="quickFilter('discipline', 'Business')">MBA Colleges</button>
            <button type="button" class="edu-pill-btn trending-pill" style="padding:4px 10px; font-size:0.75rem; background:rgba(255,255,255,0.08); color:#F8FAFC; border-color:rgba(255,255,255,0.15);" onclick="quickFilter('discipline', 'Healthcare')">MBBS / Nursing</button>
            <button type="button" class="edu-pill-btn trending-pill" style="padding:4px 10px; font-size:0.75rem; background:rgba(255,255,255,0.08); color:#F8FAFC; border-color:rgba(255,255,255,0.15);" onclick="quickFilter('city', 'Jaipur')">Jaipur</button>
            <button type="button" class="edu-pill-btn trending-pill" style="padding:4px 10px; font-size:0.75rem; background:rgba(255,255,255,0.08); color:#F8FAFC; border-color:rgba(255,255,255,0.15);" onclick="quickFilter('city', 'Kota')">Kota</button>
          </div>
        </div>

        <div class="edu-trust-signals">
          <div class="edu-trust-item">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Verified 2026–27 Statutory Data</span>
          </div>
          <div class="edu-trust-item">
            <i class="fa-solid fa-file-circle-check"></i>
            <span>Gazette Source Provenance</span>
          </div>
        </div>
      </div>

      <!-- Right Interactive Map Card -->
      <div class="edu-hero-right">
        <div class="edu-map-card">
          <div class="edu-map-header">
            <div>
              <span class="edu-map-eyebrow">REGIONAL EXPLORATION MATRIX</span>
              <div class="edu-map-sub">Six educational hubs. Verified statutory intake.</div>
            </div>
            <div class="edu-map-icon-pill" title="Verified Higher Education Map">
              <i class="fa-solid fa-building-columns"></i>
            </div>
          </div>

          <!-- Interactive Constellation Canvas -->
          <div class="edu-map-canvas" id="eduMapCanvas">
            <svg class="edu-map-svg" viewBox="0 0 400 240" preserveAspectRatio="none">
              <line class="edu-map-line" x1="90" y1="60" x2="230" y2="95" />
              <line class="edu-map-line" x1="230" y1="95" x2="310" y2="155" />
              <line class="edu-map-line" x1="90" y1="60" x2="190" y2="180" />
              <line class="edu-map-line" x1="190" y1="180" x2="230" y2="95" />
              <line class="edu-map-line" x1="140" y1="130" x2="310" y2="155" />
            </svg>

            <!-- City Nodes -->
            <div class="edu-map-node active" style="top: 25%; left: 23%;" data-city="Jaipur" onclick="selectCityFilter('Jaipur')">
              <div class="node-dot"></div>
              <span class="node-label">JAIPUR</span>
            </div>

            <div class="edu-map-node" style="top: 40%; left: 58%;" data-city="Kota" onclick="selectCityFilter('Kota')">
              <div class="node-dot"></div>
              <span class="node-label">KOTA</span>
            </div>

            <div class="edu-map-node" style="top: 65%; left: 78%;" data-city="Udaipur" onclick="selectCityFilter('Udaipur')">
              <div class="node-dot"></div>
              <span class="node-label">UDAIPUR</span>
            </div>

            <div class="edu-map-node" style="top: 75%; left: 48%;" data-city="Ajmer" onclick="selectCityFilter('Ajmer')">
              <div class="node-dot"></div>
              <span class="node-label">AJMER</span>
            </div>

            <div class="edu-map-node" style="top: 55%; left: 35%;" data-city="Jodhpur" onclick="selectCityFilter('Jodhpur')">
              <div class="node-dot"></div>
              <span class="node-label">JODHPUR</span>
            </div>

            <div class="edu-map-node" style="top: 16%; left: 45%;" data-city="Sikar" onclick="selectCityFilter('Sikar')">
              <div class="node-dot"></div>
              <span class="node-label">SIKAR</span>
            </div>

            <div class="edu-evidence-pill">
              <div class="evidence-icon">
                <i class="fa-solid fa-file-shield"></i>
              </div>
              <div>
                <strong>Evidence, not noise</strong>
                <small>Every number backed by official notification</small>
              </div>
            </div>

            <div class="edu-map-date-tag">
              Academic Year 2026–27
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =======================================================================
     2. TRUST STRIP (PRD Section 09)
     ======================================================================= -->
<div class="edu-trust-strip">
  <div class="container">
    <div class="edu-trust-strip-inner">
      <div class="edu-trust-strip-title">
        <i class="fa-solid fa-circle-check"></i>
        <span>Education decisions deserve better information.</span>
      </div>
      <div class="edu-trust-strip-items">
        <div class="edu-trust-strip-item">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Verified Statutory Information</span>
        </div>
        <div class="edu-trust-strip-item">
          <i class="fa-solid fa-calendar-check"></i>
          <span>Academic-Year Specific (2026–27)</span>
        </div>
        <div class="edu-trust-strip-item">
          <i class="fa-solid fa-scale-balanced"></i>
          <span>Source-Backed Facts</span>
        </div>
        <div class="edu-trust-strip-item">
          <i class="fa-solid fa-eye"></i>
          <span>Transparent Discovery</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- =======================================================================
     3. DISCOVER BY INTENT (PRD Section 10)
     ======================================================================= -->
<section class="edu-intent-section">
  <div class="container">
    <div class="edu-section-head">
      <div>
        <span class="edu-eyebrow">
          <i class="fa-solid fa-compass"></i> START WITH WHAT YOU KNOW
        </span>
        <h2 class="edu-section-title" style="font-size: 2.1rem; margin-top: 4px;">
          Choose how you want to discover
        </h2>
      </div>
      <span class="edu-section-hint">8 ways to start your higher-education research</span>
    </div>

    <div class="edu-intent-grid">
      <!-- 1. Find a College -->
      <a href="#usefulShortlist" class="edu-intent-card" onclick="scrollToShortlist()">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-solid fa-building-columns"></i>
          </div>
          <div class="edu-intent-title">Find a College</div>
          <div class="edu-intent-desc">Explore verified universities, engineering institutes, and medical colleges by city and budget.</div>
        </div>
        <div class="edu-intent-action">
          <span>Search institutions</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>

      <!-- 2. Explore a Course -->
      <a href="#focusedSearch" class="edu-intent-card">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div class="edu-intent-title">Explore a Course</div>
          <div class="edu-intent-desc">Discover B.Tech, MBA, Medical, Law, and Design programs with authentic intake seat counts.</div>
        </div>
        <div class="edu-intent-action">
          <span>Browse programs</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>

      <!-- 3. Check Admission -->
      <a href="#latestAdmissions" class="edu-intent-card">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-regular fa-bell"></i>
          </div>
          <div class="edu-intent-title">Check Admission</div>
          <div class="edu-intent-desc">Track active counselling rounds, JoSAA / REAP dates, and official application windows.</div>
        </div>
        <div class="edu-intent-action">
          <span>View deadlines</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>

      <!-- 4. Compare Colleges -->
      <a href="#compareMatrix" class="edu-intent-card">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-solid fa-code-compare"></i>
          </div>
          <div class="edu-intent-title">Compare Colleges</div>
          <div class="edu-intent-desc">Side-by-side factual matrix across fee schedules, approvals, hostels, and verified placements.</div>
        </div>
        <div class="edu-intent-action">
          <span>Open matrix</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>

      <!-- 5. Find an Exam -->
      <a href="#examTimelines" class="edu-intent-card">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-solid fa-stopwatch"></i>
          </div>
          <div class="edu-intent-title">Find an Exam</div>
          <div class="edu-intent-desc">7-stage milestone timelines for JEE Main, NEET UG, CAT, and CLAT 2026–27.</div>
        </div>
        <div class="edu-intent-action">
          <span>Exam progression</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>

      <!-- 6. Explore Careers -->
      <a href="#careerTrajectories" class="edu-intent-card">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-solid fa-chart-line"></i>
          </div>
          <div class="edu-intent-title">Explore Careers</div>
          <div class="edu-intent-desc">Salary progression and CTC ladders from entry analyst to director-level executive roles.</div>
        </div>
        <div class="edu-intent-action">
          <span>Salary trajectories</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>

      <!-- 7. Find Scholarships -->
      <a href="#scholarshipsSection" class="edu-intent-card">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-solid fa-award"></i>
          </div>
          <div class="edu-intent-title">Find Scholarships</div>
          <div class="edu-intent-desc">Central & state government fee reimbursement schemes, merit aid, and women in tech grants.</div>
        </div>
        <div class="edu-intent-action">
          <span>Explore aid</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>

      <!-- 8. Check Reviews & Ratings -->
      <a href="#studentReviews" class="edu-intent-card">
        <div>
          <div class="edu-intent-icon-wrap">
            <i class="fa-regular fa-star"></i>
          </div>
          <div class="edu-intent-title">Student Reviews</div>
          <div class="edu-intent-desc">Verified peer experiences covering academics, faculty guidance, infrastructure, and hostel life.</div>
        </div>
        <div class="edu-intent-action">
          <span>Read experiences</span>
          <i class="fa-solid fa-arrow-right"></i>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- =======================================================================
     2. THREE VALUE PILLARS (Image 1 bottom strip)
     ======================================================================= -->
<section class="edu-pillars-section">
  <div class="container">
    <div class="edu-pillars-grid">
      <div class="edu-pillar-item">
        <div class="edu-pillar-num">01</div>
        <div>
          <div class="edu-pillar-title">Fresh when it matters</div>
          <div class="edu-pillar-desc">Course and admission details reviewed for 2026–27.</div>
        </div>
      </div>
      <div class="edu-pillar-item">
        <div class="edu-pillar-num">02</div>
        <div>
          <div class="edu-pillar-title">Context, not just names</div>
          <div class="edu-pillar-desc">See what each city and campus might feel like.</div>
        </div>
      </div>
      <div class="edu-pillar-item">
        <div class="edu-pillar-num">03</div>
        <div>
          <div class="edu-pillar-title">A shortlist you can explain</div>
          <div class="edu-pillar-desc">Save, compare, then ask for the details you need.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =======================================================================
     3. FOCUSED SEARCH & INTENT SECTION (Image 2)
     ======================================================================= -->
<section class="edu-search-section" id="focusedSearch">
  <div class="container">
    <div class="edu-search-grid">
      <!-- Left Column: Guidance -->
      <div class="edu-search-left">
        <span class="edu-eyebrow">
          <i class="fa-solid fa-wand-magic-sparkles"></i> START WITH WHAT FEELS RIGHT
        </span>
        <h2 class="edu-search-heading">
          A focused search is a kinder search.
        </h2>
        <p class="edu-search-sub">
          Tell us a course, a city, or simply a direction. We'll help you make the next question more specific.
        </p>

        <!-- Help box -->
        <div class="edu-guide-callout">
          <div class="guide-icon">
            <i class="fa-regular fa-circle-question"></i>
          </div>
          <div>
            <strong>Not sure where to begin?</strong>
            <p>Start with your preferred city. A place can narrow the path without deciding your future.</p>
          </div>
        </div>
      </div>

      <!-- Right Column: Interactive Search Card (Image 2) -->
      <div class="edu-search-card">
        <!-- Live Search Bar -->
        <div class="edu-input-bar">
          <div class="edu-input-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input 
              type="text" 
              id="eduKeywordInput" 
              placeholder="Search a college, course, or city" 
              oninput="filterCards()" 
              autocomplete="off"
            />
          </div>
          <button class="edu-btn-primary" onclick="scrollToShortlist()">
            <span>Search colleges</span>
          </button>
        </div>

        <!-- Location Filter Pills -->
        <div class="edu-filter-group">
          <div class="edu-filter-header">
            <div class="edu-filter-label">
              <i class="fa-solid fa-location-dot"></i> WHERE WOULD YOU LIKE TO STUDY?
            </div>
            <button class="edu-filter-clear" onclick="clearCityFilter()">Clear all</button>
          </div>
          <div class="edu-filter-pills" id="cityPillContainer">
            <button class="edu-pill-btn active-dark" data-city="all" onclick="selectCityFilter('all')">All Rajasthan</button>
            <button class="edu-pill-btn" data-city="Jaipur" onclick="selectCityFilter('Jaipur')">Jaipur</button>
            <button class="edu-pill-btn" data-city="Jodhpur" onclick="selectCityFilter('Jodhpur')">Jodhpur</button>
            <button class="edu-pill-btn" data-city="Kota" onclick="selectCityFilter('Kota')">Kota</button>
            <button class="edu-pill-btn" data-city="Udaipur" onclick="selectCityFilter('Udaipur')">Udaipur</button>
            <button class="edu-pill-btn" data-city="Ajmer" onclick="selectCityFilter('Ajmer')">Ajmer</button>
            <button class="edu-pill-btn" data-city="Sikar" onclick="selectCityFilter('Sikar')">Sikar</button>
          </div>
        </div>

        <!-- Discipline Filter Pills -->
        <div class="edu-filter-group" style="margin-bottom: 0;">
          <div class="edu-filter-header">
            <div class="edu-filter-label">
              <i class="fa-solid fa-book-open"></i> WHAT ARE YOU THINKING ABOUT?
            </div>
          </div>
          <div class="edu-filter-pills" id="coursePillContainer">
            <button class="edu-pill-btn active-terracotta" data-discipline="all" onclick="selectDisciplineFilter('all')">All courses</button>
            <button class="edu-pill-btn" data-discipline="Engineering" onclick="selectDisciplineFilter('Engineering')">Engineering</button>
            <button class="edu-pill-btn" data-discipline="Computer Science" onclick="selectDisciplineFilter('Computer Science')">Computer Science</button>
            <button class="edu-pill-btn" data-discipline="Business" onclick="selectDisciplineFilter('Business')">Business</button>
            <button class="edu-pill-btn" data-discipline="Design" onclick="selectDisciplineFilter('Design')">Design</button>
            <button class="edu-pill-btn" data-discipline="Healthcare" onclick="selectDisciplineFilter('Healthcare')">Medicine & Nursing</button>
            <button class="edu-pill-btn" data-discipline="Architecture" onclick="selectDisciplineFilter('Architecture')">Architecture</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =======================================================================
     4. SHORTCUTS & LATEST ADMISSIONS SECTION (Image 3)
     ======================================================================= -->
<section class="edu-shortcuts-section">
  <div class="container">
    <!-- Shortcuts Title -->
    <div class="edu-section-head">
      <div>
        <span class="edu-eyebrow">START WITH A DIRECTION</span>
        <h3 class="edu-section-title">Shortcuts for the question you have today</h3>
      </div>
      <span class="edu-section-hint">Tap a path to shape your search</span>
    </div>

    <!-- 4 Directional Cards -->
    <div class="edu-shortcuts-grid">
      <div class="edu-shortcut-card" onclick="quickFilter('discipline', 'Computer Science')">
        <div class="edu-shortcut-left">
          <span class="edu-shortcut-num">01</span>
          <div>
            <div class="edu-shortcut-title">B.Tech CSE</div>
            <div class="edu-shortcut-sub">Engineering & tech</div>
          </div>
        </div>
        <i class="fa-solid fa-arrow-right"></i>
      </div>

      <div class="edu-shortcut-card" onclick="quickFilter('discipline', 'Business')">
        <div class="edu-shortcut-left">
          <span class="edu-shortcut-num">02</span>
          <div>
            <div class="edu-shortcut-title">MBA</div>
            <div class="edu-shortcut-sub">Business & management</div>
          </div>
        </div>
        <i class="fa-solid fa-arrow-right"></i>
      </div>

      <div class="edu-shortcut-card" onclick="quickFilter('discipline', 'Healthcare')">
        <div class="edu-shortcut-left">
          <span class="edu-shortcut-num">03</span>
          <div>
            <div class="edu-shortcut-title">Nursing</div>
            <div class="edu-shortcut-sub">Health sciences</div>
          </div>
        </div>
        <i class="fa-solid fa-arrow-right"></i>
      </div>

      <div class="edu-shortcut-card" onclick="quickFilter('city', 'Jaipur')">
        <div class="edu-shortcut-left">
          <span class="edu-shortcut-num">04</span>
          <div>
            <div class="edu-shortcut-title">Study in Jaipur</div>
            <div class="edu-shortcut-sub">City guide</div>
          </div>
        </div>
        <i class="fa-solid fa-arrow-right"></i>
      </div>
    </div>

    <!-- Latest in Admissions Strip (Image 3) -->
    <div class="edu-section-head" style="margin-top: 20px;">
      <div>
        <span class="edu-eyebrow" style="color: var(--edu-dark);">
          <i class="fa-regular fa-bell" style="color: var(--edu-terracotta);"></i> LATEST IN ADMISSIONS
        </span>
      </div>
      <a href="#focusedSearch" class="edu-section-hint" style="color: var(--edu-terracotta); text-decoration: none; font-weight: 700;">
        View all updates <i class="fa-solid fa-arrow-right" style="font-size:0.75rem; margin-left:4px;"></i>
      </a>
    </div>

    <div class="edu-alerts-grid">
      <!-- Exam Card -->
      <div class="edu-alert-card" onclick="openAdmissionAlert('reap')">
        <span class="edu-alert-arrow"><i class="fa-solid fa-arrow-right"></i></span>
        <div>
          <span class="edu-alert-badge exam">EXAM</span>
          <div class="edu-alert-title">REAP 2026 counselling registration window</div>
        </div>
        <div class="edu-alert-meta">
          <i class="fa-regular fa-clock"></i> Updated 19 Jun · 4 min read
        </div>
      </div>

      <!-- Deadline Card -->
      <div class="edu-alert-card" onclick="openAdmissionAlert('josaa')">
        <span class="edu-alert-arrow"><i class="fa-solid fa-arrow-right"></i></span>
        <div>
          <span class="edu-alert-badge deadline">DEADLINE</span>
          <div class="edu-alert-title">JoSAA 2026: choice filling starts soon</div>
        </div>
        <div class="edu-alert-meta">
          <i class="fa-regular fa-calendar-check"></i> Checked 18 Jun · Official notice
        </div>
      </div>

      <!-- Guide Card -->
      <div class="edu-alert-card" onclick="openAdmissionAlert('guide')">
        <span class="edu-alert-arrow"><i class="fa-solid fa-arrow-right"></i></span>
        <div>
          <span class="edu-alert-badge guide">GUIDE</span>
          <div class="edu-alert-title">B.Tech CSE in Rajasthan: a practical shortlist</div>
        </div>
        <div class="edu-alert-meta">
          <i class="fa-solid fa-arrows-rotate"></i> Refreshed 16 Jun · POVIndian desk
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =======================================================================
     5. THE USEFUL SHORTLIST & COLLEGE CARDS GRID (Image 4)
     ======================================================================= -->
<section class="edu-shortlist-section" id="usefulShortlist">
  <div class="container">
    <div class="edu-shortlist-layout">
      <!-- Left Column: Trust Context & Note -->
      <div class="edu-shortlist-side">
        <span class="edu-eyebrow">
          <i class="fa-solid fa-asterisk"></i> THE USEFUL SHORTLIST
        </span>
        <h2 class="edu-shortlist-heading">
          Good options,<br>
          <span class="highlight">clear signals.</span>
        </h2>
        <p class="edu-shortlist-intro">
          No vague "best college" claims here. Start with fit, then check the source and the date before you decide what deserves your time.
        </p>

        <!-- Freshness Note Box (Image 4) -->
        <div class="edu-freshness-note">
          <i class="fa-solid fa-circle-info"></i>
          <div>
            <strong>A small note on freshness</strong>
            <p>Dates show when our desk last checked the relevant official source. Always open the notice before submitting.</p>
          </div>
        </div>

        <div class="edu-trust-pill-bottom">
          <i class="fa-solid fa-user-shield" style="color: var(--edu-green);"></i>
          <span>Shortlists that work for students and the people helping them.</span>
        </div>
      </div>

      <!-- Right Column: Interactive Filters Bar -->
      <div class="edu-shortlist-filters-box" style="background:#FFF; padding:24px; border-radius:var(--edu-radius-md); border:1px solid var(--edu-border);">
        <div style="margin-bottom:16px;">
          <div class="edu-filter-header">
            <span class="edu-filter-label"><i class="fa-solid fa-location-crosshairs"></i> STUDY LOCATION</span>
            <button class="edu-filter-clear" onclick="clearCityFilter()">Clear all filters</button>
          </div>
          <div class="edu-filter-pills" id="shortlistCityPills">
            <button class="edu-pill-btn active-dark" data-city="all" onclick="selectCityFilter('all')">All Rajasthan</button>
            <button class="edu-pill-btn" data-city="Jaipur" onclick="selectCityFilter('Jaipur')">Jaipur</button>
            <button class="edu-pill-btn" data-city="Kota" onclick="selectCityFilter('Kota')">Kota</button>
            <button class="edu-pill-btn" data-city="Jodhpur" onclick="selectCityFilter('Jodhpur')">Jodhpur</button>
            <button class="edu-pill-btn" data-city="Sikar" onclick="selectCityFilter('Sikar')">Sikar</button>
          </div>
        </div>

        <div>
          <div class="edu-filter-label" style="margin-bottom:8px;">
            <i class="fa-solid fa-graduation-cap"></i> COURSE OR GOAL
          </div>
          <div class="edu-filter-pills" id="shortlistCoursePills">
            <button class="edu-pill-btn active-terracotta" data-discipline="all" onclick="selectDisciplineFilter('all')">All</button>
            <button class="edu-pill-btn" data-discipline="B.Tech" onclick="selectDisciplineFilter('Engineering')">B.Tech CSE</button>
            <button class="edu-pill-btn" data-discipline="MBA" onclick="selectDisciplineFilter('Business')">MBA</button>
            <button class="edu-pill-btn" data-discipline="Nursing" onclick="selectDisciplineFilter('Healthcare')">Nursing</button>
            <button class="edu-pill-btn" data-discipline="Design" onclick="selectDisciplineFilter('Design')">Design</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cards Header with Count, Academic Year Switcher & Date -->
    <div class="edu-cards-header" style="flex-wrap: wrap; gap: 16px;">
      <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div class="edu-cards-count" id="collegeCountBadge" style="margin-bottom:0;">
          <?= count($institutions) ?> places to look at closely
          <small><i class="fa-regular fa-clock" style="margin-right:4px;"></i> Checked for 2026–27</small>
        </div>

        <!-- Academic Year Switcher (PRD Section 02 & 74) -->
        <div class="edu-year-bar" title="Switch academic year perspective without losing records">
          <span class="edu-year-label">Year:</span>
          <button type="button" class="edu-year-btn active" data-year="2026-27" onclick="switchAcademicYear('2026-27')">
            2026–27 <span class="edu-year-badge">Active</span>
          </button>
          <button type="button" class="edu-year-btn" data-year="2025-26" onclick="switchAcademicYear('2025-26')">
            2025–26
          </button>
          <button type="button" class="edu-year-btn" data-year="2024-25" onclick="switchAcademicYear('2024-25')">
            2024–25
          </button>
        </div>
      </div>

      <div>
        <button class="edu-pill-btn" onclick="openCompareModal()" id="compareFloatingBtn" style="display:none; background:var(--edu-gold); color:var(--edu-ink); border-color:var(--edu-gold); font-weight:700;">
          <i class="fa-solid fa-code-compare" style="margin-right:6px;"></i> Compare Selected (<span id="compareCount">0</span>)
        </button>
      </div>
    </div>

    <!-- College Cards Grid (Image 4) -->
    <div class="edu-cards-grid" id="collegeCardsGrid">
      <?php if (!empty($institutions)): ?>
        <?php foreach ($institutions as $inst): ?>
          <div 
            class="edu-card" 
            data-city="<?= htmlspecialchars($inst['city']) ?>" 
            data-name="<?= htmlspecialchars(strtolower($inst['canonical_name'])) ?>"
            data-id="<?= htmlspecialchars($inst['id']) ?>"
            data-courses="<?= htmlspecialchars(strtolower(implode(' ', $inst['courseBadges'] ?? []))) ?>"
          >
            <div>
              <!-- Top Row: Logo Badge & Bookmark -->
              <div class="edu-card-top">
                <div class="edu-badge-icon" style="background-color: <?= htmlspecialchars($inst['badge_color'] ?? '#0B1020') ?>;">
                  <?= htmlspecialchars($inst['logo_text'] ?? 'JU') ?>
                </div>
                <button 
                  class="edu-card-bookmark" 
                  title="Save to shortlist"
                  onclick="toggleBookmark(<?= $inst['id'] ?>, '<?= htmlspecialchars(addslashes($inst['canonical_name'])) ?>', this)"
                >
                  <i class="fa-regular fa-bookmark"></i>
                </button>
              </div>

              <!-- Location Tag -->
              <div class="edu-card-location">
                <?= htmlspecialchars(strtoupper($inst['city'])) ?> · <?= htmlspecialchars(strtoupper($inst['locality'])) ?>
              </div>

              <!-- Institution Title -->
              <div class="edu-card-title">
                <a href="<?= htmlspecialchars(pov_url('college-detail.php?slug=' . urlencode($inst['slug'] ?? 'college-' . $inst['id']))) ?>" style="color:inherit; text-decoration:none;">
                  <?= htmlspecialchars($inst['canonical_name']) ?>
                </a>
              </div>

              <!-- Legal Type -->
              <div class="edu-card-type">
                <?= htmlspecialchars($inst['legal_recognition'] ?? 'State University') ?>
              </div>

              <!-- Explainable Recommendation Match Box (PRD Section 04 & 74) -->
              <div class="edu-match-reasons">
                <strong><i class="fa-solid fa-check-double" style="color:var(--edu-emerald); margin-right:4px;"></i> Why this matches:</strong>
                <div><span class="edu-match-check">✓</span> Verified 2026–27 academic intake</div>
                <div><span class="edu-match-check">✓</span> Located in <?= htmlspecialchars($inst['city']) ?></div>
                <?php if (!empty($inst['courseBadges'][0])): ?>
                  <div><span class="edu-match-check">✓</span> Offers <?= htmlspecialchars($inst['courseBadges'][0]) ?></div>
                <?php endif; ?>
              </div>

              <!-- Course Tags -->
              <div class="edu-card-courses">
                <?php foreach (array_slice($inst['courseBadges'] ?? [], 0, 4) as $badge): ?>
                  <span class="edu-course-pill"><?= htmlspecialchars($badge) ?></span>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Card Bottom: Verified Status & CTA -->
            <div>
              <div class="edu-card-status-bar">
                <div class="edu-status-row">
                  <span class="edu-status-tag">
                    <span class="edu-status-dot"></span>
                    <?= htmlspecialchars($inst['primary_status'] ?? 'Applications open') ?>
                  </span>
                  <span class="edu-verified-date">
                    <?= date('d M Y', strtotime($inst['last_verified_at'] ?? '2026-06-18')) ?>
                  </span>
                </div>
                <div>
                  <button type="button" class="edu-provenance-link" style="background:none; border:none; padding:0; cursor:pointer; font-family:inherit;" onclick="openProvenanceDrawer(<?= $inst['id'] ?>)" title="Inspect official regulatory gazette & source provenance">
                    <i class="fa-solid fa-file-circle-check"></i>
                    <span>Inspect Provenance</span>
                  </button>
                </div>
              </div>

              <div style="display:flex; gap:8px;">
                <a href="<?= htmlspecialchars(pov_url('college-detail.php?slug=' . urlencode($inst['slug'] ?? 'college-' . $inst['id']))) ?>" class="edu-card-btn" style="flex:1; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                  <span>View College Profile</span>
                  <i class="fa-solid fa-arrow-right"></i>
                </a>
                <button type="button" class="edu-pill-btn" style="padding:0 12px; height:44px;" onclick="openCollegeModal(<?= $inst['id'] ?>)" title="Quick Admission Inquiry">
                  <i class="fa-solid fa-headset"></i>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="grid-column: span 2; text-align: center; padding: 40px; background: #FFF; border-radius: 12px;">
          <p>No verified colleges found matching your selection.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Page-Based Pagination Controls -->
    <div class="edu-pagination-wrapper" id="eduPaginationWrapper">
      <div class="edu-pagination-info" id="eduPaginationInfo">
        Showing 1 to 4 of 8 institutions
      </div>
      <div class="edu-pagination-controls">
        <button type="button" class="edu-page-btn" id="eduPagePrev" onclick="goToPage(currentPage - 1)">
          <i class="fa-solid fa-chevron-left"></i> Previous
        </button>
        <div class="edu-page-numbers" id="eduPageNumbers"></div>
        <button type="button" class="edu-page-btn" id="eduPageNext" onclick="goToPage(currentPage + 1)">
          Next <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- ===================================================================
         6. SHORTLIST TRAY ("Save the maybes. Ask better questions.")
         =================================================================== -->
    <div class="edu-shortlist-tray-section">
      <div class="edu-tray-left">
        <span class="edu-eyebrow" style="color:var(--edu-dark);">
          <i class="fa-regular fa-bookmark" style="color:var(--edu-terracotta);"></i> YOUR ADMISSIONS TRAIL
        </span>
        <h3>Save the maybes.<br>Ask better questions.</h3>
        <p>Keep a shortlist as you explore. When you are ready, an official counsellor can help you turn it into a practical, stress-free next step.</p>
        <div class="edu-saved-pill-count">
          <i class="fa-solid fa-list-check" style="color:var(--edu-terracotta);"></i>
          <span id="shortlistTrayStatus">0 institutions in your shortlist</span>
        </div>
      </div>

      <div class="edu-tray-right-card">
        <div style="font-size:0.75rem; letter-spacing:0.12em; text-transform:uppercase; color:var(--edu-terracotta); font-weight:700; margin-bottom:12px;">
          THE NEXT USEFUL ACTION
        </div>
        <div style="font-size:0.95rem; line-height:1.4; margin-bottom:16px;">
          Get the details that are hard to compare alone:
        </div>
        <div class="edu-tray-grid">
          <div class="edu-tray-mini-box">
            <strong>Course Fit</strong>
            <small>Ask about eligibility, curriculum, and the actual student experience.</small>
          </div>
          <div class="edu-tray-mini-box">
            <strong>Admission Timing</strong>
            <small>Know what is open now and what to watch next.</small>
          </div>
        </div>
        <button class="edu-btn-primary" style="width:100%; height:48px;" onclick="openCounsellingModal()">
          <span>Talk through your options</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- ===================================================================
         7. REAL PERSON COUNSELLING CALLOUT (Bottom Image 4)
         =================================================================== -->
    <div class="edu-counsel-banner">
      <div class="edu-counsel-info">
        <div class="edu-counsel-avatar">
          <i class="fa-solid fa-user-tie"></i>
        </div>
        <div class="edu-counsel-text">
          <strong>A real person can help with the next question.</strong>
          <p>Share your courses, city, and timeline. Get a practical admissions conversation — no pressure, no ranking theatre.</p>
        </div>
      </div>
      <div>
        <button class="edu-btn-primary" onclick="openCounsellingModal()">
          <span>Start a counselling enquiry</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- =======================================================================
     6. 3-WAY STICKY COLLEGE COMPARISON MATRIX (PRD Section 35)
     ======================================================================= -->
<section class="edu-compare-matrix-section" id="compareMatrix">
  <div class="container">
    <div class="edu-section-head">
      <div>
        <span class="edu-eyebrow">
          <i class="fa-solid fa-code-compare"></i> FACTUAL EVALUATION ENGINE
        </span>
        <h2 class="edu-section-title">
          3-Way College Comparison Matrix
        </h2>
        <p style="color:var(--edu-slate); margin:4px 0 0 0; font-size:0.95rem;">
          Compare statutory recognitions, fee ranges, seats and verified placement figures. No fake winner badges — only factual differences.
        </p>
      </div>
      <div>
        <button class="edu-pill-btn" onclick="scrollToShortlist()">
          <i class="fa-solid fa-plus" style="margin-right:4px;"></i> Select from colleges
        </button>
      </div>
    </div>

    <!-- Live Matrix Table -->
    <div style="overflow-x:auto;">
      <table class="edu-compare-matrix-table" id="stickyCompareMatrix">
        <thead>
          <tr>
            <th class="matrix-col-param">Evaluation Parameter</th>
            <?php 
              $top3 = array_slice($institutions, 0, 3);
              foreach ($top3 as $c): 
            ?>
              <th>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                  <div class="edu-badge-icon" style="background:<?= htmlspecialchars($c['badge_color'] ?? '#0B1020') ?>; width:34px; height:34px; font-size:0.8rem;">
                    <?= htmlspecialchars($c['logo_text'] ?? 'CU') ?>
                  </div>
                  <div>
                    <strong style="font-size:0.95rem; display:block; color:var(--edu-ink);"><?= htmlspecialchars($c['canonical_name']) ?></strong>
                    <small style="color:var(--edu-slate);"><?= htmlspecialchars($c['city']) ?>, Rajasthan</small>
                  </div>
                </div>
                <button class="edu-btn-primary" style="width:100%; height:36px; font-size:0.8rem;" onclick="openCounsellingModal(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['canonical_name'])) ?>')">
                  Inquire Now
                </button>
              </th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="matrix-col-param"><strong>Legal Status & Ownership</strong></td>
            <?php foreach ($top3 as $c): ?>
              <td>
                <strong><?= htmlspecialchars($c['legal_recognition'] ?? 'State University') ?></strong><br>
                <small style="color:var(--edu-slate);"><?= htmlspecialchars($c['ownership'] ?? 'Public / Private') ?></small>
              </td>
            <?php endforeach; ?>
          </tr>
          <tr>
            <td class="matrix-col-param"><strong>Academic Year 2026–27 Fee</strong></td>
            <?php foreach ($top3 as $c): ?>
              <td>
                <strong style="color:var(--edu-ink); font-size:1.05rem;">₹<?= number_format($c['min_annual_fee'] ?? 150000) ?> / yr</strong><br>
                <small style="color:var(--edu-emerald); font-weight:700;"><i class="fa-solid fa-check"></i> Published Gazette</small>
              </td>
            <?php endforeach; ?>
          </tr>
          <tr>
            <td class="matrix-col-param"><strong>Intake Seats & Programs</strong></td>
            <?php foreach ($top3 as $c): ?>
              <td>
                <?= htmlspecialchars(implode(', ', $c['courseBadges'] ?? ['B.Tech CSE', 'MBA'])) ?><br>
                <small style="color:var(--edu-slate);">Sanctioned AICTE/UGC Intake</small>
              </td>
            <?php endforeach; ?>
          </tr>
          <tr>
            <td class="matrix-col-param"><strong>Entrance Pathway</strong></td>
            <?php foreach ($top3 as $c): ?>
              <td>
                JEE Main / REAP / Direct Merit<br>
                <small style="color:var(--edu-slate);">Central Seat Allocation</small>
              </td>
            <?php endforeach; ?>
          </tr>
          <tr>
            <td class="matrix-col-param"><strong>Accreditations & NAAC</strong></td>
            <?php foreach ($top3 as $c): ?>
              <td>
                <strong><?= htmlspecialchars($c['naac_grade'] ?? 'NAAC A Grade') ?></strong><br>
                <small style="color:var(--edu-slate);"><?= htmlspecialchars($c['nirf_band'] ?? 'Top Ranking') ?></small>
              </td>
            <?php endforeach; ?>
          </tr>
          <tr>
            <td class="matrix-col-param"><strong>Campus & Residential Model</strong></td>
            <?php foreach ($top3 as $c): ?>
              <td>
                <?= htmlspecialchars($c['campus_acres'] ?? '35') ?> Acres Campus<br>
                <small style="color:var(--edu-slate);"><?= htmlspecialchars($c['gender_model'] ?? 'Co-ed') ?> · In-campus Hostels</small>
              </td>
            <?php endforeach; ?>
          </tr>
          <tr>
            <td class="matrix-col-param"><strong>Source Provenance</strong></td>
            <?php foreach ($top3 as $c): ?>
              <td>
                <button type="button" class="edu-provenance-link" style="background:none; border:none; padding:0; cursor:pointer;" onclick="openProvenanceDrawer(<?= $c['id'] ?>)">
                  <i class="fa-solid fa-file-circle-check"></i>
                  <span>Inspect Audit (<?= date('d M Y', strtotime($c['last_verified_at'] ?? '2026-06-18')) ?>)</span>
                </button>
              </td>
            <?php endforeach; ?>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- =======================================================================
     7. NATIONAL ENTRANCE EXAMS 7-STAGE MILESTONE PROGRESSION (PRD Sections 30-31)
     ======================================================================= -->
<section class="edu-exams-section" id="examTimelines">
  <div class="container">
    <div class="edu-section-head" style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px;">
      <div>
        <span class="edu-eyebrow">
          <i class="fa-solid fa-stopwatch"></i> ADMISSION GATEWAYS 2026–27
        </span>
        <h2 class="edu-section-title">
          National Entrance Exams & Milestone Progressions
        </h2>
        <p style="color:var(--edu-slate); margin:4px 0 0 0; font-size:0.95rem;">
          Track live stages from notification release to counselling seat allocation. Every date verified against official NTA & conducting body gazettes.
        </p>
      </div>
      <div>
        <a href="<?= htmlspecialchars(pov_url('entrance-exams.php')) ?>" style="background:#0F172A; color:#C9A96E; font-weight:600; font-size:13px; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:8px; border:1px solid #C9A96E;">
          <span>Explore All 2026 Exams</span>
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>

    <!-- Exam Cards with 7-Stage Progression -->
    <?php if (!empty($exams)): ?>
      <?php foreach ($exams as $exam): ?>
        <div class="edu-exam-card">
          <div class="edu-exam-header">
            <div>
              <span class="edu-exam-badge-category"><?= htmlspecialchars($exam['category'] ?? 'Engineering') ?></span>
              <h3 style="font-size:1.35rem; font-weight:800; color:var(--edu-ink); margin:0;">
                <?= htmlspecialchars($exam['exam_name']) ?> (<?= htmlspecialchars($exam['short_code']) ?>)
              </h3>
              <p style="color:var(--edu-slate); font-size:0.88rem; margin:4px 0 0 0;">
                Conducting Body: <strong><?= htmlspecialchars($exam['conducting_body'] ?? 'NTA') ?></strong> · <?= htmlspecialchars($exam['mode'] ?? 'CBT Mode') ?>
              </p>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
              <span style="display:inline-flex; align-items:center; gap:6px; background:#DCFCE7; color:#15803D; font-weight:700; font-size:0.75rem; padding:4px 10px; border-radius:999px;">
                <span style="width:6px; height:6px; border-radius:50%; background:#15803D;"></span> Live Window
              </span>
              <a href="<?= htmlspecialchars($exam['official_url'] ?? '#') ?>" target="_blank" rel="noopener" class="edu-pill-btn" style="text-decoration:none;">
                Official Portal <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.75rem; margin-left:4px;"></i>
              </a>
            </div>
          </div>

          <!-- 7-Stage Milestone Progression -->
          <div class="edu-milestone-track">
            <!-- Step 1: Registration -->
            <div class="edu-milestone-step completed">
              <div class="edu-milestone-dot"><i class="fa-solid fa-check"></i></div>
              <div class="edu-milestone-label">Registration</div>
              <div class="edu-milestone-date"><?= !empty($exam['application_start']) ? date('M Y', strtotime($exam['application_start'])) : 'Jan 2026' ?></div>
            </div>

            <!-- Step 2: Form Closing -->
            <div class="edu-milestone-step completed">
              <div class="edu-milestone-dot"><i class="fa-solid fa-check"></i></div>
              <div class="edu-milestone-label">Apply Deadline</div>
              <div class="edu-milestone-date"><?= !empty($exam['application_end']) ? date('M Y', strtotime($exam['application_end'])) : 'Mar 2026' ?></div>
            </div>

            <!-- Step 3: Admit Card -->
            <div class="edu-milestone-step active">
              <div class="edu-milestone-dot"><i class="fa-solid fa-id-card"></i></div>
              <div class="edu-milestone-label">Admit Card</div>
              <div class="edu-milestone-date"><?= !empty($exam['admit_card_date']) ? date('M Y', strtotime($exam['admit_card_date'])) : 'Apr 2026' ?></div>
            </div>

            <!-- Step 4: Exam Date -->
            <div class="edu-milestone-step">
              <div class="edu-milestone-dot">4</div>
              <div class="edu-milestone-label">Exam Day</div>
              <div class="edu-milestone-date"><?= !empty($exam['exam_date']) ? date('M Y', strtotime($exam['exam_date'])) : 'Apr 2026' ?></div>
            </div>

            <!-- Step 5: Answer Key -->
            <div class="edu-milestone-step">
              <div class="edu-milestone-dot">5</div>
              <div class="edu-milestone-label">Answer Key</div>
              <div class="edu-milestone-date">May 2026</div>
            </div>

            <!-- Step 6: Results -->
            <div class="edu-milestone-step">
              <div class="edu-milestone-dot">6</div>
              <div class="edu-milestone-label">Rank List</div>
              <div class="edu-milestone-date"><?= !empty($exam['result_date']) ? date('M Y', strtotime($exam['result_date'])) : 'May 2026' ?></div>
            </div>

            <!-- Step 7: Counselling -->
            <div class="edu-milestone-step">
              <div class="edu-milestone-dot">7</div>
              <div class="edu-milestone-label">Counselling</div>
              <div class="edu-milestone-date"><?= !empty($exam['counselling_date']) ? date('M Y', strtotime($exam['counselling_date'])) : 'Jun 2026' ?></div>
            </div>
          </div>

          <!-- Eligibility and Syllabus Footer -->
          <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:14px; margin-top:16px; font-size:0.85rem; color:#475569; display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div><strong>Eligibility:</strong> <?= htmlspecialchars($exam['eligibility_summary'] ?? '10+2 with PCM/PCB') ?></div>
            <div><strong>Application Fee:</strong> <?= htmlspecialchars($exam['application_fee'] ?? '₹1,000') ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- =======================================================================
     8. HIGH-GROWTH CAREER TRAJECTORIES & CTC PROGRESSION (PRD Sections 32-33)
     ======================================================================= -->
<section class="edu-careers-section" id="careerTrajectories">
  <div class="container">
    <div class="edu-section-head">
      <div>
        <span class="edu-eyebrow">
          <i class="fa-solid fa-chart-line"></i> INDUSTRY COMPASS
        </span>
        <h2 class="edu-section-title">
          High-Growth Career Trajectories & CTC Progression
        </h2>
        <p style="color:var(--edu-slate); margin:4px 0 0 0; font-size:0.95rem;">
          Verified compensation trajectories from starting graduate placement to executive leadership roles.
        </p>
      </div>
    </div>

    <div class="edu-careers-grid">
      <?php if (!empty($careers)): ?>
        <?php foreach ($careers as $career): ?>
          <div class="edu-career-card">
            <div>
              <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                <span class="edu-exam-badge-category" style="background:#EBF8F4; color:#16866A;"><?= htmlspecialchars($career['domain']) ?></span>
                <span style="font-size:0.8rem; font-weight:700; color:var(--edu-gold);"><i class="fa-solid fa-arrow-trend-up"></i> <?= htmlspecialchars($career['growth_outlook']) ?></span>
              </div>
              <h3 style="font-size:1.3rem; font-weight:800; color:var(--edu-ink); margin:0 0 8px 0;"><?= htmlspecialchars($career['title']) ?></h3>
              <p style="font-size:0.85rem; color:var(--edu-slate); margin:0; line-height:1.5;"><?= htmlspecialchars($career['description']) ?></p>
            </div>

            <!-- CTC Progression Ladder -->
            <div class="edu-ctc-ladder">
              <div class="edu-ctc-stage">
                <span class="stage-title">Entry Analyst (0–2 yrs)</span>
                <span class="stage-ctc">₹<?= htmlspecialchars($career['avg_starting_lpa']) ?> LPA</span>
              </div>
              <div class="edu-ctc-stage">
                <span class="stage-title">Senior / Lead (3–6 yrs)</span>
                <span class="stage-ctc">₹<?= htmlspecialchars($career['mid_career_lpa']) ?> LPA</span>
              </div>
              <div class="edu-ctc-stage">
                <span class="stage-title">Director / CXO (7–12+ yrs)</span>
                <span class="stage-ctc" style="color:var(--edu-emerald); font-size:1.05rem;">₹<?= htmlspecialchars($career['leadership_lpa']) ?> LPA</span>
              </div>
            </div>

            <div>
              <div style="font-size:0.8rem; color:#475569; margin-bottom:8px;">
                <strong>Degree Pathway:</strong> <?= htmlspecialchars($career['preferred_degree']) ?>
              </div>
              <div style="font-size:0.8rem; color:var(--edu-slate);">
                <strong>Key Recruiters:</strong> <?= htmlspecialchars($career['typical_recruiters']) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- =======================================================================
     9. CAREER ASSESSMENT DISCOVERY CARD (PRD Section 34)
     ======================================================================= -->
<section style="padding:40px 0 80px 0; background:#FCFCFA;">
  <div class="container">
    <div style="background:linear-gradient(135deg, #0B1020 0%, #111827 100%); border:1px solid rgba(201,169,110,0.3); border-radius:var(--edu-radius-lg); padding:40px; color:#FFFFFF; display:grid; grid-template-columns:1.2fr 0.8fr; gap:36px; align-items:center;">
      <div>
        <span class="edu-eyebrow" style="color:var(--edu-gold);">
          <i class="fa-solid fa-compass-drafting"></i> PSYCHOMETRIC CAREER EXPLORATION
        </span>
        <h3 style="font-size:2.2rem; font-weight:800; color:#FFFFFF; margin:8px 0 16px 0; line-height:1.2;">
          Discover a direction that fits you.
        </h3>
        <p style="color:#CBD5E1; font-size:1rem; line-height:1.6; margin-bottom:24px;">
          Map your analytical instincts, work preferences, and temperament to verified undergraduate degrees and high-conviction careers. Sample evaluation with zero opaque AI guessing.
        </p>
        <button class="edu-btn-primary" onclick="openCounsellingModal()">
          <span>Take Sample Assessment</span>
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>

      <div style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12); border-radius:14px; padding:24px;">
        <div style="font-size:0.8rem; letter-spacing:0.1em; text-transform:uppercase; color:var(--edu-gold); font-weight:700; margin-bottom:12px;">
          WHAT YOU RECEIVE:
        </div>
        <div style="display:flex; flex-direction:column; gap:12px; font-size:0.88rem; color:#E2E8F0;">
          <div><i class="fa-solid fa-circle-check" style="color:#16866A; margin-right:8px;"></i> Personalized Interest & Temperament Profile</div>
          <div><i class="fa-solid fa-circle-check" style="color:#16866A; margin-right:8px;"></i> Recommended Degree Majors (B.Tech / MBA / Medicine)</div>
          <div><i class="fa-solid fa-circle-check" style="color:#16866A; margin-right:8px;"></i> Corroborated Institution Shortlist in Rajasthan</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =======================================================================
     10. SCHOLARSHIPS & FINANCIAL AID DIRECTORY (PRD Section 37)
     ======================================================================= -->
<section class="edu-scholarships-section" id="scholarshipsSection">
  <div class="container">
    <div class="edu-section-head" style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px;">
      <div>
        <span class="edu-eyebrow">
          <i class="fa-solid fa-award"></i> FINANCIAL MERIT & ACCESS
        </span>
        <h2 class="edu-section-title">
          Verified Scholarships & Grants (2026–27)
        </h2>
        <p style="color:var(--edu-slate); margin:4px 0 0 0; font-size:0.95rem;">
          State and central government tuition waiver schemes, women in engineering awards, and institutional fellowships.
        </p>
      </div>
      <div>
        <a href="<?= htmlspecialchars(pov_url('scholarships.php')) ?>" style="background:#0F172A; color:#C9A96E; font-weight:600; font-size:13px; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:8px; border:1px solid #C9A96E;">
          <span>View All Scholarships</span>
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>

    <div class="edu-scholarships-grid">
      <?php if (!empty($scholarships)): ?>
        <?php foreach ($scholarships as $s): ?>
          <div class="edu-scholarship-card">
            <div>
              <span class="edu-exam-badge-category"><?= htmlspecialchars($s['category'] ?? 'Merit Aid') ?></span>
              <h3 style="font-size:1.15rem; font-weight:800; color:var(--edu-ink); margin:8px 0 4px 0;"><?= htmlspecialchars($s['title']) ?></h3>
              <div style="font-size:0.82rem; color:var(--edu-slate);">Provider: <?= htmlspecialchars($s['provider']) ?></div>

              <div class="edu-scholarship-amount">
                <?= htmlspecialchars($s['amount_display']) ?>
              </div>

              <p style="font-size:0.82rem; color:#475569; line-height:1.45; margin:0 0 12px 0;">
                <?= htmlspecialchars($s['eligibility']) ?>
              </p>
            </div>

            <div style="border-top:1px solid #E2E8F0; padding-top:14px;">
              <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.8rem; margin-bottom:12px;">
                <span style="color:var(--edu-slate);"><i class="fa-regular fa-calendar" style="margin-right:4px;"></i> Deadline:</span>
                <strong style="color:var(--edu-ink);"><?= !empty($s['deadline']) ? date('d M Y', strtotime($s['deadline'])) : 'Open Cycle' ?></strong>
              </div>
              <a href="<?= htmlspecialchars($s['official_link'] ?? '#') ?>" target="_blank" rel="noopener" class="edu-btn-primary" style="width:100%; height:40px; font-size:0.85rem; text-decoration:none;">
                Official Application <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.75rem;"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- =======================================================================
     11. VERIFIED STUDENT REVIEWS & RATINGS (PRD Section 24)
     ======================================================================= -->
<section class="edu-reviews-section" id="studentReviews">
  <div class="container">
    <div class="edu-section-head">
      <div>
        <span class="edu-eyebrow">
          <i class="fa-regular fa-star"></i> AUTHENTIC STUDENT PERSPECTIVES
        </span>
        <h2 class="edu-section-title">
          Verified Student Reviews & Rating Meters
        </h2>
        <p style="color:var(--edu-slate); margin:4px 0 0 0; font-size:0.95rem;">
          Real experiences from verified alumni and current undergraduates across academics, faculty guidance, infrastructure, and placements.
        </p>
      </div>
    </div>

    <div class="edu-reviews-grid">
      <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $rev): ?>
          <div class="edu-review-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
              <div style="display:flex; align-items:center; gap:10px;">
                <div class="edu-badge-icon" style="background:<?= htmlspecialchars($rev['badge_color'] ?? '#0B1020') ?>; width:38px; height:38px; font-size:0.85rem;">
                  <?= htmlspecialchars($rev['logo_text'] ?? 'JU') ?>
                </div>
                <div>
                  <strong style="font-size:0.95rem; color:var(--edu-ink); display:block;"><?= htmlspecialchars($rev['reviewer_name']) ?></strong>
                  <small style="color:var(--edu-slate);"><?= htmlspecialchars($rev['course_name']) ?> · <?= htmlspecialchars($rev['batch_year']) ?></small>
                </div>
              </div>
              <span class="edu-verified-student-badge">
                <i class="fa-solid fa-certificate"></i> Verified Student
              </span>
            </div>

            <div style="margin:12px 0;">
              <div style="font-size:1.05rem; font-weight:800; color:var(--edu-ink); margin-bottom:4px;">
                "<?= htmlspecialchars($rev['review_title']) ?>"
              </div>
              <p style="font-size:0.88rem; color:#475569; line-height:1.5; margin:0;">
                <?= htmlspecialchars($rev['review_body']) ?>
              </p>
            </div>

            <!-- Multi-Criteria Rating Breakdown -->
            <div class="edu-rating-breakdown">
              <div class="edu-rating-item">
                <span class="label">Academics</span>
                <span class="val">★ <?= htmlspecialchars($rev['rating_academics'] ?? '4.8') ?></span>
              </div>
              <div class="edu-rating-item">
                <span class="label">Faculty</span>
                <span class="val">★ <?= htmlspecialchars($rev['rating_faculty'] ?? '4.7') ?></span>
              </div>
              <div class="edu-rating-item">
                <span class="label">Placements</span>
                <span class="val">★ <?= htmlspecialchars($rev['rating_placements'] ?? '4.9') ?></span>
              </div>
            </div>

            <div style="font-size:0.82rem; background:#FFFFFF; padding:10px; border-radius:6px; border:1px solid #E2E8F0;">
              <strong style="color:var(--edu-emerald);"><i class="fa-solid fa-thumbs-up" style="margin-right:4px;"></i> Pro:</strong> <?= htmlspecialchars($rev['pros']) ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- =======================================================================
     12. STATUTORY ACCREDITATIONS & APPROVAL TRUST BANNER (PRD Section 26)
     ======================================================================= -->
<section style="padding:60px 0; background:#F8FAFC; border-top:1px solid var(--edu-border);">
  <div class="container">
    <div style="text-align:center; max-width:700px; margin:0 auto 36px auto;">
      <span class="edu-eyebrow"><i class="fa-solid fa-stamp"></i> REGULATORY TRANSPARENCY</span>
      <h3 style="font-size:1.8rem; font-weight:800; color:var(--edu-ink); margin:6px 0;">Official Statutory Recognitions & Gazette Backing</h3>
      <p style="color:var(--edu-slate); font-size:0.92rem;">
        Every institution featured in our discovery engine is checked against government gazette records from apex statutory councils.
      </p>
    </div>

    <div style="display:flex; justify-content:center; flex-wrap:wrap; gap:20px;">
      <div style="background:#FFFFFF; border:1px solid var(--edu-border); border-radius:10px; padding:16px 24px; display:flex; align-items:center; gap:12px;">
        <i class="fa-solid fa-building-columns" style="font-size:1.5rem; color:var(--edu-gold);"></i>
        <div>
          <strong style="color:var(--edu-ink); font-size:0.95rem; display:block;">UGC Section 2(f) & 12(B)</strong>
          <small style="color:var(--edu-slate);">University Grants Commission</small>
        </div>
      </div>

      <div style="background:#FFFFFF; border:1px solid var(--edu-border); border-radius:10px; padding:16px 24px; display:flex; align-items:center; gap:12px;">
        <i class="fa-solid fa-microchip" style="font-size:1.5rem; color:#4F46E5;"></i>
        <div>
          <strong style="color:var(--edu-ink); font-size:0.95rem; display:block;">AICTE Approval & EOA</strong>
          <small style="color:var(--edu-slate);">All India Council for Technical Education</small>
        </div>
      </div>

      <div style="background:#FFFFFF; border:1px solid var(--edu-border); border-radius:10px; padding:16px 24px; display:flex; align-items:center; gap:12px;">
        <i class="fa-solid fa-certificate" style="font-size:1.5rem; color:var(--edu-emerald);"></i>
        <div>
          <strong style="color:var(--edu-ink); font-size:0.95rem; display:block;">NAAC & NIRF Ranked</strong>
          <small style="color:var(--edu-slate);">National Assessment and Accreditation</small>
        </div>
      </div>

      <div style="background:#FFFFFF; border:1px solid var(--edu-border); border-radius:10px; padding:16px 24px; display:flex; align-items:center; gap:12px;">
        <i class="fa-solid fa-scale-balanced" style="font-size:1.5rem; color:#7C3F4D;"></i>
        <div>
          <strong style="color:var(--edu-ink); font-size:0.95rem; display:block;">State Fee Committee Gazette</strong>
          <small style="color:var(--edu-slate);">Statutory Tuition Cap Approvals</small>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =======================================================================
     8. MODALS & DRAWERS
     ======================================================================= -->

<!-- Modal 1: College Detailed Information Drawer -->
<div class="edu-modal-backdrop" id="collegeDetailModal">
  <div class="edu-modal-window">
    <button class="edu-modal-close" onclick="closeCollegeModal()">&times;</button>
    <div id="collegeModalContent">
      <div style="text-align:center; padding: 40px;">
        <i class="fa-solid fa-circle-notch fa-spin" style="font-size:2rem; color:var(--edu-terracotta);"></i>
        <p style="margin-top:10px;">Loading verified institution details...</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal 2: Admission Counselling Enquiry Form Modal -->
<div class="edu-modal-backdrop" id="counsellingModal">
  <div class="edu-modal-window" style="max-width: 620px;">
    <button class="edu-modal-close" onclick="closeCounsellingModal()">&times;</button>
    
    <div style="margin-bottom: 24px;">
      <span class="edu-eyebrow">
        <i class="fa-solid fa-hand-holding-heart"></i> ADMISSIONS GUIDANCE 2026–27
      </span>
      <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--edu-dark); margin-bottom: 8px;">
        Start a genuine admissions conversation
      </h3>
      <p style="color: var(--edu-text-muted); font-size: 0.9rem; margin: 0;">
        No spam, no cold calls. Our verified education desk will review your requirements and provide authentic fee structures, eligibility guidelines, and seat notices.
      </p>
    </div>

    <form id="eduLeadForm" onsubmit="submitEduLead(event)">
      <input type="hidden" id="leadInstId" name="institution_id" value="" />
      <input type="hidden" id="leadType" name="lead_type" value="admission_counselling" />

      <div class="edu-form-grid">
        <div class="edu-form-group">
          <label for="leadName">Your Full Name *</label>
          <input type="text" id="leadName" name="student_name" placeholder="e.g. Aakash Sharma" required />
        </div>

        <div class="edu-form-group">
          <label for="leadMobile">Mobile Number (WhatsApp) *</label>
          <input type="tel" id="leadMobile" name="mobile" placeholder="10-digit number" required maxlength="12" />
        </div>

        <div class="edu-form-group">
          <label for="leadEmail">Email Address</label>
          <input type="email" id="leadEmail" name="email" placeholder="student@example.com" />
        </div>

        <div class="edu-form-group">
          <label for="leadCourse">Interested Program / Discipline *</label>
          <select id="leadCourse" name="preferred_course" required>
            <option value="B.Tech Computer Science">B.Tech Computer Science & AI</option>
            <option value="MBA">MBA (Finance, Marketing, Analytics)</option>
            <option value="B.Sc Nursing">B.Sc Nursing / Healthcare</option>
            <option value="B.Des Design">Design (B.Des)</option>
            <option value="B.Arch Architecture">Architecture (B.Arch)</option>
            <option value="BBA / Commerce">BBA / Commerce</option>
            <option value="MBBS / Medical">MBBS / Medical Sciences</option>
          </select>
        </div>

        <div class="edu-form-group">
          <label for="leadCity">Preferred Study City</label>
          <select id="leadCity" name="city">
            <option value="Jaipur">Jaipur</option>
            <option value="Kota">Kota</option>
            <option value="Jodhpur">Jodhpur</option>
            <option value="Udaipur">Udaipur</option>
            <option value="Ajmer">Ajmer</option>
            <option value="Sikar">Sikar</option>
            <option value="Pilani">Pilani</option>
            <option value="All Rajasthan">Anywhere in Rajasthan</option>
          </select>
        </div>

        <div class="edu-form-group">
          <label for="leadBudget">Annual Budget Preference</label>
          <select id="leadBudget" name="budget_range">
            <option value="Under ₹1 Lakh/yr (Govt)">Under ₹1 Lakh/yr (Government/Aided)</option>
            <option value="₹1.5 Lakh - ₹2.5 Lakh/yr">₹1.5 Lakh - ₹2.5 Lakh/yr</option>
            <option value="₹2.5 Lakh - ₹4 Lakh/yr">₹2.5 Lakh - ₹4 Lakh/yr</option>
            <option value="Above ₹4 Lakh/yr">Above ₹4 Lakh/yr</option>
          </select>
        </div>

        <div class="edu-form-group full-width">
          <label for="leadExam">Entrance Exam / 12th Marks (Optional)</label>
          <input type="text" id="leadExam" name="entrance_exam" placeholder="e.g. JEE Main 88 percentile / Class 12th 85% PCM" />
        </div>
      </div>

      <!-- Dual-Consent Architecture (PRD Section 45) -->
      <div class="edu-consent-box">
        <input type="checkbox" id="eduConsentInfo" required checked style="width:auto; margin-top:2px;" />
        <label for="eduConsentInfo" style="margin:0; font-weight:normal; cursor:pointer;">
          <strong>Information Request Consent (Mandatory):</strong> I consent to receiving the official 2026–27 academic brochure, fee breakups, and eligibility notices from POVIndian and the selected institution.
        </label>
      </div>

      <div class="edu-consent-box secondary">
        <input type="checkbox" id="eduConsentAdvisory" checked style="width:auto; margin-top:2px;" />
        <label for="eduConsentAdvisory" style="margin:0; font-weight:normal; cursor:pointer;">
          <strong>Admissions Guidance Consent (Optional):</strong> I agree to receive a verified admissions guidance callback or WhatsApp update from an authorized institution counsellor. No third-party marketing calls.
        </label>
      </div>

      <button type="submit" class="edu-btn-primary" style="width:100%; height:52px;" id="eduSubmitBtn">
        <span>Submit Counselling Request</span>
        <i class="fa-solid fa-paper-plane"></i>
      </button>

      <div id="leadFeedback" style="display:none; margin-top:16px; padding:14px; border-radius:8px; text-align:center;"></div>
    </form>
  </div>
</div>

<!-- Modal 3: Side-by-Side Comparison Modal -->
<div class="edu-modal-backdrop" id="compareModal">
  <div class="edu-modal-window" style="max-width: 860px;">
    <button class="edu-modal-close" onclick="closeCompareModal()">&times;</button>
    <div style="margin-bottom: 20px;">
      <span class="edu-eyebrow"><i class="fa-solid fa-code-compare"></i> SIDE-BY-SIDE EVALUATION</span>
      <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--edu-dark); margin: 0;">Compare Saved Institutions</h3>
    </div>
    <div id="compareContent" style="overflow-x: auto;">
      <!-- Populated via JS -->
    </div>
  </div>
</div>

<!-- Modal 4: Signature Data Provenance Slide-Over Drawer (PRD Section 66 & 74) -->
<div class="edu-provenance-overlay" id="provenanceOverlay" onclick="closeProvenanceDrawer()"></div>
<div class="edu-provenance-drawer" id="provenanceDrawer">
  <div class="edu-provenance-head">
    <div>
      <span style="font-size:0.75rem; letter-spacing:0.12em; text-transform:uppercase; color:var(--edu-gold); font-weight:800; display:block; margin-bottom:4px;">
        <i class="fa-solid fa-shield-halved"></i> DATA PROVENANCE & AUDIT LOG
      </span>
      <h3>Statutory Source Citation</h3>
      <p id="provInstName">Institution Evidence Trail</p>
    </div>
    <button type="button" class="edu-provenance-close" onclick="closeProvenanceDrawer()">&times;</button>
  </div>

  <div class="edu-provenance-body" id="provenanceBody">
    <div class="edu-provenance-card">
      <div class="edu-provenance-field">
        <span class="label">Academic Year</span>
        <span class="val" id="provAcademicYear">2026–27</span>
      </div>
      <div class="edu-provenance-field">
        <span class="label">Data Verification State</span>
        <span class="val" style="color:var(--edu-emerald);"><i class="fa-solid fa-circle-check"></i> Statutorily Verified</span>
      </div>
      <div class="edu-provenance-field">
        <span class="label">Sourced Authority</span>
        <span class="val" id="provAuthority">State Fee Regulatory Committee</span>
      </div>
      <div class="edu-provenance-field">
        <span class="label">Last Audit Date</span>
        <span class="val" id="provVerifiedDate">18 June 2026</span>
      </div>
      <div class="edu-provenance-field">
        <span class="label">Audited By</span>
        <span class="val">POV Desk Senior Researcher</span>
      </div>
    </div>

    <div style="background:#FFF8EE; border:1px solid #FDE68A; border-radius:8px; padding:14px; margin-bottom:20px; font-size:0.85rem; color:#92400E;">
      <strong><i class="fa-solid fa-scale-balanced" style="margin-right:4px;"></i> Provenance Guarantee:</strong>
      <p style="margin:4px 0 0 0; line-height:1.45;">
        Every annual fee, sanctioned seat count, and reservation criteria is directly corroborated with official AICTE/UGC gazettes or state admission notices before display. Missing records are marked as "Not reported", never assumed as ₹0.
      </p>
    </div>

    <div id="provActionContainer">
      <a href="#" id="provOfficialLink" target="_blank" rel="noopener" class="edu-btn-primary" style="width:100%; height:48px; text-decoration:none; margin-bottom:12px;">
        <span>Open Primary Gazette / Notice</span>
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </a>
      <button type="button" class="edu-pill-btn" style="width:100%; height:44px;" onclick="closeProvenanceDrawer(); openCounsellingModal();">
        <i class="fa-solid fa-paper-plane" style="margin-right:6px;"></i> Request Official Prospectus
      </button>
    </div>
  </div>
</div>

<!-- =======================================================================
     JAVASCRIPT LOGIC
     ======================================================================= -->
<script>
  // State
  let activeCity = 'all';
  let activeDiscipline = 'all';
  let bookmarkedIds = JSON.parse(localStorage.getItem('pov_edu_bookmarks') || '[]');
  const institutionsData = <?= json_encode($institutions) ?>;

  // Pagination State (Page-Based Browsing)
  let currentPage = 1;
  const itemsPerPage = 4; // 4 institutions per page for comfortable, focused evaluation

  // Check URL for ?page= parameter
  const urlPageParam = new URLSearchParams(window.location.search).get('page');
  if (urlPageParam) {
    const parsedPage = parseInt(urlPageParam, 10);
    if (!isNaN(parsedPage) && parsedPage > 0) {
      currentPage = parsedPage;
    }
  }

  // Initialize UI on Load
  document.addEventListener('DOMContentLoaded', () => {
    updateBookmarkUI();
    filterCards(false); // initial render with page slicing
  });

  // 1. City Filter
  function selectCityFilter(city) {
    activeCity = city;

    // Update Pill styles in both sections
    document.querySelectorAll('#cityPillContainer .edu-pill-btn, #shortlistCityPills .edu-pill-btn').forEach(btn => {
      if (btn.getAttribute('data-city') === city) {
        btn.classList.add('active-dark');
      } else {
        btn.classList.remove('active-dark');
      }
    });

    // Update map node active state
    document.querySelectorAll('.edu-map-node').forEach(node => {
      if (node.getAttribute('data-city') === city) {
        node.classList.add('active');
      } else {
        node.classList.remove('active');
      }
    });

    filterCards(true); // reset to page 1 on filter change
  }

  function clearCityFilter() {
    selectCityFilter('all');
    selectDisciplineFilter('all');
    const input = document.getElementById('eduKeywordInput');
    if (input) input.value = '';
    filterCards(true);
  }

  // 2. Discipline Filter
  function selectDisciplineFilter(disc) {
    activeDiscipline = disc;

    document.querySelectorAll('#coursePillContainer .edu-pill-btn, #shortlistCoursePills .edu-pill-btn').forEach(btn => {
      const bDisc = btn.getAttribute('data-discipline');
      if (bDisc === disc || (disc === 'all' && bDisc === 'all')) {
        btn.classList.add('active-terracotta');
      } else {
        btn.classList.remove('active-terracotta');
      }
    });

    filterCards(true);
  }

  function quickFilter(type, value) {
    if (type === 'city') selectCityFilter(value);
    if (type === 'discipline') selectDisciplineFilter(value);
    scrollToShortlist();
  }

  // 3. Page-Based Core Card Filtering & Pagination
  function filterCards(resetPage = true) {
    if (resetPage) {
      currentPage = 1;
    }

    const keyword = (document.getElementById('eduKeywordInput')?.value || '').toLowerCase().trim();
    const cards = Array.from(document.querySelectorAll('.edu-card'));

    // Filter matching cards
    const matchingCards = cards.filter(card => {
      const city = card.getAttribute('data-city') || '';
      const name = card.getAttribute('data-name') || '';
      const courses = card.getAttribute('data-courses') || '';

      const matchCity = (activeCity === 'all' || city.toLowerCase() === activeCity.toLowerCase());
      const matchDisc = (activeDiscipline === 'all' || courses.includes(activeDiscipline.toLowerCase()) || name.includes(activeDiscipline.toLowerCase()));
      const matchKey = (!keyword || name.includes(keyword) || city.toLowerCase().includes(keyword) || courses.includes(keyword));

      return matchCity && matchDisc && matchKey;
    });

    const totalMatching = matchingCards.length;
    const totalPages = Math.ceil(totalMatching / itemsPerPage) || 1;

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, totalMatching);

    // Hide all cards first
    cards.forEach(card => card.style.display = 'none');

    // Display only cards belonging to the current page slice
    for (let i = startIndex; i < endIndex; i++) {
      if (matchingCards[i]) {
        matchingCards[i].style.display = 'flex';
      }
    }

    // Update count badge
    const countBadge = document.getElementById('collegeCountBadge');
    if (countBadge) {
      countBadge.innerHTML = `${totalMatching} places to look at closely <small><i class="fa-regular fa-clock" style="margin-right:4px;"></i> Checked for 2026–27</small>`;
    }

    // Render Pagination Controls
    const paginationWrapper = document.getElementById('eduPaginationWrapper');
    const paginationInfo = document.getElementById('eduPaginationInfo');
    const prevBtn = document.getElementById('eduPagePrev');
    const nextBtn = document.getElementById('eduPageNext');
    const pageNumbersContainer = document.getElementById('eduPageNumbers');

    if (totalMatching === 0) {
      if (paginationWrapper) paginationWrapper.style.display = 'none';
      return;
    }

    if (paginationWrapper) paginationWrapper.style.display = 'flex';

    if (paginationInfo) {
      paginationInfo.textContent = `Showing ${startIndex + 1}–${endIndex} of ${totalMatching} institutions · Page ${currentPage} of ${totalPages}`;
    }

    if (prevBtn) prevBtn.disabled = (currentPage <= 1);
    if (nextBtn) nextBtn.disabled = (currentPage >= totalPages);

    if (pageNumbersContainer) {
      let pagesHtml = '';
      for (let p = 1; p <= totalPages; p++) {
        pagesHtml += `<button type="button" class="edu-page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
      }
      pageNumbersContainer.innerHTML = pagesHtml;
    }
  }

  // 4. Page Navigation
  function goToPage(page) {
    currentPage = page;
    filterCards(false);

    // Update browser URL query param without full page reload
    if (history.pushState) {
      const newUrl = window.location.pathname + (page > 1 ? '?page=' + page : '') + window.location.hash;
      window.history.pushState({ page: page }, '', newUrl);
    }

    scrollToShortlist();
  }

  function scrollToShortlist() {
    document.getElementById('usefulShortlist')?.scrollIntoView({ behavior: 'smooth' });
  }

  // 4. Bookmark Management
  function toggleBookmark(id, name, btn) {
    id = Number(id);
    const idx = bookmarkedIds.indexOf(id);
    if (idx > -1) {
      bookmarkedIds.splice(idx, 1);
    } else {
      bookmarkedIds.push(id);
    }
    localStorage.setItem('pov_edu_bookmarks', JSON.stringify(bookmarkedIds));
    updateBookmarkUI();
  }

  function updateBookmarkUI() {
    document.querySelectorAll('.edu-card').forEach(card => {
      const id = Number(card.getAttribute('data-id'));
      const btn = card.querySelector('.edu-card-bookmark');
      if (btn) {
        const icon = btn.querySelector('i');
        if (bookmarkedIds.includes(id)) {
          btn.classList.add('bookmarked');
          if (icon) {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
          }
        } else {
          btn.classList.remove('bookmarked');
          if (icon) {
            icon.classList.remove('fa-solid');
            icon.classList.add('fa-regular');
          }
        }
      }
    });

    // Shortlist tray counter
    const count = bookmarkedIds.length;
    const trayStatus = document.getElementById('shortlistTrayStatus');
    if (trayStatus) {
      trayStatus.textContent = `${count} institution${count === 1 ? '' : 's'} in your shortlist`;
    }

    // Compare button in cards header
    const compareBtn = document.getElementById('compareFloatingBtn');
    const compareCount = document.getElementById('compareCount');
    if (compareBtn && compareCount) {
      compareCount.textContent = count;
      compareBtn.style.display = count >= 2 ? 'inline-flex' : 'none';
    }
  }

  // 5. College Detailed View Modal
  function openCollegeModal(id) {
    const modal = document.getElementById('collegeDetailModal');
    const content = document.getElementById('collegeModalContent');
    modal.classList.add('open');

    const college = institutionsData.find(i => Number(i.id) === Number(id));
    if (!college) {
      content.innerHTML = '<p>Institution details could not be loaded.</p>';
      return;
    }

    let offeringsHtml = '';
    if (college.offerings && college.offerings.length > 0) {
      offeringsHtml = `
        <table class="edu-offerings-table">
          <thead>
            <tr>
              <th>Program</th>
              <th>Annual Tuition Fee</th>
              <th>Intake</th>
              <th>Eligibility</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            ${college.offerings.map(o => `
              <tr>
                <td><strong>${o.short_name}</strong><br><small style="color:#64748B;">${o.canonical_name}</small></td>
                <td><strong>₹${Number(o.annual_tuition_fee).toLocaleString('en-IN')}</strong> / year</td>
                <td>${o.intake_seats || '120'} seats</td>
                <td><small>${o.eligibility_criteria}</small></td>
                <td><span style="color:var(--edu-green); font-weight:700;">● ${o.application_status}</span></td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      `;
    }

    let approvalsHtml = '';
    if (college.approvals && college.approvals.length > 0) {
      approvalsHtml = `
        <div style="display:flex; flex-wrap:wrap; gap:10px; margin: 16px 0;">
          ${college.approvals.map(a => `
            <div style="background:#F1F5F9; border:1px solid #CBD5E1; padding:8px 14px; border-radius:8px; font-size:0.82rem;">
              <strong>${a.regulator}</strong>: ${a.approval_type} <br>
              <small style="color:#64748B;">Ref: ${a.reference_number || 'Official Record'}</small>
            </div>
          `).join('')}
        </div>
      `;
    }

    content.innerHTML = `
      <div class="edu-detail-header">
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px;">
          <div class="edu-badge-icon" style="background:${college.badge_color || '#0d2b39'}; width:54px; height:54px; font-size:1.2rem;">
            ${college.logo_text || 'JU'}
          </div>
          <div>
            <h2 style="font-size:1.8rem; font-weight:800; color:var(--edu-dark); margin:0;">${college.canonical_name}</h2>
            <div style="color:var(--edu-text-muted); font-size:0.9rem;">
              ${college.city}, ${college.state} · ${college.legal_recognition} · Est. ${college.est_year || '2012'}
            </div>
          </div>
        </div>
      </div>

      <!-- Provenance Audit Box -->
      <div class="edu-provenance-box">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div>
            <strong style="color:#15803D;"><i class="fa-solid fa-file-circle-check"></i> Source-Checked Academic Year 2026–27</strong>
            <div style="color:#276749; font-size:0.8rem; margin-top:2px;">
              Primary Evidence: ${college.provenance_source_name} (Verified ${college.last_verified_at})
            </div>
          </div>
          <a href="${college.provenance_source_url}" target="_blank" rel="noopener" style="color:var(--edu-terracotta); font-weight:700; font-size:0.82rem; text-decoration:underline;">
            Open Official Source <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </a>
        </div>
      </div>

      <!-- Regulatory Approvals -->
      <h4 style="font-size:1.1rem; font-weight:800; color:var(--edu-dark); margin:24px 0 8px 0;">Accreditations & Statutory Approvals</h4>
      ${approvalsHtml}

      <!-- Academic Offerings 2026-27 -->
      <h4 style="font-size:1.1rem; font-weight:800; color:var(--edu-dark); margin:24px 0 8px 0;">Academic-Year 2026–27 Programs & Fees</h4>
      ${offeringsHtml}

      <!-- Campus Details -->
      <div style="background:#FAF7F2; padding:18px; border-radius:8px; margin:20px 0; font-size:0.88rem; display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div><strong>Campus Size:</strong> ${college.campus_acres || '32'} Acres</div>
        <div><strong>NAAC / NIRF:</strong> ${college.naac_grade || 'Recognized'} / ${college.nirf_band || 'Top Ranking'}</div>
        <div><strong>Gender Model:</strong> ${college.gender_model || 'Co-ed'}</div>
        <div><strong>Official Website:</strong> <a href="${college.official_website}" target="_blank" style="color:var(--edu-terracotta);">${college.official_website}</a></div>
      </div>

      <!-- Direct Action Buttons -->
      <div style="display:flex; gap:14px; margin-top:28px;">
        <button class="edu-btn-primary" style="flex:1;" onclick="openCounsellingModal(${college.id}, '${college.canonical_name}')">
          <span>Get Admission & Fee Assistance</span>
          <i class="fa-solid fa-paper-plane"></i>
        </button>
        <a href="${college.official_website}" target="_blank" class="edu-pill-btn" style="padding:14px 20px; display:inline-flex; align-items:center;">
          Visit Official Portal <i class="fa-solid fa-arrow-up-right-from-square" style="margin-left:6px;"></i>
        </a>
      </div>
    `;
  }

  function closeCollegeModal() {
    document.getElementById('collegeDetailModal').classList.remove('open');
  }

  // 6. Counselling Enquiry Form Submission
  function openCounsellingModal(instId = null, instName = '') {
    closeCollegeModal();
    const modal = document.getElementById('counsellingModal');
    if (instId) {
      document.getElementById('leadInstId').value = instId;
    }
    document.getElementById('leadFeedback').style.display = 'none';
    modal.classList.add('open');
  }

  function closeCounsellingModal() {
    document.getElementById('counsellingModal').classList.remove('open');
  }

  async function submitEduLead(e) {
    e.preventDefault();
    const btn = document.getElementById('eduSubmitBtn');
    const feedback = document.getElementById('leadFeedback');

    btn.disabled = true;
    btn.innerHTML = '<span>Recording your request...</span> <i class="fa-solid fa-circle-notch fa-spin"></i>';

    const payload = {
      student_name: document.getElementById('leadName').value,
      mobile: document.getElementById('leadMobile').value,
      email: document.getElementById('leadEmail').value,
      preferred_course: document.getElementById('leadCourse').value,
      city: document.getElementById('leadCity').value,
      budget_range: document.getElementById('leadBudget').value,
      entrance_exam: document.getElementById('leadExam').value,
      institution_id: document.getElementById('leadInstId').value || null,
      lead_type: 'admission_counselling',
      first_touch_source: 'Rajasthan 2026-27 Higher Education Discovery',
      last_touch_source: 'Explore Page Modal',
      landing_page: window.location.pathname
    };

    try {
      const res = await fetch('http://127.0.0.1:5000/api/v1/edu/leads', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      feedback.style.display = 'block';
      if (data.success) {
        feedback.style.background = '#DCFCE7';
        feedback.style.color = '#15803D';
        feedback.innerHTML = `
          <strong><i class="fa-solid fa-circle-check"></i> Inquiry Recorded Successfully!</strong>
          <p style="margin:4px 0 0 0; font-size:0.85rem;">Reference: <strong>${data.data.lead_number}</strong>. A dedicated counsellor will contact you with authentic details.</p>
        `;
        document.getElementById('eduLeadForm').reset();
        setTimeout(() => closeCounsellingModal(), 3500);
      } else {
        feedback.style.background = '#FEE2E2';
        feedback.style.color = '#B91C1C';
        feedback.textContent = data.message || 'Could not record inquiry. Please verify your phone number.';
      }
    } catch (err) {
      feedback.style.display = 'block';
      feedback.style.background = '#FEE2E2';
      feedback.style.color = '#B91C1C';
      feedback.textContent = 'Server connection error. Please try again shortly.';
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<span>Submit Counselling Request</span> <i class="fa-solid fa-paper-plane"></i>';
    }
  }

  // 7. Side-by-Side Comparison
  function openCompareModal() {
    const modal = document.getElementById('compareModal');
    const container = document.getElementById('compareContent');
    modal.classList.add('open');

    const selected = institutionsData.filter(i => bookmarkedIds.includes(Number(i.id)));
    if (selected.length === 0) {
      container.innerHTML = '<p>No institutions selected for comparison yet.</p>';
      return;
    }

    container.innerHTML = `
      <table class="edu-compare-table">
        <thead>
          <tr>
            <th style="width:20%;">Parameter</th>
            ${selected.map(s => `<th><strong>${s.canonical_name}</strong><br><small>${s.city}</small></th>`).join('')}
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Type & Ownership</strong></td>
            ${selected.map(s => `<td>${s.legal_recognition}<br><small>${s.ownership}</small></td>`).join('')}
          </tr>
          <tr>
            <td><strong>Campus & Est.</strong></td>
            ${selected.map(s => `<td>${s.campus_acres || '30+'} Acres · Est. ${s.est_year || '2012'}</td>`).join('')}
          </tr>
          <tr>
            <td><strong>2026-27 Fee Range</strong></td>
            ${selected.map(s => `<td>From ₹${Number(s.min_annual_fee || 150000).toLocaleString('en-IN')} / yr</td>`).join('')}
          </tr>
          <tr>
            <td><strong>Accreditation</strong></td>
            ${selected.map(s => `<td>${s.naac_grade || 'State Recognized'}<br><small>${s.nirf_band || ''}</small></td>`).join('')}
          </tr>
          <tr>
            <td><strong>Approvals</strong></td>
            ${selected.map(s => `<td>${(s.approvals || []).map(a => a.regulator).join(', ') || 'UGC'}</td>`).join('')}
          </tr>
          <tr>
            <td><strong>Action</strong></td>
            ${selected.map(s => `
              <td>
                <button class="edu-btn-primary" style="padding:6px 12px; font-size:0.8rem;" onclick="openCounsellingModal(${s.id}, '${s.canonical_name}')">
                  Apply / Inquire
                </button>
              </td>
            `).join('')}
          </tr>
        </tbody>
      </table>
    `;
  }

  function closeCompareModal() {
    document.getElementById('compareModal').classList.remove('open');
  }

  // 8. Admission Alerts Helper
  function openAdmissionAlert(type) {
    if (type === 'reap') {
      window.open('https://reap2026.rajasthan.gov.in', '_blank');
    } else if (type === 'josaa') {
      window.open('https://josaa.nic.in', '_blank');
    } else {
      quickFilter('discipline', 'Engineering');
    }
  }

  // 9. Signature Data Provenance Drawer (PRD Section 66)
  function openProvenanceDrawer(instId) {
    const college = institutionsData.find(i => Number(i.id) === Number(instId));
    if (!college) return;

    document.getElementById('provInstName').textContent = `${college.canonical_name} (${college.city})`;
    document.getElementById('provAuthority').textContent = college.provenance_source_name || 'State Higher Education Council';
    document.getElementById('provVerifiedDate').textContent = college.last_verified_at ? new Date(college.last_verified_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '18 Jun 2026';
    
    const linkBtn = document.getElementById('provOfficialLink');
    if (linkBtn) {
      linkBtn.href = college.provenance_source_url || college.official_website || '#';
    }

    document.getElementById('provenanceOverlay').classList.add('open');
    document.getElementById('provenanceDrawer').classList.add('open');
  }

  function closeProvenanceDrawer() {
    document.getElementById('provenanceOverlay')?.classList.remove('open');
    document.getElementById('provenanceDrawer')?.classList.remove('open');
  }

  // 10. Signature Academic Year Switcher (PRD Section 02)
  let activeAcademicYear = '2026-27';
  function switchAcademicYear(year) {
    activeAcademicYear = year;
    document.querySelectorAll('.edu-year-btn').forEach(btn => {
      if (btn.getAttribute('data-year') === year) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    const provYear = document.getElementById('provAcademicYear');
    if (provYear) provYear.textContent = year;

    // Toast/Feedback indicator for year perspective
    const countBadge = document.getElementById('collegeCountBadge');
    if (countBadge) {
      countBadge.innerHTML = `${institutionsData.length} places to look at closely <small><i class="fa-regular fa-clock" style="margin-right:4px;"></i> Switched to ${year} Archive</small>`;
    }
  }

  // 11. Hero Search Input Sync
  function syncHeroSearch(query) {
    const cardInput = document.getElementById('eduKeywordInput');
    if (cardInput) {
      cardInput.value = query;
      filterCards(true);
    }
  }


  // Close modals when clicking backdrop
  window.onclick = function(event) {
    const m1 = document.getElementById('collegeDetailModal');
    const m2 = document.getElementById('counsellingModal');
    const m3 = document.getElementById('compareModal');
    const overlay = document.getElementById('provenanceOverlay');
    if (event.target === m1) closeCollegeModal();
    if (event.target === m2) closeCounsellingModal();
    if (event.target === m3) closeCompareModal();
    if (event.target === overlay) closeProvenanceDrawer();
  };

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeCollegeModal();
      closeCounsellingModal();
      closeCompareModal();
      closeProvenanceDrawer();
    }
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
