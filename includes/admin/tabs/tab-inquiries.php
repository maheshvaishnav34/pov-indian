<?php
/**
 * Admin Tab: Inquiries & Leads
 */
?>
<section class="tab-section <?= $activeTab === 'inquiries' ? 'active' : '' ?>" id="section-inquiries">
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-envelope-open-text" style="color:var(--primary);"></i> Inquiries & Form Submissions (<?= count($submissions) ?>)
      </h2>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search inquiries..." onkeyup="filterTable(this, 'table-inquiries')" />
      </div>
    </div>
    <div class="table-container">
      <?php if (empty($submissions)): ?>
        <div class="empty-state-box">
          <i class="fa-regular fa-folder-open"></i>
          <p>No inquiries or lead submissions received yet.</p>
        </div>
      <?php else: ?>
        <table class="data-table" id="table-inquiries">
          <thead>
            <tr>
              <th>ID</th>
              <th>Type</th>
              <th>Name</th>
              <th>Email Address</th>
              <th>Subject & Message</th>
              <th>Submitted At</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($submissions as $sub): ?>
              <tr>
                <td style="font-weight:700;">#<?= htmlspecialchars($sub['id']) ?></td>
                <td>
                  <?php
                    $typeChip = 'chip-slate';
                    if ($sub['type'] === 'list-business') $typeChip = 'chip-primary';
                    elseif ($sub['type'] === 'contact') $typeChip = 'chip-info';
                    elseif ($sub['type'] === 'signup') $typeChip = 'chip-success';
                  ?>
                  <span class="chip <?= $typeChip ?>"><?= htmlspecialchars($sub['type']) ?></span>
                </td>
                <td style="font-weight:600;"><?= htmlspecialchars($sub['name'] ?? 'N/A') ?></td>
                <td><a href="mailto:<?= htmlspecialchars($sub['email']) ?>" style="color:var(--info); text-decoration:none;"><?= htmlspecialchars($sub['email']) ?></a></td>
                <td style="max-width:320px;">
                  <div style="font-weight:600; margin-bottom:2px;"><?= htmlspecialchars($sub['subject'] ?? '') ?></div>
                  <div style="color:var(--text-muted); font-size:12.5px; line-height:1.4;"><?= htmlspecialchars($sub['message'] ?? '') ?></div>
                </td>
                <td style="color:var(--text-muted); font-size:12.5px; white-space:nowrap;">
                  <?= htmlspecialchars(date('M d, Y · h:i A', strtotime($sub['created_at']))) ?>
                </td>
                <td><span class="chip chip-success"><?= htmlspecialchars($sub['status']) ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</section>
