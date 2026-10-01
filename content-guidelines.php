<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
$current_page = 'pages';
$page_title = 'Content Guidelines | POV Indian';
$page_description = 'Editorial standards for contributors publishing on POV Indian.';
$body_class = 'inner-page';
$hero_eyebrow = 'Legal';
$hero_title = 'Content Guidelines';
$hero_sub = 'How we keep stories, guides, and diaries authentic, useful, and respectful.';
$breadcrumbs = [
  'Content Guidelines' => null
];
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
$hero_image = 'assets/img/heroes/legal.jpg';
include __DIR__ . '/includes/page-hero.php';
?>

<main class="page-section">
  <div class="container legal-wrap">
    <article class="content-card prose">
      <h2>Content Guidelines</h2>
      <p>POV Indian publishes first-person perspectives grounded in lived experience. These rules apply to pitches, published articles, and community submissions.</p>

      <h3>What we look for</h3>
      <ul>
        <li>Original writing based on real visits, study, work, or local knowledge</li>
        <li>Practical detail readers can use (routes, timing, costs, etiquette)</li>
        <li>Clear voice — honest about trade-offs, not only highlights</li>
      </ul>

      <h3>Attribution &amp; originality</h3>
      <p>Do not plagiarise. Quote sparingly and credit sources. AI-assisted drafting is fine for structure, but facts and experiences must be yours to stand behind. Disclose sponsored trips or paid partnerships.</p>

      <h3>Respect &amp; safety</h3>
      <ul>
        <li>No hate speech, harassment, or stereotyping of communities</li>
        <li>Do not reveal private individuals without consent</li>
        <li>Avoid directions that endanger people, wildlife, or fragile sites</li>
      </ul>

      <h3>Editing</h3>
      <p>Our editors may lightly edit for clarity, length, and house style while preserving your POV. We may decline or unpublish work that breaks these guidelines or our <a href="<?= htmlspecialchars(pov_url('terms.php')) ?>">Terms of Service</a>.</p>

      <p>Ready to pitch? Visit <a href="<?= htmlspecialchars(pov_url('contributor.php')) ?>">Become a Contributor</a>.</p>
    </article>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
