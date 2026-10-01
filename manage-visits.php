<?php
/**
 * manage-visits.php
 * POV Indian Rentals & Stays - Owner / Agent / Admin Visit Request Management
 * Allows property owners, wardens, and administrators to review, confirm, reschedule, or cancel visit requests.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/visit-store.php';

$page_title = 'Visit Requests Manager | POV Indian Host Portal';
$page_description = 'Manage incoming property walkthrough requests, confirm dates, and propose alternate schedules.';
$current_page = 'pages';
$body_class = 'category-landing rentals-page';
$extra_css = 'css/schedule-visit.css';

$allVisits = pov_load_all_visits();
$metrics = pov_get_visit_metrics();

$statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
$visits = $allVisits;
if ($statusFilter !== 'all') {
  $visits = array_values(array_filter($allVisits, function($v) use ($statusFilter) {
    return strtolower($v['status'] ?? '') === $statusFilter;
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
        <span class="sv-crumb-curr">Owner / Agent Visit Manager</span>
      </nav>

      <a href="<?= htmlspecialchars(pov_url('my-visits.php')) ?>" 
         style="font-size:12.5px; font-weight:700; color:var(--sv-navy); text-decoration:none; background:#FFFFFF; border:1px solid #E2E8F0; padding:6px 14px; border-radius:999px;">
        <i class="fa-solid fa-arrow-left"></i> View as Tenant
      </a>
    </div>
  </div>

  <main class="container" style="margin-bottom:60px;">
    <!-- Metric Cards Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:28px;">
      <div style="background:#FFFFFF; border:1px solid var(--sv-border); border-radius:var(--sv-radius-md); padding:18px 20px;">
        <span style="font-size:12px; color:var(--sv-muted); font-weight:700; text-transform:uppercase;">Total Requests</span>
        <h2 style="font-size:28px; font-weight:800; color:var(--sv-navy); margin:4px 0 0;"><?= $metrics['total'] ?></h2>
      </div>

      <div style="background:#FFFFFF; border:1px solid #FDE68A; border-radius:var(--sv-radius-md); padding:18px 20px; background:#FFFBEB;">
        <span style="font-size:12px; color:#92400E; font-weight:700; text-transform:uppercase;">Pending Approval</span>
        <h2 style="font-size:28px; font-weight:800; color:#B45309; margin:4px 0 0;"><?= $metrics['requested'] ?></h2>
      </div>

      <div style="background:#FFFFFF; border:1px solid #BBF7D0; border-radius:var(--sv-radius-md); padding:18px 20px; background:#ECFDF5;">
        <span style="font-size:12px; color:#065F46; font-weight:700; text-transform:uppercase;">Confirmed Visits</span>
        <h2 style="font-size:28px; font-weight:800; color:#047857; margin:4px 0 0;"><?= $metrics['confirmed'] ?></h2>
      </div>

      <div style="background:#FFFFFF; border:1px solid #BAE6FD; border-radius:var(--sv-radius-md); padding:18px 20px; background:#F0F9FF;">
        <span style="font-size:12px; color:#075985; font-weight:700; text-transform:uppercase;">Rescheduled</span>
        <h2 style="font-size:28px; font-weight:800; color:#0284C7; margin:4px 0 0;"><?= $metrics['rescheduled'] ?></h2>
      </div>

      <div style="background:#FFFFFF; border:1px solid var(--sv-border); border-radius:var(--sv-radius-md); padding:18px 20px;">
        <span style="font-size:12px; color:var(--sv-muted); font-weight:700; text-transform:uppercase;">Completed</span>
        <h2 style="font-size:28px; font-weight:800; color:var(--sv-navy); margin:4px 0 0;"><?= $metrics['completed'] ?></h2>
      </div>
    </div>

    <!-- Management Table Container -->
    <div style="background:#FFFFFF; border:1px solid var(--sv-border); border-radius:var(--sv-radius-lg); padding:28px; box-shadow:0 4px 20px rgba(20,33,50,0.05);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid var(--sv-border);">
        <div>
          <h2 style="font-family:var(--sv-font-serif); font-size:24px; font-weight:700; color:var(--sv-navy); margin:0 0 4px;">Visit Requests Pipeline</h2>
          <p style="font-size:13.5px; color:var(--sv-muted); margin:0;">Review visitor requests, verify slot availability, and send confirmations.</p>
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <a href="?status=all" class="rd-switcher-pill <?= $statusFilter === 'all' ? 'is-active' : '' ?>">All</a>
          <a href="?status=requested" class="rd-switcher-pill <?= $statusFilter === 'requested' ? 'is-active' : '' ?>">Pending</a>
          <a href="?status=confirmed" class="rd-switcher-pill <?= $statusFilter === 'confirmed' ? 'is-active' : '' ?>">Confirmed</a>
          <a href="?status=rescheduled" class="rd-switcher-pill <?= $statusFilter === 'rescheduled' ? 'is-active' : '' ?>">Rescheduled</a>
          <a href="?status=completed" class="rd-switcher-pill <?= $statusFilter === 'completed' ? 'is-active' : '' ?>">Completed</a>
          <a href="?status=cancelled" class="rd-switcher-pill <?= $statusFilter === 'cancelled' ? 'is-active' : '' ?>">Cancelled</a>
        </div>
      </div>

      <!-- Table -->
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13.5px;">
          <thead>
            <tr style="border-bottom:2px solid #E2E8F0; color:var(--sv-muted); font-size:12px; text-transform:uppercase;">
              <th style="padding:12px 14px;">Visit ID & Property</th>
              <th style="padding:12px 14px;">Visitor Details</th>
              <th style="padding:12px 14px;">Preferred Slot</th>
              <th style="padding:12px 14px;">Status</th>
              <th style="padding:12px 14px;">Message / Note</th>
              <th style="padding:12px 14px; text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($visits)): ?>
              <tr>
                <td colspan="6" style="padding:40px; text-align:center; color:var(--sv-muted);">
                  No visit requests in this filter.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($visits as $v): ?>
                <?php 
                  $status = strtoupper($v['status'] ?? 'REQUESTED');
                  $badgeClass = 'sv-status-requested';
                  if ($status === 'CONFIRMED') $badgeClass = 'sv-status-confirmed';
                  elseif ($status === 'RESCHEDULED') $badgeClass = 'sv-status-rescheduled';
                  elseif ($status === 'CANCELLED') $badgeClass = 'sv-status-cancelled';
                  elseif ($status === 'COMPLETED') $badgeClass = 'sv-status-requested';
                ?>
                <tr style="border-bottom:1px solid #F1F5F9;">
                  <!-- Property Info -->
                  <td style="padding:14px; vertical-align:top;">
                    <strong style="color:var(--sv-navy); font-size:12.5px; display:block;"><?= htmlspecialchars($v['id']) ?></strong>
                    <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $v['property_id'])) ?>" target="_blank" 
                       style="color:var(--sv-terracotta); font-weight:700; text-decoration:none; display:block; max-width:200px; margin-top:2px;">
                      <?= htmlspecialchars($v['property_title']) ?>
                    </a>
                    <span style="color:var(--sv-muted); font-size:11.5px;"><?= htmlspecialchars($v['property_location']) ?></span>
                  </td>

                  <!-- Visitor Info -->
                  <td style="padding:14px; vertical-align:top;">
                    <strong style="color:var(--sv-navy);"><?= htmlspecialchars($v['visitor_name']) ?></strong>
                    <div style="color:var(--sv-muted); font-size:12.5px;">
                      <i class="fa-solid fa-phone" style="font-size:10px;"></i> +91 <?= htmlspecialchars($v['visitor_phone']) ?>
                    </div>
                    <?php if (!empty($v['visitor_email'])): ?>
                      <div style="color:var(--sv-muted); font-size:11.5px;"><?= htmlspecialchars($v['visitor_email']) ?></div>
                    <?php endif; ?>
                    <span style="background:#F1F5F9; color:#475569; padding:2px 6px; border-radius:4px; font-size:11px; margin-top:4px; display:inline-block;">
                      <?= $v['visitor_count'] ?> visitor(s)
                    </span>
                  </td>

                  <!-- Slot -->
                  <td style="padding:14px; vertical-align:top;">
                    <strong style="color:var(--sv-navy); display:block;"><?= pov_format_visit_date($v['confirmed_date'] ?? $v['preferred_date']) ?></strong>
                    <span style="color:var(--sv-terracotta); font-weight:700;"><?= htmlspecialchars($v['confirmed_time'] ?? $v['preferred_time']) ?></span>
                    <?php if (!empty($v['alternate_date'])): ?>
                      <div style="font-size:11px; color:#64748B; margin-top:4px;">
                        Alt: <?= htmlspecialchars($v['alternate_date']) ?> (<?= htmlspecialchars($v['alternate_time'] ?? 'Any') ?>)
                      </div>
                    <?php endif; ?>
                  </td>

                  <!-- Status -->
                  <td style="padding:14px; vertical-align:top;">
                    <span class="sv-badge-status <?= $badgeClass ?>"><?= $status ?></span>
                    <?php if (!empty($v['owner_message'])): ?>
                      <div style="font-size:11.5px; color:#475569; margin-top:4px; max-width:180px; font-style:italic;">
                        "<?= htmlspecialchars($v['owner_message']) ?>"
                      </div>
                    <?php endif; ?>
                  </td>

                  <!-- Notes -->
                  <td style="padding:14px; vertical-align:top; max-width:180px; color:#475569; font-size:12.5px;">
                    <?= !empty($v['message']) ? htmlspecialchars($v['message']) : '<span style="color:#94A3B8;">None</span>' ?>
                  </td>

                  <!-- Actions -->
                  <td style="padding:14px; vertical-align:top; text-align:right; white-space:nowrap;">
                    <?php if ($status === 'REQUESTED' || $status === 'RESCHEDULED'): ?>
                      <button type="button" class="sv-btn-submit" style="padding:6px 12px; font-size:12px; margin-bottom:4px;" 
                              onclick="ownerConfirm('<?= $v['id'] ?>')">
                        <i class="fa-solid fa-check"></i> Confirm
                      </button>
                      <button type="button" class="sv-btn-schedule" style="padding:6px 10px; font-size:12px; margin-bottom:4px;" 
                              onclick="ownerReschedule('<?= $v['id'] ?>', '<?= $v['preferred_date'] ?>')">
                        Propose Time
                      </button>
                      <button type="button" class="sv-btn-cancel" style="padding:6px 10px; font-size:12px; color:#DC2626;" 
                              onclick="ownerCancel('<?= $v['id'] ?>')">
                        Reject
                      </button>

                    <?php elseif ($status === 'CONFIRMED'): ?>
                      <button type="button" class="sv-btn-schedule" style="padding:6px 12px; font-size:12px; color:#059669; border-color:#BBF7D0; background:#ECFDF5;" 
                              onclick="ownerComplete('<?= $v['id'] ?>')">
                        <i class="fa-solid fa-circle-check"></i> Mark Completed
                      </button>
                      <button type="button" class="sv-btn-cancel" style="padding:6px 10px; font-size:12px;" 
                              onclick="ownerCancel('<?= $v['id'] ?>')">
                        Cancel Visit
                      </button>

                    <?php else: ?>
                      <span style="font-size:12px; color:var(--sv-muted); font-weight:600;">Archived</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Propose Alternate Time Modal -->
  <div class="rd-modal-overlay" id="modalReschedule" onclick="closeOnOverlay(event, 'modalReschedule')">
    <div class="rd-modal-content">
      <button type="button" class="rd-modal-close" onclick="closeModal('modalReschedule')">&times;</button>
      <h3 class="rd-modal-title">Propose Alternate Visit Time</h3>
      <p class="rd-modal-sub">Suggest a convenient date & time for this visitor.</p>

      <form onsubmit="handleRescheduleSubmit(event)">
        <input type="hidden" id="reschedVisitId" name="visit_id" />

        <div class="sv-form-group">
          <label class="sv-label">Proposed New Date</label>
          <input type="date" class="sv-input" id="reschedDate" name="new_date" required min="<?= date('Y-m-d') ?>" />
        </div>

        <div class="sv-form-group">
          <label class="sv-label">Proposed Time Slot</label>
          <select class="sv-select" id="reschedTime" name="new_time" required>
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

        <div class="sv-form-group">
          <label class="sv-label">Message for Visitor</label>
          <textarea class="sv-textarea" id="reschedMessage" name="owner_message" rows="2" 
                    placeholder="e.g. Previous tenant is moving out on Friday morning. Please visit Friday evening at 4:00 PM."></textarea>
        </div>

        <button type="submit" class="sv-btn-submit" style="width:100%;">Send Reschedule Proposal</button>
      </form>
    </div>
  </div>

  <script>
    function ownerConfirm(id) {
      const note = prompt('Add confirmation note / lobby entry instructions (optional):', 'Confirmed! Please meet security at Gate 1 lobby.');
      if (note === null) return;

      const fd = new FormData();
      fd.append('visit_id', id);
      fd.append('owner_message', note);

      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=confirm')) ?>', {
        method: 'POST',
        body: fd
      })
      .then(res => res.json())
      .then(d => {
        alert(d.message);
        location.reload();
      });
    }

    function ownerReschedule(id, curDate) {
      document.getElementById('reschedVisitId').value = id;
      document.getElementById('reschedDate').value = curDate || '<?= date('Y-m-d', strtotime('+1 day')) ?>';
      document.getElementById('modalReschedule').classList.add('is-open');
    }

    function handleRescheduleSubmit(e) {
      e.preventDefault();
      const form = e.target;
      const fd = new FormData(form);

      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=reschedule')) ?>', {
        method: 'POST',
        body: fd
      })
      .then(res => res.json())
      .then(d => {
        alert(d.message);
        location.reload();
      });
    }

    function ownerCancel(id) {
      const reason = prompt('Reason for cancelling/rejecting this visit request:', 'Slot unavailable / Maintenance ongoing');
      if (reason === null) return;

      const fd = new FormData();
      fd.append('visit_id', id);
      fd.append('reason', reason);
      fd.append('cancelled_by', 'Owner');

      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=cancel')) ?>', {
        method: 'POST',
        body: fd
      })
      .then(res => res.json())
      .then(d => {
        alert(d.message);
        location.reload();
      });
    }

    function ownerComplete(id) {
      if (!confirm('Mark this property walkthrough as COMPLETED?')) return;
      const fd = new FormData();
      fd.append('visit_id', id);

      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=complete')) ?>', {
        method: 'POST',
        body: fd
      })
      .then(res => res.json())
      .then(d => {
        alert(d.message);
        location.reload();
      });
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('is-open');
    }
    function closeOnOverlay(e, id) {
      if (e.target.id === id) closeModal(id);
    }
  </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
