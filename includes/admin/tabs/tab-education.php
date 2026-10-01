<?php
/**
 * Admin Tab: Find the Right College — Higher Education & Admissions Master Suite
 * PRD 2026-27 Verified Education Graph Platform
 */

$allEduOfferings = [];
if (!empty($eduInstitutions)) {
    foreach ($eduInstitutions as $inst) {
        if (!empty($inst['offerings'])) {
            foreach ($inst['offerings'] as $off) {
                $off['institution_name'] = $inst['canonical_name'];
                $off['institution_city'] = $inst['city'];
                $off['institution_code'] = $inst['short_code'] ?? ($inst['logo_text'] ?? 'COL');
                $allEduOfferings[] = $off;
            }
        }
    }
}
?>
<section class="tab-section <?= $activeTab === 'education' ? 'active' : '' ?>" id="section-education">
  
  <!-- Section Title & Action Bar -->
  <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid #E2E8F0;">
    <div>
      <h1 style="font-size:1.6rem; font-weight:800; color:#0F172A; display:flex; align-items:center; gap:10px;">
        <span style="width:38px; height:38px; border-radius:10px; background:#FFF3E8; color:#D97746; display:grid; place-items:center; font-size:18px;">
          <i class="fa-solid fa-graduation-cap"></i>
        </span>
        Find the Right College. — Master Education Backend
      </h1>
      <p style="color:#64748B; font-size:0.92rem; margin-top:4px;">
        Manage verified Indian universities, 2026–27 course offerings & fees, student inquiries, entrance exams, and scholarship registries.
      </p>
    </div>
    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
      <button type="button" class="btn-topbar btn-topbar-primary" style="background:#D97746; border:none; padding:9px 16px; border-radius:8px; font-weight:700; color:#FFF; display:inline-flex; align-items:center; gap:8px; cursor:pointer;" onclick="openModal('addEduInstitutionModal')">
        <i class="fa-solid fa-plus-circle"></i> Add New College
      </button>
      <button type="button" class="btn-topbar btn-topbar-secondary" style="background:#F1F5F9; border:1px solid #CBD5E1; padding:9px 14px; border-radius:8px; font-weight:700; color:#334155; display:inline-flex; align-items:center; gap:8px; cursor:pointer;" onclick="openModal('addEduOfferingModal')">
        <i class="fa-solid fa-book-open"></i> Add Course Offering
      </button>
      <button type="button" class="btn-topbar btn-topbar-secondary" style="background:#F1F5F9; border:1px solid #CBD5E1; padding:9px 14px; border-radius:8px; font-weight:700; color:#334155; display:inline-flex; align-items:center; gap:8px; cursor:pointer;" onclick="openModal('addEduExamModal')">
        <i class="fa-solid fa-calendar-plus"></i> Add Exam
      </button>
      <button type="button" class="btn-topbar btn-topbar-secondary" style="background:#F1F5F9; border:1px solid #CBD5E1; padding:9px 14px; border-radius:8px; font-weight:700; color:#334155; display:inline-flex; align-items:center; gap:8px; cursor:pointer;" onclick="openModal('addEduScholarshipModal')">
        <i class="fa-solid fa-award"></i> Add Scholarship
      </button>
      <a href="<?= htmlspecialchars(pov_url('education.php')) ?>" target="_blank" style="background:#FFF; color:#D97746; border:1px solid #D97746; padding:9px 14px; border-radius:8px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Live Page
      </a>
    </div>
  </div>

  <!-- 1. KPI Top Summary Cards -->
  <div class="metrics-grid" style="grid-template-columns: repeat(5, 1fr); margin-bottom: 24px;">
    <div class="metric-card">
      <div class="metric-header">
        <span class="metric-label">Colleges & Universities</span>
        <div class="metric-icon" style="background:#FFF3E8; color:#D97746;">
          <i class="fa-solid fa-building-columns"></i>
        </div>
      </div>
      <div class="metric-value"><?= count($eduInstitutions ?? []) ?></div>
      <div class="metric-change positive">
        <i class="fa-solid fa-shield-halved"></i> 100% AISHE / UGC Verified
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-header">
        <span class="metric-label">2026–27 Programs</span>
        <div class="metric-icon" style="background:#EBF5F0; color:#237A57;">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
      </div>
      <div class="metric-value"><?= count($allEduOfferings) ?></div>
      <div class="metric-change positive">
        <i class="fa-solid fa-calendar-check"></i> Live Seat Intake
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-header">
        <span class="metric-label">Student Inquiries</span>
        <div class="metric-icon" style="background:#EFF6FF; color:#2563EB;">
          <i class="fa-solid fa-users-line"></i>
        </div>
      </div>
      <div class="metric-value"><?= count($eduLeads ?? []) ?></div>
      <div class="metric-change positive">
        <i class="fa-solid fa-user-plus"></i> Verified Lead Ingestion
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-header">
        <span class="metric-label">Entrance Exams (2026)</span>
        <div class="metric-icon" style="background:#FEF3C7; color:#D97706;">
          <i class="fa-solid fa-clipboard-list"></i>
        </div>
      </div>
      <div class="metric-value"><?= count($eduExams ?? []) ?></div>
      <div class="metric-change positive">
        <i class="fa-solid fa-bell"></i> NTA / State Live Hub
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-header">
        <span class="metric-label">Scholarships Tracked</span>
        <div class="metric-icon" style="background:#F5F3FF; color:#7C3AED;">
          <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
      </div>
      <div class="metric-value"><?= count($eduScholarships ?? []) ?></div>
      <div class="metric-change positive">
        <i class="fa-solid fa-gift"></i> Merit & Means Registry
      </div>
    </div>
  </div>

  <!-- Subtabs Navigation Header -->
  <div style="display:flex; gap:10px; margin-bottom:20px; border-bottom:1px solid #E2E8F0; padding-bottom:12px; flex-wrap:wrap;">
    <button type="button" class="btn-primary" id="btnSubtabEduInst" onclick="switchEduSubtab('institutions')" style="background:#D97746; color:#FFF; border:none; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
      <i class="fa-solid fa-building-columns" style="margin-right:6px;"></i> Colleges & Universities (<?= count($eduInstitutions ?? []) ?>)
    </button>
    <button type="button" class="btn-secondary" id="btnSubtabEduOfferings" onclick="switchEduSubtab('offerings')" style="background:#F1F5F9; color:#475569; border:1px solid #CBD5E1; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
      <i class="fa-solid fa-book" style="margin-right:6px;"></i> Course Offerings & Fees (<?= count($allEduOfferings) ?>)
    </button>
    <button type="button" class="btn-secondary" id="btnSubtabEduLeads" onclick="switchEduSubtab('leads')" style="background:#F1F5F9; color:#475569; border:1px solid #CBD5E1; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
      <i class="fa-solid fa-user-graduate" style="margin-right:6px;"></i> Student Admission Inquiries (<?= count($eduLeads ?? []) ?>)
    </button>
    <button type="button" class="btn-secondary" id="btnSubtabEduExams" onclick="switchEduSubtab('exams')" style="background:#F1F5F9; color:#475569; border:1px solid #CBD5E1; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
      <i class="fa-solid fa-clipboard-list" style="margin-right:6px;"></i> Entrance Exams (<?= count($eduExams ?? []) ?>)
    </button>
    <button type="button" class="btn-secondary" id="btnSubtabEduScholarships" onclick="switchEduSubtab('scholarships')" style="background:#F1F5F9; color:#475569; border:1px solid #CBD5E1; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
      <i class="fa-solid fa-award" style="margin-right:6px;"></i> Scholarships (<?= count($eduScholarships ?? []) ?>)
    </button>
    <button type="button" class="btn-secondary" id="btnSubtabEduApprovals" onclick="switchEduSubtab('approvals')" style="background:#F1F5F9; color:#475569; border:1px solid #CBD5E1; padding:8px 16px; border-radius:8px; font-weight:700; cursor:pointer;">
      <i class="fa-solid fa-certificate" style="margin-right:6px;"></i> Approvals Tracker
    </button>
  </div>

  <!-- =======================================================================
       Panel 1: Verified Colleges & Universities Directory (CRUD)
       ======================================================================= -->
  <div class="card-panel" id="eduSubtabInstitutions">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-building-columns" style="color:#D97746;"></i> Higher Education Colleges & Universities Directory
      </h2>
      <div class="card-panel-actions" style="display:flex; gap:10px;">
        <input type="text" class="search-filter-input" placeholder="Search by name, city, NAAC, type..." onkeyup="filterTable(this, 'table-edu-inst')" />
        <button type="button" class="btn-topbar btn-topbar-primary" style="background:#D97746; color:#FFF; border:none; padding:6px 14px; border-radius:6px; font-weight:700; cursor:pointer; font-size:13px;" onclick="openModal('addEduInstitutionModal')">
          <i class="fa-solid fa-plus"></i> Add College
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-edu-inst">
        <thead>
          <tr>
            <th>Code</th>
            <th>College Name</th>
            <th>Type & Ownership</th>
            <th>Location</th>
            <th>NAAC / NIRF</th>
            <th>2026–27 Status</th>
            <th>Offerings</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($eduInstitutions)): ?>
            <tr>
              <td colspan="8" style="text-align:center; padding:30px; color:#64748B;">
                No colleges found in database. Click <strong>Add College</strong> above to create the first one.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($eduInstitutions as $inst): ?>
              <tr>
                <td>
                  <span class="chip chip-slate" style="font-weight:800; background:<?= htmlspecialchars($inst['badge_color'] ?? '#0D2B39') ?>; color:#FFF; font-size:12px;">
                    <?= htmlspecialchars($inst['logo_text'] ?? ($inst['short_code'] ?? 'COL')) ?>
                  </span>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--text-dark); font-size:14px;"><?= htmlspecialchars($inst['canonical_name']) ?></div>
                  <div style="font-size:11.5px; color:#64748B;">
                    <?= htmlspecialchars($inst['group_name'] ?? 'Autonomous') ?> • Est. <?= htmlspecialchars((string)($inst['est_year'] ?? 2012)) ?>
                  </div>
                </td>
                <td style="font-size:12.5px;">
                  <div><strong><?= htmlspecialchars($inst['institution_type']) ?></strong></div>
                  <div style="color:#64748B;"><?= htmlspecialchars($inst['ownership']) ?></div>
                </td>
                <td style="font-size:12.5px;">
                  <div><i class="fa-solid fa-location-dot" style="color:#D97746; font-size:11px;"></i> <?= htmlspecialchars($inst['city']) ?>, <?= htmlspecialchars($inst['state']) ?></div>
                  <div style="color:#64748B; font-size:11.5px;"><?= htmlspecialchars($inst['locality']) ?></div>
                </td>
                <td>
                  <span class="chip chip-success" style="font-size:11px;"><?= htmlspecialchars($inst['naac_grade'] ?? 'Recognized') ?></span>
                  <?php if (!empty($inst['nirf_band'])): ?>
                    <div style="font-size:11px; color:#64748B; margin-top:2px;">NIRF: <?= htmlspecialchars($inst['nirf_band']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <span style="color:#15803D; font-weight:700; font-size:12px;">
                    ● <?= htmlspecialchars($inst['primary_status'] ?? 'Applications Open') ?>
                  </span>
                  <div style="font-size:11px; color:#64748B;">AY 2026–27 Live</div>
                </td>
                <td>
                  <button type="button" class="chip chip-primary" style="cursor:pointer; background:#FFF3E8; color:#D97746; border:1px solid #D97746; font-weight:700;" onclick="openAddOfferingForInst(<?= (int)$inst['id'] ?>, '<?= htmlspecialchars(addslashes($inst['canonical_name'])) ?>')">
                    + Add Course (<?= count($inst['offerings'] ?? []) ?>)
                  </button>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:6px;">
                    <!-- Edit Button -->
                    <button type="button" class="action-btn" title="Edit College Details" onclick='openEditEduModal(<?= json_encode($inst, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                      <i class="fa-solid fa-pen-to-square" style="color:#2563EB;"></i>
                    </button>
                    <!-- Delete Button Form -->
                    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" onsubmit="return confirm('Are you sure you want to delete \'<?= htmlspecialchars(addslashes($inst['canonical_name'])) ?>\'? This will also remove all its course offerings.');" style="display:inline;">
                      <input type="hidden" name="admin_action" value="delete_edu_institution" />
                      <input type="hidden" name="id" value="<?= (int)$inst['id'] ?>" />
                      <button type="submit" class="action-btn" title="Delete College" style="border:none; background:none; cursor:pointer;">
                        <i class="fa-solid fa-trash" style="color:#EF4444;"></i>
                      </button>
                    </form>
                    <!-- Public Link -->
                    <a href="<?= htmlspecialchars(pov_url('education.php')) ?>#collegeDirectorySection" target="_blank" class="action-btn" title="View on Live Website">
                      <i class="fa-solid fa-arrow-up-right-from-square" style="color:#64748B;"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- =======================================================================
       Panel 2: Course Offerings & Fees Matrix (CRUD)
       ======================================================================= -->
  <div class="card-panel" id="eduSubtabOfferings" style="display:none;">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-book-open" style="color:#D97746;"></i> Course Offerings, Fees & Eligibility Matrix (<?= count($allEduOfferings) ?>)
      </h2>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search courses, degree, or college..." onkeyup="filterTable(this, 'table-edu-offerings')" />
        <button type="button" class="btn-topbar btn-topbar-primary" style="background:#D97746; color:#FFF; border:none; padding:6px 14px; border-radius:6px; font-weight:700; cursor:pointer; font-size:13px;" onclick="openModal('addEduOfferingModal')">
          <i class="fa-solid fa-plus"></i> Add Course Offering
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-edu-offerings">
        <thead>
          <tr>
            <th>College</th>
            <th>Program / Course Name</th>
            <th>Degree & Level</th>
            <th>Duration</th>
            <th>Annual Tuition Fee</th>
            <th>Intake Seats</th>
            <th>Entrance / Eligibility</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($allEduOfferings)): ?>
            <tr>
              <td colspan="8" style="text-align:center; padding:30px; color:#64748B;">
                No course offerings added yet. Click <strong>Add Course Offering</strong> to link courses to colleges.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($allEduOfferings as $off): ?>
              <tr>
                <td>
                  <div style="font-weight:700; color:#0F172A;"><?= htmlspecialchars($off['institution_name']) ?></div>
                  <div style="font-size:11.5px; color:#64748B;"><?= htmlspecialchars($off['institution_city']) ?> • AY <?= htmlspecialchars($off['academic_year'] ?? '2026-27') ?></div>
                </td>
                <td style="font-weight:600; color:#D97746;">
                  <?= htmlspecialchars($off['canonical_name'] ?? ($off['short_name'] ?? 'B.Tech')) ?>
                </td>
                <td>
                  <span class="chip chip-primary" style="font-size:11px;"><?= htmlspecialchars($off['degree_type'] ?? 'Degree') ?></span>
                  <div style="font-size:11px; color:#64748B; margin-top:2px;"><?= htmlspecialchars($off['academic_level'] ?? 'UG') ?></div>
                </td>
                <td style="font-size:12.5px;">
                  <?= htmlspecialchars((string)($off['duration_years'] ?? 4)) ?> Years
                </td>
                <td style="font-weight:700; color:#15803D; font-size:13px;">
                  ₹<?= number_format((float)($off['annual_tuition_fee'] ?? 150000)) ?> / yr
                </td>
                <td>
                  <span class="chip chip-slate" style="font-size:11px;"><?= htmlspecialchars((string)($off['intake_seats'] ?? 60)) ?> Seats</span>
                </td>
                <td style="font-size:11.5px; max-width:240px; color:#475569;">
                  <div><strong>Exam:</strong> <?= htmlspecialchars($off['entrance_exams'] ?? 'JEE Main') ?></div>
                  <div style="color:#64748B;"><?= htmlspecialchars(substr($off['eligibility_criteria'] ?? '', 0, 45)) ?>...</div>
                </td>
                <td>
                  <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" onsubmit="return confirm('Delete this course offering?');" style="display:inline;">
                    <input type="hidden" name="admin_action" value="delete_edu_offering" />
                    <input type="hidden" name="offering_id" value="<?= (int)($off['id'] ?? 0) ?>" />
                    <button type="submit" class="action-btn" title="Delete Course Offering" style="border:none; background:none; cursor:pointer;">
                      <i class="fa-solid fa-trash" style="color:#EF4444;"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- =======================================================================
       Panel 3: Student Admission Inquiries & Leads Pipeline
       ======================================================================= -->
  <div class="card-panel" id="eduSubtabLeads" style="display:none;">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-user-graduate" style="color:#D97746;"></i> Student Admission Inquiries & Counselling Pipeline (<?= count($eduLeads ?? []) ?>)
      </h2>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search by student, phone, or course..." onkeyup="filterTable(this, 'table-edu-leads')" />
      </div>
    </div>
    <div class="table-container">
      <?php if (empty($eduLeads)): ?>
        <div class="empty-state-box">
          <i class="fa-regular fa-folder-open"></i>
          <p>No student admission leads ingested yet.</p>
        </div>
      <?php else: ?>
        <table class="data-table" id="table-edu-leads">
          <thead>
            <tr>
              <th>Lead Ref</th>
              <th>Student Details</th>
              <th>Preferred Program</th>
              <th>City & Budget</th>
              <th>Score / Exam</th>
              <th>Pipeline Stage</th>
              <th>Attribution</th>
              <th>Counsellor & Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($eduLeads as $lead): ?>
              <tr>
                <td style="font-weight:700; font-family:monospace; color:#D97746;">
                  <?= htmlspecialchars($lead['lead_number']) ?>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--text-dark);"><?= htmlspecialchars($lead['student_name']) ?></div>
                  <div style="font-size:12px; color:var(--text-muted);">
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $lead['mobile']) ?>" target="_blank" style="text-decoration:none; color:#25D366; font-weight:700;">
                      <i class="fa-brands fa-whatsapp"></i> <?= htmlspecialchars($lead['mobile']) ?>
                    </a>
                  </div>
                  <?php if (!empty($lead['email'])): ?>
                    <div style="font-size:12px; color:var(--text-muted);"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($lead['email']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div style="font-weight:600;"><?= htmlspecialchars($lead['preferred_course']) ?></div>
                  <span class="chip chip-primary" style="font-size:11px; padding:2px 8px; background:#FBEFE8; color:#D97746; border:1px solid #D97746;">
                    AY <?= htmlspecialchars($lead['academic_year'] ?? '2026-27') ?>
                  </span>
                </td>
                <td>
                  <div><i class="fa-solid fa-location-dot" style="font-size:11px; color:#64748B;"></i> <?= htmlspecialchars($lead['city'] ?? 'Jaipur') ?></div>
                  <div style="font-size:12px; color:#64748B;"><?= htmlspecialchars($lead['budget_range'] ?? 'Flexible') ?></div>
                </td>
                <td style="font-size:12px;">
                  <?= htmlspecialchars($lead['entrance_exam'] ?? 'Not specified') ?>
                </td>
                <td>
                  <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" style="display:inline-block;">
                    <input type="hidden" name="admin_action" value="update_edu_lead_stage" />
                    <input type="hidden" name="lead_id" value="<?= (int)$lead['id'] ?>" />
                    <select name="lead_stage" onchange="this.form.submit()" style="padding:4px 8px; border-radius:6px; font-size:12px; font-weight:700; border:1px solid #CBD5E1; background:#FFF; cursor:pointer;">
                      <?php foreach (['New', 'Contacted', 'Interested', 'Counselling', 'Application Started', 'Admitted', 'Closed'] as $stg): ?>
                        <option value="<?= $stg ?>" <?= ($lead['lead_stage'] ?? '') === $stg ? 'selected' : '' ?>><?= $stg ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </td>
                <td style="font-size:11.5px; color:#64748B; max-width:180px;">
                  <div><strong>Touch:</strong> <?= htmlspecialchars($lead['first_touch_source'] ?? 'Direct') ?></div>
                  <div><strong>Last:</strong> <?= htmlspecialchars($lead['last_touch_source'] ?? 'Modal') ?></div>
                </td>
                <td style="font-size:12px;">
                  <div style="color:var(--text-dark); font-weight:600;"><?= htmlspecialchars($lead['counsellor_assigned'] ?? 'POV Education Desk') ?></div>
                  <small style="color:#64748B;"><?= htmlspecialchars(substr($lead['counsellor_notes'] ?? 'Pending reach out', 0, 40)) ?>...</small>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- =======================================================================
       Panel 4: Entrance Exams Hub (CRUD)
       ======================================================================= -->
  <div class="card-panel" id="eduSubtabExams" style="display:none;">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-clipboard-list" style="color:#D97746;"></i> National & State Entrance Exams Hub (2026)
      </h2>
      <div class="card-panel-actions">
        <button type="button" class="btn-topbar btn-topbar-primary" style="background:#D97746; color:#FFF; border:none; padding:6px 14px; border-radius:6px; font-weight:700; cursor:pointer; font-size:13px;" onclick="openModal('addEduExamModal')">
          <i class="fa-solid fa-plus"></i> Add Entrance Exam
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-edu-exams">
        <thead>
          <tr>
            <th>Code</th>
            <th>Exam Name</th>
            <th>Category</th>
            <th>Conducting Body</th>
            <th>Exam Date</th>
            <th>Application End</th>
            <th>Fee</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($eduExams)): ?>
            <tr><td colspan="8" style="text-align:center; padding:30px; color:#64748B;">No exams registered yet. Click <strong>Add Entrance Exam</strong>.</td></tr>
          <?php else: ?>
            <?php foreach ($eduExams as $exam): ?>
              <tr>
                <td>
                  <span class="chip chip-slate" style="font-weight:800; background:#0B1020; color:#FFF; font-size:12px;">
                    <?= htmlspecialchars($exam['short_code']) ?>
                  </span>
                </td>
                <td style="font-weight:700; color:#0F172A;"><?= htmlspecialchars($exam['exam_name']) ?></td>
                <td><span class="chip chip-primary" style="font-size:11px;"><?= htmlspecialchars($exam['category'] ?? 'Engineering') ?></span></td>
                <td style="font-size:12.5px;"><?= htmlspecialchars($exam['conducting_body'] ?? 'NTA') ?></td>
                <td style="font-weight:700; color:#15803D;"><?= htmlspecialchars($exam['exam_date'] ?? 'TBA') ?></td>
                <td style="font-size:12px; color:#EF4444;"><?= htmlspecialchars($exam['application_end'] ?? 'TBA') ?></td>
                <td style="font-size:12px;"><?= htmlspecialchars($exam['application_fee'] ?? '₹1,000') ?></td>
                <td>
                  <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" onsubmit="return confirm('Delete this entrance exam?');" style="display:inline;">
                    <input type="hidden" name="admin_action" value="delete_edu_exam" />
                    <input type="hidden" name="exam_id" value="<?= (int)($exam['id'] ?? 0) ?>" />
                    <button type="submit" class="action-btn" title="Delete Exam" style="border:none; background:none; cursor:pointer;">
                      <i class="fa-solid fa-trash" style="color:#EF4444;"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- =======================================================================
       Panel 5: Scholarships Registry (CRUD)
       ======================================================================= -->
  <div class="card-panel" id="eduSubtabScholarships" style="display:none;">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-award" style="color:#D97746;"></i> National & State Scholarships Registry
      </h2>
      <div class="card-panel-actions">
        <button type="button" class="btn-topbar btn-topbar-primary" style="background:#D97746; color:#FFF; border:none; padding:6px 14px; border-radius:6px; font-weight:700; cursor:pointer; font-size:13px;" onclick="openModal('addEduScholarshipModal')">
          <i class="fa-solid fa-plus"></i> Add Scholarship
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-edu-scholarships">
        <thead>
          <tr>
            <th>Scholarship Title</th>
            <th>Provider / Authority</th>
            <th>Category</th>
            <th>Amount Display</th>
            <th>Applicable Course</th>
            <th>Deadline</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($eduScholarships)): ?>
            <tr><td colspan="7" style="text-align:center; padding:30px; color:#64748B;">No scholarships registered yet. Click <strong>Add Scholarship</strong>.</td></tr>
          <?php else: ?>
            <?php foreach ($eduScholarships as $sch): ?>
              <tr>
                <td style="font-weight:700; color:#0F172A;"><?= htmlspecialchars($sch['title']) ?></td>
                <td style="font-size:12.5px;"><?= htmlspecialchars($sch['provider'] ?? 'Government') ?></td>
                <td><span class="chip chip-success" style="font-size:11px;"><?= htmlspecialchars($sch['category'] ?? 'Merit') ?></span></td>
                <td style="font-weight:700; color:#15803D;"><?= htmlspecialchars($sch['amount_display'] ?? 'Up to ₹1,00,000') ?></td>
                <td style="font-size:12px;"><?= htmlspecialchars($sch['applicable_course'] ?? 'B.Tech / Degree') ?></td>
                <td style="font-size:12px; color:#EF4444; font-weight:600;"><?= htmlspecialchars($sch['deadline'] ?? 'TBA') ?></td>
                <td>
                  <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>" onsubmit="return confirm('Delete this scholarship?');" style="display:inline;">
                    <input type="hidden" name="admin_action" value="delete_edu_scholarship" />
                    <input type="hidden" name="scholarship_id" value="<?= (int)($sch['id'] ?? 0) ?>" />
                    <button type="submit" class="action-btn" title="Delete Scholarship" style="border:none; background:none; cursor:pointer;">
                      <i class="fa-solid fa-trash" style="color:#EF4444;"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- =======================================================================
       Panel 6: Regulatory Approvals Tracker
       ======================================================================= -->
  <div class="card-panel" id="eduSubtabApprovals" style="display:none;">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-certificate" style="color:#D97746;"></i> Regulatory Approvals & Accreditation Registry (UGC, AICTE, INC, PCI)
      </h2>
    </div>
    <div style="padding:20px;">
      <p style="color:#64748B; font-size:0.9rem; margin-bottom:16px;">
        In accordance with PRD Section 4 (Data Trust & Provenance), every regulatory approval record is independently verified against official Gazette / Central Portal records.
      </p>
      <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px;">
        <div style="background:#FAF7F2; border:1px solid #E8E2D7; border-radius:10px; padding:18px;">
          <div style="font-weight:800; font-size:1.1rem; color:#0D2B39; margin-bottom:4px;">UGC Section 2(f) & 12(B)</div>
          <div style="font-size:0.85rem; color:#64748B; margin-bottom:10px;">University Grants Commission Statues</div>
          <span class="chip chip-success" style="font-size:11px;">Verified Gazette 2026-27</span>
        </div>
        <div style="background:#FAF7F2; border:1px solid #E8E2D7; border-radius:10px; padding:18px;">
          <div style="font-weight:800; font-size:1.1rem; color:#0D2B39; margin-bottom:4px;">AICTE EOA 2026–27</div>
          <div style="font-size:0.85rem; color:#64748B; margin-bottom:10px;">Extension of Approval for Engineering & MBA</div>
          <span class="chip chip-success" style="font-size:11px;">Central Dashboard Sync</span>
        </div>
        <div style="background:#FAF7F2; border:1px solid #E8E2D7; border-radius:10px; padding:18px;">
          <div style="font-weight:800; font-size:1.1rem; color:#0D2B39; margin-bottom:4px;">JoSAA / CSAB 2026</div>
          <div style="font-size:0.85rem; color:#64748B; margin-bottom:10px;">Central Seat Allocation for INI (MNIT & IIIT Kota)</div>
          <span class="chip chip-success" style="font-size:11px;">Seat Matrix Verified</span>
        </div>
      </div>
    </div>
  </div>

