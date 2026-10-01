<?php
/**
 * Admin Tab: Users Management
 */
?>
<section class="tab-section <?= $activeTab === 'users' ? 'active' : '' ?>" id="section-users">
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-users" style="color:var(--primary);"></i> Registered Accounts (<?= count($users) ?>)
      </h2>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search users..." onkeyup="filterTable(this, 'table-users')" />
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-users">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Role</th>
            <th>Phone</th>
            <th>Interests</th>
            <th>Status</th>
            <th>Joined Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr><td colspan="8" class="empty-state-box">No users found in database.</td></tr>
          <?php else: ?>
            <?php foreach ($users as $u): ?>
              <tr>
                <td style="font-weight:700;">#<?= htmlspecialchars($u['id']) ?></td>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="profile-avatar" style="width:32px; height:32px; font-size:13px;">
                      <?= strtoupper(substr($u['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <span style="font-weight:700;"><?= htmlspecialchars($u['name']) ?></span>
                  </div>
                </td>
                <td><a href="mailto:<?= htmlspecialchars($u['email']) ?>" style="color:var(--info); text-decoration:none;"><?= htmlspecialchars($u['email']) ?></a></td>
                <td>
                  <?php if ($u['role'] === 'admin'): ?>
                    <span class="chip chip-primary"><i class="fa-solid fa-shield-halved"></i> SuperAdmin</span>
                  <?php else: ?>
                    <span class="chip chip-info"><i class="fa-solid fa-user"></i> User</span>
                  <?php endif; ?>
                </td>
                <td style="color:var(--text-muted); font-size:13px;"><?= htmlspecialchars($u['phone'] ?: '-') ?></td>
                <td style="font-size:12.5px;"><?= htmlspecialchars($u['interests'] ?: '-') ?></td>
                <td>
                  <?php if (!empty($u['is_active'])): ?>
                    <span class="chip chip-success">Active</span>
                  <?php else: ?>
                    <span class="chip chip-slate">Inactive</span>
                  <?php endif; ?>
                </td>
                <td style="color:var(--text-muted); font-size:12.5px; white-space:nowrap;">
                  <?= htmlspecialchars(date('M d, Y', strtotime($u['created_at']))) ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
