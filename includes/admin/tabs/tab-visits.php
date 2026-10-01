<?php
/**
 * includes/admin/tabs/tab-visits.php
 * Admin Tab: Scheduled Property Visits & Walkthrough Requests
 */
?>
<section class="tab-section <?= $activeTab === 'visits' ? 'active' : '' ?>" id="section-visits">

  <!-- Metric Overview Cards -->
  <div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card" style="border-left: 4px solid #059669; background:#f0fdf4;">
      <div>
        <div class="stat-title" style="color:#047857;">Total Requests</div>
        <div class="stat-value" style="color:#065f46;"><?= $visitMetrics['total'] ?? count($visitsList ?? []) ?></div>
        <div class="stat-desc" style="color:#059669;">
          <i class="fa-regular fa-calendar-check"></i> All recorded visits
        </div>
      </div>
      <div class="stat-icon-wrap" style="background:#dcfce7; color:#059669;">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
    </div>

    <div class="stat-card orange">
      <div>
        <div class="stat-title">Pending Approval</div>
        <div class="stat-value"><?= $visitMetrics['requested'] ?? 0 ?></div>
        <div class="stat-desc" style="color:var(--primary);">
          <i class="fa-regular fa-clock"></i> Requires host/admin action
        </div>
      </div>
      <div class="stat-icon-wrap orange">
        <i class="fa-solid fa-hourglass-half"></i>
      </div>
    </div>

    <div class="stat-card green">
      <div>
        <div class="stat-title">Confirmed Visits</div>
        <div class="stat-value"><?= $visitMetrics['confirmed'] ?? 0 ?></div>
        <div class="stat-desc" style="color:var(--success);">
          <i class="fa-solid fa-circle-check"></i> Slots confirmed
        </div>
      </div>
      <div class="stat-icon-wrap green">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
    </div>

    <div class="stat-card blue">
      <div>
        <div class="stat-title">Rescheduled</div>
        <div class="stat-value"><?= $visitMetrics['rescheduled'] ?? 0 ?></div>
        <div class="stat-desc" style="color:var(--info);">
          <i class="fa-solid fa-arrows-rotate"></i> Slot counter-proposed
        </div>
      </div>
      <div class="stat-icon-wrap blue">
        <i class="fa-solid fa-calendar-day"></i>
      </div>
    </div>

    <div class="stat-card purple">
      <div>
        <div class="stat-title">Completed</div>
        <div class="stat-value"><?= $visitMetrics['completed'] ?? 0 ?></div>
        <div class="stat-desc" style="color:#8b5cf6;">
          <i class="fa-solid fa-flag-checkered"></i> Walkthroughs done
        </div>
      </div>
      <div class="stat-icon-wrap purple">
        <i class="fa-solid fa-circle-check"></i>
      </div>
    </div>
  </div>

  <!-- Visits Management Table Card -->
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-calendar-check" style="color:#059669;"></i> Scheduled Visits & Walkthrough Requests (<?= count($visitsList ?? []) ?>)
      </h2>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search visits by visitor, phone, property..." onkeyup="filterTable(this, 'table-visits')" />
        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>" target="_blank" class="btn-topbar btn-topbar-secondary" title="View Rentals & Stays">
          <i class="fa-solid fa-house-chimney"></i> Rentals Page
        </a>
      </div>
    </div>

    <div class="table-container">
      <?php if (empty($visitsList)): ?>
        <div class="empty-state-box">
          <i class="fa-regular fa-calendar-xmark" style="font-size:36px; color:var(--muted); margin-bottom:10px;"></i>
          <p>No visit requests recorded yet.</p>
        </div>
      <?php else: ?>
        <table class="data-table" id="table-visits">
          <thead>
            <tr>
              <th>Ref & Property</th>
              <th>Visitor Details</th>
              <th>Requested Slot</th>
              <th>Status</th>
              <th>Visitor Note</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($visitsList as $v): ?>
              <?php 
                $vStatus = strtoupper($v['status'] ?? 'REQUESTED');
                $statusChipClass = 'chip-primary';
                if ($vStatus === 'CONFIRMED') $statusChipClass = 'chip-success';
                elseif ($vStatus === 'RESCHEDULED') $statusChipClass = 'chip-info';
                elseif ($vStatus === 'CANCELLED') $statusChipClass = 'chip-danger';
                elseif ($vStatus === 'COMPLETED') $statusChipClass = 'chip-slate';
              ?>
              <tr>
                <td>
                  <span style="font-size:11.5px; font-weight:700; color:var(--text-muted);">#<?= htmlspecialchars($v['id']) ?></span>
                  <div style="font-weight:700; color:var(--primary); font-size:14px; margin-top:2px;">
                    <?= htmlspecialchars($v['property_title']) ?>
                  </div>
                  <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                    <i class="fa-solid fa-user-shield"></i> <?= htmlspecialchars($v['owner_name']) ?> (<?= htmlspecialchars($v['owner_role'] ?? 'Host') ?>)
                  </div>
                </td>

                <td>
                  <div style="font-weight:600; color:var(--ink);"><?= htmlspecialchars($v['visitor_name']) ?></div>
                  <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                    <i class="fa-solid fa-phone" style="font-size:10px;"></i> +91 <?= htmlspecialchars($v['visitor_phone']) ?>
                  </div>
                  <?php if (!empty($v['visitor_email'])): ?>
                    <div style="font-size:11.5px; color:var(--text-muted); margin-top:1px;">
                      <a href="mailto:<?= htmlspecialchars($v['visitor_email']) ?>" style="color:var(--info); text-decoration:none;"><?= htmlspecialchars($v['visitor_email']) ?></a>
                    </div>
                  <?php endif; ?>
                  <span class="chip chip-slate" style="margin-top:4px; font-size:10.5px;">
                    <?= (int)($v['visitor_count'] ?? 1) ?> Visitor(s)
                  </span>
                </td>

                <td>
                  <div style="font-weight:700; color:var(--ink);"><?= pov_format_visit_date($v['confirmed_date'] ?? $v['preferred_date']) ?></div>
                  <div style="color:#059669; font-weight:700; font-size:13px; margin-top:2px;">
                    <i class="fa-regular fa-clock" style="font-size:11px;"></i> <?= htmlspecialchars($v['confirmed_time'] ?? $v['preferred_time']) ?>
                  </div>
                  <?php if (!empty($v['alternate_date'])): ?>
                    <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                      Alt: <?= htmlspecialchars($v['alternate_date']) ?> (<?= htmlspecialchars($v['alternate_time'] ?? '') ?>)
                    </div>
                  <?php endif; ?>
                </td>

                <td>
                  <span class="chip <?= $statusChipClass ?>"><?= $vStatus ?></span>
                  <?php if (!empty($v['owner_message'])): ?>
                    <div style="font-size:11.5px; color:var(--text-muted); margin-top:4px; max-width:180px; font-style:italic;">
                      "<?= htmlspecialchars($v['owner_message']) ?>"
                    </div>
                  <?php endif; ?>
                </td>

                <td style="max-width:200px; font-size:12.5px; color:var(--text);">
                  <?= !empty($v['message']) ? htmlspecialchars($v['message']) : '<span style="color:var(--text-muted); font-style:italic;">None</span>' ?>
                </td>

                <td style="text-align:right; white-space:nowrap;">
                  <?php if ($vStatus === 'REQUESTED' || $vStatus === 'RESCHEDULED'): ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Confirm this visit slot?');">
                      <input type="hidden" name="admin_action" value="confirm_visit" />
                      <input type="hidden" name="visit_id" value="<?= htmlspecialchars($v['id']) ?>" />
                      <button type="submit" class="btn-topbar btn-topbar-secondary" style="padding:4px 10px; font-size:12px; color:#059669; border-color:#bbf7d0;" title="Confirm Visit">
                        <i class="fa-solid fa-check"></i> Confirm
                      </button>
                    </form>
                  <?php endif; ?>

                  <?php if ($vStatus === 'CONFIRMED'): ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Mark visit as completed?');">
                      <input type="hidden" name="admin_action" value="complete_visit" />
                      <input type="hidden" name="visit_id" value="<?= htmlspecialchars($v['id']) ?>" />
                      <button type="submit" class="btn-topbar btn-topbar-secondary" style="padding:4px 10px; font-size:12px; color:#8b5cf6; border-color:#ddd6fe;" title="Mark Completed">
                        <i class="fa-solid fa-flag-checkered"></i> Done
                      </button>
                    </form>
                  <?php endif; ?>

                  <?php if ($vStatus !== 'CANCELLED' && $vStatus !== 'COMPLETED'): ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Cancel this visit request?');">
                      <input type="hidden" name="admin_action" value="cancel_visit" />
                      <input type="hidden" name="visit_id" value="<?= htmlspecialchars($v['id']) ?>" />
                      <button type="submit" class="btn-topbar btn-topbar-secondary" style="padding:4px 10px; font-size:12px; color:#ef4444; border-color:#fecaca;" title="Cancel Visit">
                        <i class="fa-solid fa-ban"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</section>