</section>

<!-- =======================================================================
     MODAL 1: ADD NEW COLLEGE / INSTITUTION
     ======================================================================= -->
<div class="modal-backdrop" id="addEduInstitutionModal" onclick="handleBackdropClick(event, 'addEduInstitutionModal')">
  <div class="modal-window" style="max-width:750px;">
    <div class="modal-header">
      <h3 class="modal-title" style="color:#D97746;">
        <i class="fa-solid fa-building-columns"></i> Add College / University (Find the Right College)
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addEduInstitutionModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_edu_institution" />
      <div class="modal-body">
        
        <div class="form-row">
          <div class="form-group" style="flex:2;">
            <label class="form-label">College / University Canonical Name *</label>
            <input type="text" name="canonical_name" class="form-control" placeholder="e.g. Malaviya National Institute of Technology" required />
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Short Code / Badge</label>
            <input type="text" name="short_code" class="form-control" placeholder="e.g. MNIT" required />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Institution Type *</label>
            <select name="institution_type" class="form-control" required>
              <option value="University">University</option>
              <option value="College">College</option>
              <option value="Standalone HEI">Standalone HEI / Autonomous</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Ownership Model *</label>
            <select name="ownership" class="form-control" required>
              <option value="Central Government">Central Government</option>
              <option value="Institute of National Importance">Institute of National Importance</option>
              <option value="State Government">State Government</option>
              <option value="Private Unaided">Private Unaided</option>
              <option value="Government-Aided">Government-Aided</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City *</label>
            <input type="text" name="city" class="form-control" value="Jaipur" required />
          </div>
          <div class="form-group">
            <label class="form-label">State *</label>
            <input type="text" name="state" class="form-control" value="Rajasthan" required />
          </div>
          <div class="form-group">
            <label class="form-label">Locality / Landmark</label>
            <input type="text" name="locality" class="form-control" placeholder="e.g. Malviya Nagar, JLN Marg" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">NAAC Accreditation Grade</label>
            <select name="naac_grade" class="form-control">
              <option value="A++">A++ (Highest)</option>
              <option value="A+" selected>A+</option>
              <option value="A">A</option>
              <option value="B++">B++</option>
              <option value="Recognized">UGC Recognized</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">NIRF 2025/26 Ranking / Band</label>
            <input type="text" name="nirf_band" class="form-control" placeholder="e.g. Rank 37 (Engineering) or Top 100" />
          </div>
          <div class="form-group">
            <label class="form-label">Campus Size (Acres)</label>
            <input type="number" step="0.1" name="campus_acres" class="form-control" value="50.0" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Official Website URL *</label>
            <input type="url" name="official_website" class="form-control" placeholder="https://..." required />
          </div>
          <div class="form-group">
            <label class="form-label">Admissions Portal URL</label>
            <input type="url" name="admissions_url" class="form-control" placeholder="https://.../admissions" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Admissions Contact Email</label>
            <input type="email" name="primary_email" class="form-control" placeholder="admissions@college.edu.in" />
          </div>
          <div class="form-group">
            <label class="form-label">Admissions Helpline Phone</label>
            <input type="text" name="primary_phone" class="form-control" placeholder="+91 141 ..." />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Statutory Provenance Verification Source</label>
            <input type="text" name="provenance_source_name" class="form-control" value="Official University Gazette / AISHE" />
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="verified" selected>Verified (Live on Platform)</option>
              <option value="under_review">Under Review (Draft)</option>
            </select>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addEduInstitutionModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary" style="background:#D97746; color:#FFF; border:none;">Publish College to Platform</button>
      </div>
    </form>
  </div>
