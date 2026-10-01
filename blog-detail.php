<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$slug = $_GET['slug'] ?? '';
$post = pov_find_post($slug);
$current_page = 'blog';
$body_class = 'inner-page';

if (!$post) {
  $page_title = 'Article not found | POV Indian';
  $page_description = 'This article could not be found.';
  $hero_eyebrow = 'Blog';
  $hero_title = 'Article not found';
  $hero_sub = 'The article you requested is unavailable.';
  $breadcrumbs = ['Blog' => 'blog.php', 'Not found' => null];
  include __DIR__ . '/includes/head.php';
  include __DIR__ . '/includes/header.php';
  $hero_image = 'assets/img/heroes/blog.jpg';
  include __DIR__ . '/includes/page-hero.php';
  ?>
  <main class="page-section">
    <div class="container">
      <div class="content-card" style="max-width:640px;">
        <p>We could not find that article. Browse the latest India insights instead.</p>
        <p style="margin-top:18px;"><a class="btn" href="<?= htmlspecialchars(pov_url('blog.php')) ?>">Back to Blog</a></p>
      </div>
    </div>
  </main>
  <?php
  include __DIR__ . '/includes/footer.php';
  exit;
}

$page_title = $post['title'] . ' | POV Indian';
$page_description = $post['excerpt'];
$hero_eyebrow = $post['tag'];
$hero_title = $post['title'];
$hero_sub = $post['author'] . ' · ' . $post['date'] . ' · ' . $post['read'];
$breadcrumbs = ['Blog' => 'blog.php', $post['title'] => null];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/blog/' . $post['img'];
include __DIR__ . '/includes/page-hero.php';
$paragraphs = preg_split('/\n\n+/', trim($post['body']));
?>
<main class="page-section">
  <div class="container legal-wrap">
    <img class="blog-detail-hero-img" src="<?= htmlspecialchars(pov_url('assets/img/blog/' . $post['img'])) ?>" alt="<?= htmlspecialchars($post['title']) ?>" />
    <article class="content-card prose">
      <div class="article-meta">
        <span class="tag" style="color:var(--primary);font-weight:600;"><?= htmlspecialchars($post['tag']) ?></span>
        <span><?= htmlspecialchars($post['date']) ?></span>
        <span><?= htmlspecialchars($post['author']) ?></span>
        <span><?= htmlspecialchars($post['read']) ?></span>
      </div>
      <?php foreach ($paragraphs as $p): ?>
        <p><?= htmlspecialchars($p) ?></p>
      <?php endforeach; ?>
      <p><a class="btn btn-outline" href="<?= htmlspecialchars(pov_url('blog.php')) ?>">Back to Blog</a></p>
    </article>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
