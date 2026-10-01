<?php
/**
 * Admin Tab: Blogs
 */
?>
<section class="tab-section <?= $activeTab === 'blogs' ? 'active' : '' ?>" id="section-blogs">
  <div class="card-panel">
    <div class="card-panel-header">
      <h2 class="card-panel-title">
        <i class="fa-solid fa-newspaper" style="color:var(--primary);"></i> Blog Articles & Guides (<?= count($blogs) ?>)
      </h2>
      <div class="card-panel-actions">
        <input type="text" class="search-filter-input" placeholder="Search blogs..." onkeyup="filterTable(this, 'table-blogs')" />
        <button type="button" class="btn-topbar btn-topbar-primary" onclick="openModal('addBlogModal')">
          <i class="fa-solid fa-plus"></i> Publish Article
        </button>
      </div>
    </div>
    <div class="table-container">
      <table class="data-table" id="table-blogs">
        <thead>
          <tr>
            <th>Cover</th>
            <th>Article Title</th>
            <th>Tag</th>
            <th>Author</th>
            <th>Published Date</th>
            <th>Read Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($blogs)): ?>
            <tr><td colspan="7" class="empty-state-box">No blog posts in database yet.</td></tr>
          <?php else: ?>
            <?php foreach ($blogs as $b): ?>
              <tr>
                <td>
                  <img src="<?= htmlspecialchars(pov_dash_blog_img($b['img'] ?? '')) ?>" class="media-thumb" alt="cover" onerror="this.src='<?= htmlspecialchars(pov_url('assets/img/blog/hotel.webp')) ?>'" />
                </td>
                <td>
                  <div style="font-weight:700; font-size:14px;"><?= htmlspecialchars($b['title']) ?></div>
                  <div style="font-size:12px; color:var(--text-muted); max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    <?= htmlspecialchars($b['excerpt'] ?? '') ?>
                  </div>
                </td>
                <td><span class="chip chip-primary"><?= htmlspecialchars($b['tag'] ?? 'General') ?></span></td>
                <td style="font-weight:600;"><?= htmlspecialchars($b['author'] ?? 'Admin') ?></td>
                <td style="color:var(--text-muted); font-size:12.5px;"><?= htmlspecialchars($b['date'] ?? 'Recent') ?></td>
                <td><span class="chip chip-slate"><?= htmlspecialchars($b['read'] ?? '5 min') ?></span></td>
                <td><span class="chip chip-success">Published</span></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
