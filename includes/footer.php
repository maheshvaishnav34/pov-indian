<?php ?>
<footer class="site-footer" id="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand footer-col">
      <a href="<?= htmlspecialchars(pov_url('index.php')) ?>"><img src="<?= htmlspecialchars(pov_url('assets/img/ui/pov-logo-light.svg')) ?>" alt="POV Indian" style="height:42px;" /></a>
      <p>POV Indian is a platform dedicated to showcasing authentic perspectives on India through verified business listings and insightful content created by locals who know best.</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <a href="<?= htmlspecialchars(pov_url('index.php')) ?>">Home</a>
      <a href="<?= htmlspecialchars(pov_url('category-hotels.php')) ?>">Hotels & Accommodations</a>
      <a href="<?= htmlspecialchars(pov_url('listings.php')) ?>">Business Listings</a>
      <a href="<?= htmlspecialchars(pov_url('blog.php')) ?>">India Insights Blog</a>
      <a href="<?= htmlspecialchars(pov_url('about.php')) ?>">About Us</a>
      <a href="<?= htmlspecialchars(pov_url('contact.php')) ?>">Contact</a>
      <a href="<?= htmlspecialchars(pov_url('list-business.php')) ?>">List Your Business</a>
      <a href="<?= htmlspecialchars(pov_url('contributor.php')) ?>">Become a Contributor</a>
    </div>
    <div class="footer-col">
      <h4>Legal</h4>
      <a href="<?= htmlspecialchars(pov_url('terms.php')) ?>">Terms of Service</a>
      <a href="<?= htmlspecialchars(pov_url('privacy.php')) ?>">Privacy Policy</a>
      <a href="<?= htmlspecialchars(pov_url('cookies.php')) ?>">Cookie Policy</a>
      <a href="<?= htmlspecialchars(pov_url('listing-guidelines.php')) ?>">Listing Guidelines</a>
      <a href="<?= htmlspecialchars(pov_url('content-guidelines.php')) ?>">Content Guidelines</a>
      <a href="<?= htmlspecialchars(pov_url('careers.php')) ?>">Careers</a>
    </div>
    <div class="footer-col">
      <h4>Stay Updated</h4>
      <p>Subscribe to our newsletter for the latest insights and updates about India.</p>
      <form class="subscribe-form" id="subscribeForm" method="post" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>">
        <input type="hidden" name="form_type" value="subscribe" />
        <input type="hidden" name="redirect" value="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'] ?? 'index.php')) ?>" />
        <input type="email" name="email" placeholder="Email address" required />
        <button class="btn" type="submit">Subscribe</button>
      </form>
    </div>
  </div>
  <div class="container footer-bottom">
    <p>Made with love in India · © <?= date('Y') ?> POV Indian. All rights reserved.</p>
    <div class="socials">
      <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
      <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
      <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
    </div>
  </div>
</footer>

<?php include __DIR__ . '/signup-modal.php'; ?>
<?php include __DIR__ . '/edit-interests-modal.php'; ?>
<div class="toast" role="status" aria-live="polite"></div>
<?php
require_once __DIR__ . '/form-store.php';
$flash_ok = pov_flash_get('form_success');
$flash_err = pov_flash_get('form_error');
?>
<script src="<?= htmlspecialchars(pov_url('js/main.js')) ?>?v=20260724e"></script>
<?php if ($flash_ok || $flash_err): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toast = document.querySelector('.toast');
  if (!toast) return;
  toast.textContent = <?= json_encode($flash_ok ?: $flash_err) ?>;
  <?php if ($flash_err): ?>
  toast.classList.add('toast-error');
  <?php else: ?>
  toast.classList.add('toast-success');
  <?php endif; ?>
  toast.classList.add('show');
  setTimeout(function () { toast.classList.remove('show'); }, <?= $flash_err ? 4000 : 3200 ?>);
});
</script>
<?php endif; ?>
</body>
</html>