</div>

<!-- =======================================================================
     MODAL 2: EDIT COLLEGE / INSTITUTION
     ======================================================================= -->
<div class="modal-backdrop" id="editEduInstitutionModal" onclick="handleBackdropClick(event, 'editEduInstitutionModal')">
  <div class="modal-window" style="max-width:750px;">
    <div class="modal-header">
      <h3 class="modal-title" style="color:#2563EB;">
        <i class="fa-solid fa-pen-to-square"></i> Edit College Details
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('editEduInstitutionModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="edit_edu_institution" />
      <input type="hidden" name="id" id="editEduId" value="" />
      <div class="modal-body">
        
        <div class="form-row">
          <div class="form-group" style="flex:2;">
            <label class="form-label">College Canonical Name *</label>
            <input type="text" name="canonical_name" id="editEduName" class="form-control" required />
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Short Code / Badge</label>
            <input type="text" name="short_code" id="editEduCode" class="form-control" required />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Institution Type</label>
            <select name="institution_type" id="editEduType" class="form-control">
              <option value="University">University</option>
              <option value="College">College</option>
              <option value="Standalone HEI">Standalone HEI / Autonomous</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Ownership</label>
            <select name="ownership" id="editEduOwnership" class="form-control">
              <option value="Central Government">Central Government</option>
              <option value="Institute of National Importance">Institute of National Importance</option>
              <option value="State Government">State Government</option>
              <option value="Private Unaided">Private Unaided</option>
              <option value="Government-Aided">Government-Aided</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" id="editEduCity" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">State</label>
            <input type="text" name="state" id="editEduState" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Locality</label>
            <input type="text" name="locality" id="editEduLocality" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">NAAC Grade</label>
            <select name="naac_grade" id="editEduNaac" class="form-control">
              <option value="A++">A++</option>
              <option value="A+">A+</option>
              <option value="A">A</option>
              <option value="B++">B++</option>
              <option value="Recognized">Recognized</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">NIRF Rank / Band</label>
            <input type="text" name="nirf_band" id="editEduNirf" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Campus Size (Acres)</label>
            <input type="number" step="0.1" name="campus_acres" id="editEduCampus" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Official Website</label>
            <input type="url" name="official_website" id="editEduWebsite" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Admissions URL</label>
            <input type="url" name="admissions_url" id="editEduAdmUrl" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="primary_email" id="editEduEmail" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="primary_phone" id="editEduPhone" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" id="editEduStatus" class="form-control">
              <option value="verified">Verified (Live)</option>
              <option value="under_review">Under Review (Draft)</option>
            </select>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('editEduInstitutionModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary" style="background:#2563EB; color:#FFF; border:none;">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- =======================================================================
     MODAL 3: ADD COURSE OFFERING TO COLLEGE
     ======================================================================= -->
<div class="modal-backdrop" id="addEduOfferingModal" onclick="handleBackdropClick(event, 'addEduOfferingModal')">
  <div class="modal-window" style="max-width:680px;">
    <div class="modal-header">
      <h3 class="modal-title" style="color:#D97746;">
        <i class="fa-solid fa-book-open"></i> Add Course Offering (2026–27)
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addEduOfferingModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_edu_offering" />
      <div class="modal-body">
        
        <div class="form-group">
          <label class="form-label">Select College / University *</label>
          <select name="institution_id" id="offeringInstSelect" class="form-control" required>
            <option value="">-- Choose Institution --</option>
            <?php foreach ($eduInstitutions as $inst): ?>
              <option value="<?= (int)$inst['id'] ?>"><?= htmlspecialchars($inst['canonical_name']) ?> (<?= htmlspecialchars($inst['city']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row">
          <div class="form-group" style="flex:2;">
            <label class="form-label">Program / Course Name *</label>
            <input type="text" name="program_name" class="form-control" placeholder="e.g. B.Tech Computer Science & Engineering" required />
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Degree Type</label>
            <select name="degree_type" class="form-control">
              <option value="B.Tech">B.Tech</option>
              <option value="MBA">MBA</option>
              <option value="MBBS">MBBS</option>
              <option value="BBA">BBA</option>
              <option value="BCA">BCA</option>
              <option value="M.Tech">M.Tech</option>
              <option value="B.Sc">B.Sc</option>
              <option value="Law / LL.B">Law / LL.B</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Academic Level</label>
            <select name="academic_level" class="form-control">
              <option value="Undergraduate">Undergraduate</option>
              <option value="Postgraduate">Postgraduate</option>
              <option value="Diploma">Diploma</option>
              <option value="Doctorate">Doctorate</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Duration (Years)</label>
            <input type="number" step="0.5" name="duration_years" class="form-control" value="4.0" required />
          </div>
          <div class="form-group">
            <label class="form-label">Academic Year</label>
            <input type="text" name="academic_year" class="form-control" value="2026-27" required />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Annual Tuition Fee (₹) *</label>
            <input type="number" name="annual_tuition_fee" class="form-control" placeholder="150000" required />
          </div>
          <div class="form-group">
            <label class="form-label">Intake Seats</label>
            <input type="number" name="intake_seats" class="form-control" value="60" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Entrance Exam(s) Accepted</label>
          <input type="text" name="entrance_exams" class="form-control" placeholder="e.g. JEE Main / REAP / Direct Merit" value="JEE Main / REAP" />
        </div>

        <div class="form-group">
          <label class="form-label">Eligibility Criteria</label>
          <input type="text" name="eligibility_criteria" class="form-control" value="10+2 with Physics, Mathematics & min 50% aggregate" />
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addEduOfferingModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary" style="background:#D97746; color:#FFF; border:none;">Save Course Offering</button>
      </div>
    </form>
  </div>
</div>

<!-- =======================================================================
     MODAL 4: ADD ENTRANCE EXAM
     ======================================================================= -->
<div class="modal-backdrop" id="addEduExamModal" onclick="handleBackdropClick(event, 'addEduExamModal')">
  <div class="modal-window" style="max-width:620px;">
    <div class="modal-header">
      <h3 class="modal-title" style="color:#D97746;">
        <i class="fa-solid fa-clipboard-list"></i> Add National / State Entrance Exam
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addEduExamModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_edu_exam" />
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group" style="flex:2;">
            <label class="form-label">Exam Name *</label>
            <input type="text" name="exam_name" class="form-control" placeholder="e.g. REAP 2026 Rajasthan Engineering Admission" required />
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Short Code *</label>
            <input type="text" name="short_code" class="form-control" placeholder="e.g. REAP" required />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category" class="form-control">
              <option value="Engineering">Engineering</option>
              <option value="Medical">Medical</option>
              <option value="Management">Management</option>
              <option value="Law">Law</option>
              <option value="Design">Design</option>
              <option value="Government">Government</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Conducting Body</label>
            <input type="text" name="conducting_body" class="form-control" value="CEG Rajasthan / NTA" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Exam Date</label>
            <input type="date" name="exam_date" class="form-control" value="2026-05-20" />
          </div>
          <div class="form-group">
            <label class="form-label">Application Deadline</label>
            <input type="date" name="application_end" class="form-control" value="2026-04-30" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Application Fee</label>
            <input type="text" name="application_fee" class="form-control" value="₹1,000" />
          </div>
          <div class="form-group">
            <label class="form-label">Official Portal URL</label>
            <input type="url" name="official_url" class="form-control" value="https://nta.ac.in" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addEduExamModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary" style="background:#D97746; color:#FFF; border:none;">Publish Exam</button>
      </div>
    </form>
  </div>
</div>

<!-- =======================================================================
     MODAL 5: ADD SCHOLARSHIP
     ======================================================================= -->
<div class="modal-backdrop" id="addEduScholarshipModal" onclick="handleBackdropClick(event, 'addEduScholarshipModal')">
  <div class="modal-window" style="max-width:620px;">
    <div class="modal-header">
      <h3 class="modal-title" style="color:#7C3AED;">
        <i class="fa-solid fa-award"></i> Add Higher Education Scholarship
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addEduScholarshipModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_edu_scholarship" />
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Scholarship Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. Chief Minister Higher Education Scholarship 2026" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Provider Authority</label>
            <input type="text" name="provider" class="form-control" value="Government of Rajasthan" />
          </div>
          <div class="form-group">
            <label class="form-label">Category</label>
            <select name="category" class="form-control">
              <option value="Merit-Based">Merit-Based</option>
              <option value="Means-Cum-Merit">Means-Cum-Merit</option>
              <option value="Women in STEM">Women in STEM</option>
              <option value="Reserved Category">Reserved Category</option>
              <option value="Corporate CSR">Corporate CSR</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Amount Display</label>
            <input type="text" name="amount_display" class="form-control" value="Up to ₹50,000 / year" />
          </div>
          <div class="form-group">
            <label class="form-label">Applicable Course(s)</label>
            <input type="text" name="applicable_course" class="form-control" value="B.Tech / Degree Programs" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Eligibility Criteria</label>
            <input type="text" name="eligibility" class="form-control" value="Min 75% in 12th Board & Family income < ₹8 LPA" />
          </div>
          <div class="form-group">
            <label class="form-label">Application Deadline</label>
            <input type="date" name="deadline" class="form-control" value="2026-08-31" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Official Application Portal URL</label>
          <input type="url" name="official_link" class="form-control" value="https://scholarships.gov.in" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addEduScholarshipModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary" style="background:#7C3AED; color:#FFF; border:none;">Publish Scholarship</button>
      </div>
    </form>
  </div>
</div>

<script>
  function switchEduSubtab(subtab) {
    const tabs = ['institutions', 'offerings', 'leads', 'exams', 'scholarships', 'approvals'];
    tabs.forEach(t => {
      const el = document.getElementById('eduSubtab' + t.charAt(0).toUpperCase() + t.slice(1));
      const btn = document.getElementById('btnSubtabEdu' + t.charAt(0).toUpperCase() + t.slice(1));
      if (el) el.style.display = (t === subtab) ? 'block' : 'none';
      if (btn) {
        btn.style.background = (t === subtab) ? '#D97746' : '#F1F5F9';
        btn.style.color = (t === subtab) ? '#FFF' : '#475569';
        btn.style.border = (t === subtab) ? 'none' : '1px solid #CBD5E1';
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($activeSubtab)): ?>
      switchEduSubtab('<?= $activeSubtab ?>');
    <?php endif; ?>
  });

  function openEditEduModal(inst) {
    if (!inst) return;
    document.getElementById('editEduId').value = inst.id || '';
    document.getElementById('editEduName').value = inst.canonical_name || '';
    document.getElementById('editEduCode').value = inst.short_code || inst.logo_text || '';
    if (document.getElementById('editEduType')) document.getElementById('editEduType').value = inst.institution_type || 'University';
    if (document.getElementById('editEduOwnership')) document.getElementById('editEduOwnership').value = inst.ownership || 'Private Unaided';
    document.getElementById('editEduCity').value = inst.city || '';
    document.getElementById('editEduState').value = inst.state || 'Rajasthan';
    document.getElementById('editEduLocality').value = inst.locality || '';
    if (document.getElementById('editEduNaac')) document.getElementById('editEduNaac').value = inst.naac_grade || 'A+';
    document.getElementById('editEduNirf').value = inst.nirf_band || '';
    document.getElementById('editEduCampus').value = inst.campus_acres || '';
    document.getElementById('editEduWebsite').value = inst.official_website || '';
    document.getElementById('editEduAdmUrl').value = inst.admissions_url || '';
    document.getElementById('editEduEmail').value = inst.primary_email || '';
    document.getElementById('editEduPhone').value = inst.primary_phone || '';
    if (document.getElementById('editEduStatus')) document.getElementById('editEduStatus').value = inst.status || 'verified';
    
    openModal('editEduInstitutionModal');
  }

  function openAddOfferingForInst(instId, instName) {
    const sel = document.getElementById('offeringInstSelect');
    if (sel && instId) {
      sel.value = instId;
    }
    openModal('addEduOfferingModal');
  }
</script>
