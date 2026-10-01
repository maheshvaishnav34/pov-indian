<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$tag = $_GET['tag'] ?? '';
$posts = $POV_BLOG;
if ($tag !== '') {
  $posts = array_values(array_filter($POV_BLOG, fn($p) => $p['tag_slug'] === $tag));
}
$current_page = 'blog';
$page_title = 'India Insights Blog | POV Indian';
$page_description = 'Authentic stories, insights, and experiences from locals and travelers across India.';
$body_class = 'inner-page';
$hero_eyebrow = 'Discover India by POV';
$hero_title = 'India Insights Blog';
$hero_sub = 'Authentic stories, insights, and experiences from locals and travelers across India.';
$breadcrumbs = ['Blog' => null];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/blog.jpg';
include __DIR__ . '/includes/page-hero.php';
?>
<main class="page-section">
  <div class="container">
    <div class="filter-list" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:28px;">
      <a href="<?= htmlspecialchars(pov_url('blog.php')) ?>">All</a>
      <a href="<?= htmlspecialchars(pov_url('blog.php?tag=hidden-gems')) ?>">Hidden Gems</a>
      <a href="<?= htmlspecialchars(pov_url('blog.php?tag=student-diaries')) ?>">Student Diaries</a>
      <a href="<?= htmlspecialchars(pov_url('blog.php?tag=cultural-insights')) ?>">Cultural Insights</a>
    </div>
    <?php if (!$posts): ?>
      <div class="empty-note">No articles found for this topic yet.</div>
    <?php else: ?>
    <div class="blog-grid">
      <?php foreach ($posts as $post): ?>
        <article class="blog-card">
          <div class="blog-thumb"><img src="<?= htmlspecialchars(pov_url('assets/img/blog/' . $post['img'])) ?>" alt="<?= htmlspecialchars($post['title']) ?>" /></div>
          <div class="blog-body">
            <div class="blog-meta"><span class="tag"><?= htmlspecialchars($post['tag']) ?></span><span><?= htmlspecialchars($post['date']) ?></span></div>
            <h3><a href="<?= htmlspecialchars(pov_url('blog-detail.php?slug=' . urlencode($post['slug']))) ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
            <p><?= htmlspecialchars($post['excerpt']) ?></p>
            <div class="blog-meta" style="margin-top:14px;"><span><?= htmlspecialchars($post['author']) ?></span><span><?= htmlspecialchars($post['read']) ?></span></div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
