<?php
require_once __DIR__ . '/form-store.php';
require_once __DIR__ . '/data.php';
$signupCategories = function_exists('pov_get_signup_categories') ? pov_get_signup_categories() : [];
?>
<div class="modal" id="signupModal" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
  <div class="modal-backdrop" data-close-modal></div>
  <div class="modal-panel modal-panel-lg">
    <button class="modal-close" type="button" data-close-modal aria-label="Close">×</button>



    <!-- Sign In Pane (Login) -->
    <div class="auth-pane is-active" id="authPaneSignin">
      <h3 id="authModalTitle">Welcome Back</h3>
      <p class="modal-lead">Sign in to access your POV Indian account & dashboard.</p>
      
      <div class="social-login">
        <button class="btn btn-outline" type="button" data-social="Google"><i class="fa-brands fa-google"></i> Google</button>
        <button class="btn btn-outline" type="button" data-social="Facebook"><i class="fa-brands fa-facebook-f"></i> Facebook</button>
      </div>
      <p class="or-line">or sign in with email</p>

      <form id="signinForm" method="post" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>">
        <input type="hidden" name="form_type" value="login" />
        <input type="hidden" name="redirect" value="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'] ?? 'index.php')) ?>" />

        <div class="form-row">
          <label for="signinEmail">Email</label>
          <input id="signinEmail" name="email" type="email" placeholder="admin@povindian.com" required />
        </div>

        <div class="form-row">
          <label for="signinPassword">Password</label>
          <input id="signinPassword" name="password" type="password" placeholder="••••••••••••" required />
        </div>

        <div class="form-meta">
          <label><input type="checkbox" name="remember" checked /> Remember me</label>
          <a href="#" onclick="switchAuthPane('forgot'); return false;" style="color:var(--primary); font-weight:600;">Forgot Password?</a>
        </div>

        <div class="modal-actions">
          <button class="btn btn-outline" type="button" data-close-modal>Cancel</button>
          <button class="btn" type="submit">Sign In</button>
        </div>

        <p style="text-align: center; margin: 16px 0 0; font-size: 13px; color: #64748b;">
          Don't have an account?
          <a href="#" data-switch-auth="signup" style="color: var(--primary); font-weight: 600;">Sign Up</a>
        </p>
      </form>
    </div>

    <!-- Forgot Password Pane -->
    <div class="auth-pane" id="authPaneForgot">
      <div style="text-align:center; margin-bottom:18px;">
        <div style="width:54px; height:54px; background:#ffeeea; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px;">
          <i class="fa-solid fa-lock-open" style="font-size:22px; color:var(--primary);"></i>
        </div>
        <h3 style="margin:0 0 6px; font-size:20px;">Reset Password</h3>
        <p class="modal-lead" style="margin:0;">Enter your registered email and we'll generate a reset link for you.</p>
      </div>

      <!-- Step 1: Email Input -->
      <div id="forgotStep1">
        <form id="forgotForm" onsubmit="handleForgotPassword(event)">
          <div class="form-row">
            <label for="forgotEmail">Email Address</label>
            <input id="forgotEmail" name="email" type="email" placeholder="you@example.com" required autocomplete="email" />
          </div>
          <div id="forgotError" style="display:none; background:#fee2e2; color:#dc2626; border-radius:8px; padding:10px 14px; font-size:13px; margin-bottom:12px;"></div>
          <div class="modal-actions" style="margin-top:4px;">
            <button type="button" class="btn btn-outline" onclick="switchAuthPane('signin')">Back to Sign In</button>
            <button type="submit" class="btn" id="forgotSubmitBtn">Send Reset Link</button>
          </div>
        </form>
      </div>

      <!-- Step 2: Reset Link Result -->
      <div id="forgotStep2" style="display:none;">
        <div id="forgotSuccessBox" style="background:#ecfdf5; border:1.5px solid #a7f3d0; border-radius:12px; padding:18px 16px; margin-bottom:16px;">
          <div style="font-weight:700; color:#065f46; margin-bottom:8px; font-size:14px;">
            <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i> Reset link is ready!
          </div>
          <p style="font-size:13px; color:#047857; margin:0 0 12px;">Click the button below to set your new password. This link expires in <strong>1 hour</strong>.</p>
          <a id="forgotResetLink" href="#" target="_blank" style="display:inline-flex; align-items:center; gap:8px; background:var(--primary); color:#fff; padding:10px 18px; border-radius:8px; font-weight:600; font-size:13.5px; text-decoration:none; transition:all 0.2s;">
            <i class="fa-solid fa-key"></i> Open Reset Page
          </a>
        </div>
        <div style="font-size:12px; color:#64748b; text-align:center;">
          Wrong email? <a href="#" onclick="showForgotStep1(); return false;" style="color:var(--primary); font-weight:600;">Try again</a>
        </div>
      </div>

      <!-- Email not found -->
      <div id="forgotStep3" style="display:none; text-align:center;">
        <div style="background:#fef3c7; border:1.5px solid #fbbf24; border-radius:12px; padding:18px 16px; margin-bottom:16px;">
          <i class="fa-solid fa-envelope-circle-check" style="font-size:28px; color:#d97706; margin-bottom:10px; display:block;"></i>
          <p style="font-size:13.5px; color:#92400e; margin:0;">If this email is registered, a reset link has been sent. Please check your inbox.</p>
        </div>
        <button type="button" class="btn btn-outline" onclick="switchAuthPane('signin')" style="width:100%;">Back to Sign In</button>
      </div>
    </div>

    <!-- Sign Up Pane (Register) -->
    <div class="auth-pane" id="authPaneSignup">
      <h3>Join Our Community</h3>
      <p class="modal-lead">Connect with locals, share experiences, and get exclusive access to authentic Indian insights.</p>
      
      <div class="social-login">
        <button class="btn btn-outline" type="button" data-social="Google"><i class="fa-brands fa-google"></i> Google</button>
        <button class="btn btn-outline" type="button" data-social="Facebook"><i class="fa-brands fa-facebook-f"></i> Facebook</button>
      </div>
      <p class="or-line">or register with email</p>

      <form id="signupForm" method="post" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>">
        <input type="hidden" name="form_type" value="signup" />
        <input type="hidden" name="redirect" value="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'] ?? 'index.php')) ?>" />
        
        <div class="form-row">
          <label for="fullName">Full Name</label>
          <input id="fullName" name="full_name" type="text" placeholder="Your name" required />
        </div>

        <div class="form-row">
          <label for="signupEmail">Email</label>
          <input id="signupEmail" name="email" type="email" placeholder="you@example.com" required />
        </div>

        <div class="form-row">
          <label for="signupPassword">Password</label>
          <input id="signupPassword" name="password" type="password" placeholder="Create a password" required />
        </div>

        <div class="form-row">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <label style="font-weight:600; font-size:13.5px; color:var(--ink); margin:0;">
              Your Categories of Interest <span style="color:#ef4444;">*</span>
            </label>
            <small style="color:var(--muted); font-size:12px;">Select at least 1</small>
          </div>
          <div class="interest-chips" id="signupInterestChips" style="display:flex; flex-wrap:wrap; gap:8px; max-height:160px; overflow-y:auto; padding:4px 2px;">
            <?php foreach ($signupCategories as $scat): ?>
              <?php $catVal = $scat['name'] ?? $scat['slug'] ?? ''; ?>
              <label class="interest-chip">
                <input type="checkbox" name="interest[]" value="<?= htmlspecialchars($catVal) ?>" onchange="this.closest('label').classList.toggle('is-selected', this.checked);" />
                <span><?= htmlspecialchars($scat['name']) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
          <div id="signupInterestError" style="display:none; color:#dc2626; font-size:12px; margin-top:6px; font-weight:600;">
            <i class="fa-solid fa-circle-exclamation"></i> Please select at least one category to personalize your experience.
          </div>
        </div>

        <div class="form-meta">
          <label><input type="checkbox" required /> I agree to the <a href="<?= htmlspecialchars(pov_url('terms.php')) ?>">Terms of Service</a> and <a href="<?= htmlspecialchars(pov_url('privacy.php')) ?>">Privacy Policy</a></label>
        </div>

        <div class="modal-actions">
          <button class="btn btn-outline" type="button" data-close-modal>Cancel</button>
          <button class="btn" type="submit">Sign Up</button>
        </div>

        <p style="text-align: center; margin: 16px 0 0; font-size: 13px; color: #64748b;">
          Already have an account? 
          <a href="#" data-switch-auth="signin" style="color: var(--primary); font-weight: 600;">Sign In</a>
        </p>
      </form>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const signupForm = document.getElementById('signupForm');
  if (signupForm) {
    signupForm.addEventListener('submit', function (e) {
      const checked = signupForm.querySelectorAll('input[name="interest[]"]:checked');
      const errBox = document.getElementById('signupInterestError');
      if (checked.length === 0) {
        e.preventDefault();
        if (errBox) {
          errBox.style.display = 'block';
          errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        return false;
      }
      if (errBox) errBox.style.display = 'none';
    });
  }
});
</script>
