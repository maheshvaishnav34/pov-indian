<?php
/**
 * my-visits.php
 * POV Indian Rentals & Stays - User Dashboard: My Scheduled Visits
 * Displays visitor's scheduled visits, statuses, owner responses, and visit passes.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/visit-store.php';

$page_title = 'My Visits | POV Indian Rentals & Stays';
$page_description = 'View and manage your scheduled property walkthroughs and host confirmations.';
$current_page = 'listings';
$body_class = 'category-landing rentals-page';
$extra_css = 'css/schedule-visit.css';

$allVisits = pov_load_all_visits();
$metrics = pov_get_visit_metrics();

// Optional filter
$filter = strtolower(trim($_GET['status'] ?? 'all'));
$filteredVisits = $allVisits;
if ($filter !== 'all') {
  $filteredVisits = array_values(array_filter($allVisits, function($v) use ($filter) {
    return strtolower($v['status'] ?? '') === $filter;
  }));
}

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

  <div class="sv-breadcrumbs-bar">
    <div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <nav class="sv-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= htmlspecialchars(pov_url('index.php')) ?>">Home</a>
        <span class="sv-crumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>">Rentals & Stays</a>
        <span class="sv-crumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span class="sv-crumb-curr">My Visits Dashboard</span>
      </nav>

      <a href="<?= htmlspecialchars(pov_url('manage-visits.php')) ?>" 
         style="font-size:12.5px; font-weight:700; color:var(--sv-terracotta); text-decoration:none; background:#FFF2ED; border:1px solid #FED7AA; padding:6px 14px; border-radius:999px; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-user-shield"></i> Host / Admin Portal
      </a>
    </div>
  </div>

  <main class="container" style="margin-bottom:60px;">
    <!-- Page Header & Metrics Summary -->
    <div style="background:#FFFFFF; border:1px solid var(--sv-border); border-radius:var(--sv-radius-lg); padding:28px; margin-bottom:24px; box-shadow:0 2px 12px rgba(20,33,50,0.04);">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
        <div>
          <span style="font-size:11px; font-weight:800; letter-spacing:1px; text-transform:uppercase; color:var(--sv-terracotta);">TENANT DASHBOARD</span>
          <h1 style="font-family:var(--sv-font-serif); font-size:30px; font-weight:700; color:var(--sv-navy); margin:4px 0 6px;">My Property Visits</h1>
          <p style="font-size:14px; color:var(--sv-muted); margin:0;">Track physical inspection requests, host confirmations, and entry gate passes.</p>
        </div>

        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>" class="sv-btn-schedule" style="text-decoration:none;">
          <i class="fa-solid fa-magnifying-glass"></i> Explore More Homes
        </a>
      </div>

      <!-- Quick Metrics Counter Pills -->
      <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="?status=all" style="text-decoration:none;" class="rd-switcher-pill <?= $filter === 'all' ? 'is-active' : '' ?>">
          All Visits (<?= $metrics['total'] ?>)
        </a>
        <a href="?status=requested" style="text-decoration:none;" class="rd-switcher-pill <?= $filter === 'requested' ? 'is-active' : '' ?>">
          <span style="color:#D97706;"><i class="fa-solid fa-clock"></i></span> Pending (<?= $metrics['requested'] ?>)
        </a>
        <a href="?status=confirmed" style="text-decoration:none;" class="rd-switcher-pill <?= $filter === 'confirmed' ? 'is-active' : '' ?>">
          <span style="color:#059669;"><i class="fa-solid fa-circle-check"></i></span> Confirmed (<?= $metrics['confirmed'] ?>)
        </a>
        <a href="?status=rescheduled" style="text-decoration:none;" class="rd-switcher-pill <?= $filter === 'rescheduled' ? 'is-active' : '' ?>">
          <span style="color:#0284C7;"><i class="fa-solid fa-calendar-days"></i></span> Rescheduled (<?= $metrics['rescheduled'] ?>)
        </a>
        <a href="?status=completed" style="text-decoration:none;" class="rd-switcher-pill <?= $filter === 'completed' ? 'is-active' : '' ?>">
          Completed (<?= $metrics['completed'] ?>)
        </a>
      </div>
    </div>

    <!-- Visits List -->
    <?php if (empty($filteredVisits)): ?>
      <div style="background:#FFFFFF; border:1px dashed var(--sv-border); border-radius:var(--sv-radius-lg); padding:60px 20px; text-align:center;">
        <div style="font-size:44px; color:#CBD5E1; margin-bottom:12px;"><i class="fa-regular fa-calendar-xmark"></i></div>
        <h3 style="font-size:18px; color:var(--sv-navy); margin:0 0 6px;">No visit requests found in this view</h3>
        <p style="font-size:14px; color:var(--sv-muted); max-width:400px; margin:0 auto 20px;">Browse verified accommodations and schedule a free physical walkthrough before paying any advance.</p>
        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>" class="sv-btn-submit" style="text-decoration:none;">
          Browse Rentals & Stays
        </a>
      </div>
    <?php else: ?>
      <div style="display:flex; flex-direction:column; gap:18px;">
        <?php foreach ($filteredVisits as $v): ?>
          <?php 
            $status = strtoupper($v['status'] ?? 'REQUESTED');
            $badgeClass = 'sv-status-requested';
            $badgeIcon = 'fa-clock';
            $statusLabel = 'Pending Host Approval';

            if ($status === 'CONFIRMED') {
              $badgeClass = 'sv-status-confirmed';
              $badgeIcon = 'fa-circle-check';
              $statusLabel = 'Visit Confirmed';
            } elseif ($status === 'RESCHEDULED') {
              $badgeClass = 'sv-status-rescheduled';
              $badgeIcon = 'fa-calendar-days';
              $statusLabel = 'Host Proposed Alternate Time';
            } elseif ($status === 'COMPLETED') {
              $badgeClass = 'sv-status-requested';
              $badgeIcon = 'fa-flag-checkered';
              $statusLabel = 'Visit Completed';
            } elseif ($status === 'CANCELLED') {
              $badgeClass = 'sv-status-cancelled';
              $badgeIcon = 'fa-ban';
              $statusLabel = 'Visit Cancelled';
            }
          ?>
          <div style="background:#FFFFFF; border:1px solid var(--sv-border); border-radius:var(--sv-radius-lg); padding:24px; box-shadow:0 3px 14px rgba(20,33,50,0.04); display:grid; grid-template-columns:180px 1fr auto; gap:24px; align-items:center;">
            <!-- Property Thumbnail -->
            <div style="height:120px; border-radius:var(--sv-radius-md); overflow:hidden; background:#EAE3D9; position:relative;">
              <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . ($v['property_img'] ?? 'realestate_03.webp'))) ?>" 
                   alt="<?= htmlspecialchars($v['property_title']) ?>" style="width:100%; height:100%; object-fit:cover;" />
              <span style="position:absolute; top:8px; left:8px; background:rgba(0,0,0,0.7); color:#fff; font-size:10px; font-weight:700; padding:3px 7px; border-radius:4px;">
                #<?= $v['property_id'] ?>
              </span>
            </div>

            <!-- Visit Details Info -->
            <div>
              <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px; flex-wrap:wrap;">
                <span class="sv-badge-status <?= $badgeClass ?>" style="display:inline-flex; align-items:center; gap:5px;">
                  <i class="fa-solid <?= $badgeIcon ?>"></i> <?= $statusLabel ?>
                </span>
                <span style="font-size:12px; color:var(--sv-muted);">Ref: <strong><?= htmlspecialchars($v['id']) ?></strong></span>
                <span style="font-size:12px; color:var(--sv-muted);">• Requested <?= date('d M Y, h:i A', strtotime($v['created_at'])) ?></span>
              </div>

              <h3 style="font-family:var(--sv-font-serif); font-size:18px; margin:0 0 6px;">
                <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $v['property_id'])) ?>" style="color:var(--sv-navy); text-decoration:none;">
                  <?= htmlspecialchars($v['property_title']) ?>
                </a>
              </h3>

              <div style="font-size:13px; color:var(--sv-muted); margin-bottom:12px;">
                <i class="fa-solid fa-location-dot" style="color:var(--sv-terracotta);"></i> <?= htmlspecialchars($v['property_location']) ?>
              </div>

              <!-- Scheduled Time Box -->
              <div style="background:#FAF8F5; border:1px solid #ECE6DD; border-radius:8px; padding:10px 14px; display:inline-flex; align-items:center; gap:16px; font-size:13px;">
                <div>
                  <span style="color:var(--sv-muted); font-size:11px; display:block;">SCHEDULED DATE</span>
                  <strong style="color:var(--sv-navy); font-size:14px;"><i class="fa-regular fa-calendar" style="color:var(--sv-terracotta);"></i> <?= pov_format_visit_date($v['confirmed_date'] ?? $v['preferred_date']) ?></strong>
                </div>
                <div style="border-left:1px solid #E2E8F0; padding-left:16px;">
                  <span style="color:var(--sv-muted); font-size:11px; display:block;">TIME SLOT</span>
                  <strong style="color:var(--sv-navy); font-size:14px;"><i class="fa-regular fa-clock" style="color:var(--sv-terracotta);"></i> <?= htmlspecialchars($v['confirmed_time'] ?? $v['preferred_time']) ?></strong>
                </div>
                <div style="border-left:1px solid #E2E8F0; padding-left:16px;">
                  <span style="color:var(--sv-muted); font-size:11px; display:block;">HOST / CONTACT</span>
                  <strong style="color:var(--sv-navy); font-size:13px;"><?= htmlspecialchars($v['owner_name']) ?> (<?= htmlspecialchars($v['owner_role'] ?? 'Host') ?>)</strong>
                </div>
              </div>

              <!-- Owner Reschedule / Confirmation Alert Banner -->
              <?php if (!empty($v['owner_message'])): ?>
                <div style="margin-top:12px; background:<?= $status === 'CONFIRMED' ? '#ECFDF5' : '#E0F2FE' ?>; border:1px solid <?= $status === 'CONFIRMED' ? '#BBF7D0' : '#BAE6FD' ?>; border-radius:8px; padding:10px 14px; font-size:13px; color:<?= $status === 'CONFIRMED' ? '#065F46' : '#0369A1' ?>;">
                  <i class="fa-solid fa-comment-dots"></i> <strong>Note from <?= htmlspecialchars($v['owner_name']) ?>:</strong> <?= htmlspecialchars($v['owner_message']) ?>
                </div>
              <?php endif; ?>
            </div>

            <!-- Right Column Actions -->
            <div style="display:flex; flex-direction:column; gap:8px; min-width:160px; text-align:right;">
              <?php if ($status === 'REQUESTED'): ?>
                <button type="button" class="sv-btn-schedule" style="font-size:13px; padding:8px 16px; justify-content:center; color:#DC2626; border-color:#FECACA; background:#FEF2F2;" 
                        onclick="cancelVisit('<?= $v['id'] ?>')">
                  <i class="fa-solid fa-ban" style="color:#DC2626;"></i> Cancel Request
                </button>
                <span style="font-size:11px; color:var(--sv-muted); text-align:center;">Host will respond shortly</span>

              <?php elseif ($status === 'CONFIRMED'): ?>
                <button type="button" class="sv-btn-schedule" style="font-size:13px; padding:8px 16px; justify-content:center; background:#ECFDF5; border-color:#BBF7D0; color:#065F46;" 
                        onclick="viewPass('<?= $v['id'] ?>', '<?= htmlspecialchars(addslashes($v['property_title'])) ?>', '<?= pov_format_visit_date($v['confirmed_date']) ?>', '<?= htmlspecialchars($v['confirmed_time']) ?>', '<?= htmlspecialchars(addslashes($v['owner_name'])) ?>', '<?= htmlspecialchars(addslashes($v['owner_phone'])) ?>')">
                  <i class="fa-solid fa-ticket" style="color:#059669;"></i> View Visit Pass
                </button>
                <button type="button" class="sv-btn-cancel" style="font-size:12px; padding:6px 12px; justify-content:center;" 
                        onclick="cancelVisit('<?= $v['id'] ?>')">
                  Cancel Visit
                </button>

              <?php elseif ($status === 'RESCHEDULED'): ?>
                <button type="button" class="sv-btn-submit" style="font-size:13px; padding:8px 16px; justify-content:center; background:#0284C7;" 
                        onclick="acceptRescheduled('<?= $v['id'] ?>')">
                  <i class="fa-solid fa-check"></i> Accept New Time
                </button>
                <button type="button" class="sv-btn-cancel" style="font-size:12px; padding:6px 12px; justify-content:center;" 
                        onclick="cancelVisit('<?= $v['id'] ?>')">
                  Decline & Cancel
                </button>

              <?php else: ?>
                <a href="<?= htmlspecialchars(pov_url('schedule-visit.php?property_id=' . $v['property_id'])) ?>" class="sv-btn-schedule" style="font-size:12px; padding:6px 14px; justify-content:center; text-decoration:none;">
                  <i class="fa-regular fa-calendar-plus"></i> Re-schedule Visit
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Visit Pass Modal -->
  <div class="rd-modal-overlay" id="modalVisitPass" onclick="closeOnOverlay(event, 'modalVisitPass')">
    <div class="rd-modal-content" style="max-width:440px; padding:30px; text-align:center;">
      <button type="button" class="rd-modal-close" onclick="closeModal('modalVisitPass')" aria-label="Close">&times;</button>
      <div style="width:52px; height:52px; background:#ECFDF5; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#059669; font-size:22px; margin:0 auto 12px;">
        <i class="fa-solid fa-shield-check"></i>
      </div>
      <h3 style="font-family:var(--sv-font-serif); font-size:20px; color:var(--sv-navy); margin:0 0 4px;">Verified POV Indian Visit Pass</h3>
      <p style="font-size:12.5px; color:var(--sv-muted); margin:0 0 18px;">Show this digital gate pass upon arrival at the lobby entrance.</p>

      <div class="sv-receipt-box" style="margin-bottom:20px;">
        <div class="sv-receipt-row"><span class="sv-receipt-label">Pass ID</span><strong id="passId">POV-PASS-00</strong></div>
        <div class="sv-receipt-row"><span class="sv-receipt-label">Property</span><strong id="passProperty">--</strong></div>
        <div class="sv-receipt-row"><span class="sv-receipt-label">Confirmed Date</span><strong id="passDate">--</strong></div>
        <div class="sv-receipt-row"><span class="sv-receipt-label">Confirmed Slot</span><strong id="passTime">--</strong></div>
        <div class="sv-receipt-row"><span class="sv-receipt-label">Host Contact</span><strong id="passHost">--</strong></div>
        <div class="sv-receipt-row"><span class="sv-receipt-label">Entry Location</span><strong style="color:#059669;"><i class="fa-solid fa-location-dot"></i> Lobby Desk / Gate 1</strong></div>
      </div>

      <button type="button" class="sv-btn-submit" style="width:100%;" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print / Save as PDF
      </button>
    </div>
  </div>

  <script>
    function cancelVisit(id) {
      const reason = prompt('Please enter a cancellation reason (optional):', 'Schedule conflict');
      if (reason === null) return;

      const fd = new FormData();
      fd.append('visit_id', id);
      fd.append('reason', reason);
      fd.append('cancelled_by', 'Visitor');

      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=cancel')) ?>', {
        method: 'POST',
        body: fd
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        location.reload();
      });
    }

    function acceptRescheduled(id) {
      if (!confirm('Accept this proposed date & time from the owner?')) return;
      const fd = new FormData();
      fd.append('visit_id', id);
      fd.append('owner_message', 'Accepted proposed alternate slot.');

      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=confirm')) ?>', {
        method: 'POST',
        body: fd
      })
      .then(res => res.json())
      .then(data => {
        alert('Visit confirmed for the new proposed time!');
        location.reload();
      });
    }

    function viewPass(id, title, date, time, host, phone) {
      document.getElementById('passId').innerText = id;
      document.getElementById('passProperty').innerText = title;
      document.getElementById('passDate').innerText = date;
      document.getElementById('passTime').innerText = time;
      document.getElementById('passHost').innerText = host + ' (' + phone + ')';
      document.getElementById('modalVisitPass').classList.add('is-open');
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('is-open');
    }
    function closeOnOverlay(e, id) {
      if (e.target.id === id) closeModal(id);
    }
  </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
