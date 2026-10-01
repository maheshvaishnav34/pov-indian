<?php
/**
 * Admin Tab: System & Database Architecture
 */
?>
<section class="tab-section <?= $activeTab === 'system' ? 'active' : '' ?>" id="section-system">
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-server" style="color:var(--primary);"></i> System Architecture & Health
      </h2>
      <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=pov_indian_db" target="_blank" class="btn-topbar btn-topbar-primary">
        <i class="fa-solid fa-database"></i> Launch phpMyAdmin
      </a>
    </div>
    <div style="padding: 24px;">
      <div class="health-grid">
        <!-- MySQL Status -->
        <div class="health-card">
          <div class="health-card-header">
            <div class="health-card-title">
              <i class="fa-solid fa-database" style="color:#0284c7;"></i> MariaDB / MySQL
            </div>
            <span class="chip chip-success">Online</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Host & Port</span>
            <span class="health-info-val">127.0.0.1:3306</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Database Name</span>
            <span class="health-info-val">pov_indian_db</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Engine</span>
            <span class="health-info-val">InnoDB / utf8mb4</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Connection Pool</span>
            <span class="health-info-val">Active (10 connections)</span>
          </div>
        </div>

        <!-- Node.js API Status -->
        <div class="health-card">
          <div class="health-card-header">
            <div class="health-card-title">
              <i class="fa-brands fa-node-js" style="color:#16a34a;"></i> Node.js REST API
            </div>
            <span class="chip chip-success">Healthy</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">URL</span>
            <span class="health-info-val">http://127.0.0.1:5000</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Health Endpoint</span>
            <span class="health-info-val"><a href="http://127.0.0.1:5000/api/health" target="_blank" style="color:var(--info);">/api/health</a></span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Security Guard</span>
            <span class="health-info-val">X-Internal-API-Key</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Auth System</span>
            <span class="health-info-val">JWT & bcryptjs</span>
          </div>
        </div>

        <!-- PHP Frontend Status -->
        <div class="health-card">
          <div class="health-card-header">
            <div class="health-card-title">
              <i class="fa-brands fa-php" style="color:#6366f1;"></i> PHP Web Server
            </div>
            <span class="chip chip-success">Active</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Host & Port</span>
            <span class="health-info-val">127.0.0.1:8000</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">PHP Version</span>
            <span class="health-info-val"><?= htmlspecialchars(PHP_VERSION) ?></span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Transport Stream</span>
            <span class="health-info-val">Native HTTP Stream (<?= extension_loaded('curl') ? 'cURL' : 'file_get_contents' ?>)</span>
          </div>
          <div class="health-info-row">
            <span class="health-info-label">Session Storage</span>
            <span class="health-info-val">Active (Admin Authenticated)</span>
          </div>
        </div>
      </div>

      <!-- Database Tables Breakdown -->
      <div style="margin-top:28px;">
        <h3 style="font-size:15px; font-weight:800; margin-bottom:14px; color:var(--text-main);">
          <i class="fa-solid fa-table-list" style="margin-right:6px; color:var(--primary);"></i> MariaDB Tables & Row Counts
        </h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:14px;">
          <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">listings</div>
            <div style="font-size:22px; font-weight:800; color:var(--primary);"><?= count($listings) ?></div>
          </div>
          <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">categories</div>
            <div style="font-size:22px; font-weight:800; color:var(--info);"><?= count($categories) ?></div>
          </div>
          <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">locations</div>
            <div style="font-size:22px; font-weight:800; color:#8b5cf6;"><?= count($locations) ?></div>
          </div>
          <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">blogs</div>
            <div style="font-size:22px; font-weight:800; color:#10b981;"><?= count($blogs) ?></div>
          </div>
          <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">form_submissions</div>
            <div style="font-size:22px; font-weight:800; color:#f59e0b;"><?= count($submissions) ?></div>
          </div>
          <div style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">users</div>
            <div style="font-size:22px; font-weight:800; color:#ec4899;"><?= count($users) ?></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
