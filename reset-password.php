<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/form-store.php';
require_once __DIR__ . '/includes/api-client.php';
require_once __DIR__ . '/includes/data.php';

$token    = trim((string)($_GET['token'] ?? ''));
$validate = $token ? pov_password_reset_validate($token) : ['valid' => false, 'message' => 'No reset token provided.'];

$success  = false;
$error    = '';
$done     = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $postToken   = trim((string)($_POST['token'] ?? ''));
  $newPass     = (string)($_POST['new_password'] ?? '');
  $confirmPass = (string)($_POST['confirm_password'] ?? '');

  if ($newPass !== $confirmPass) {
    $error = 'Passwords do not match.';
  } elseif (strlen($newPass) < 6) {
    $error = 'Password must be at least 6 characters.';
  } else {
    $result = pov_password_reset_complete($postToken, $newPass);
    if ($result['success']) {
      $success = true;
      $done    = true;
    } else {
      $error = $result['message'];
    }
  }
}

$page_title       = 'Reset Password — POV Indian';
$page_description = 'Set a new password for your POV Indian account.';
include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>
<main style="min-height: 80vh; display:flex; align-items:center; justify-content:center; padding: 40px 16px; background:#f8fafc;">
  <div style="width:100%; max-width:460px;">

    <!-- Logo -->
    <div style="text-align:center; margin-bottom:28px;">
      <a href="<?= htmlspecialchars(pov_url('index.php')) ?>" style="display:inline-flex; align-items:center; gap:10px; text-decoration:none;">
        <div style="width:42px; height:42px; background:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center;">
          <i class="fa-solid fa-location-dot" style="color:#fff; font-size:20px;"></i>
        </div>
        <span style="font-size:22px; font-weight:800; color:#0f172a;">POV <span style="color:var(--primary);">Indian</span></span>
      </a>
    </div>

    <div style="background:#ffffff; border-radius:20px; padding:36px 32px; box-shadow:0 4px 30px rgba(0,0,0,0.08); border:1px solid #f1f5f9;">

      <?php if ($done && $success): ?>
        <!-- SUCCESS STATE -->
        <div style="text-align:center;">
          <div style="width:64px; height:64px; background:#ecfdf5; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">
            <i class="fa-solid fa-check" style="font-size:28px; color:#10b981;"></i>
          </div>
          <h2 style="font-size:22px; font-weight:800; color:#0f172a; margin:0 0 8px;">Password Updated!</h2>
          <p style="color:#64748b; font-size:14px; margin:0 0 24px;">Your password has been reset successfully. You can now sign in with your new password.</p>
          <a href="<?= htmlspecialchars(pov_url('index.php')) ?>#signin" onclick="setTimeout(()=>{ const b=document.querySelector('[data-open-modal]'); if(b) b.click(); }, 300);"
             style="display:inline-flex; align-items:center; gap:8px; background:var(--primary); color:#fff; padding:13px 28px; border-radius:10px; font-weight:700; font-size:14px; text-decoration:none;">
            <i class="fa-solid fa-arrow-right-to-bracket"></i> Go to Sign In
          </a>
        </div>

      <?php elseif (!$validate['valid']): ?>
        <!-- INVALID TOKEN STATE -->
        <div style="text-align:center;">
          <div style="width:64px; height:64px; background:#fee2e2; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size:26px; color:#ef4444;"></i>
          </div>
          <h2 style="font-size:20px; font-weight:800; color:#0f172a; margin:0 0 8px;">Invalid or Expired Link</h2>
          <p style="color:#64748b; font-size:14px; margin:0 0 24px;"><?= htmlspecialchars($validate['message']) ?></p>
          <a href="<?= htmlspecialchars(pov_url('index.php')) ?>"
             style="display:inline-flex; align-items:center; gap:8px; background:#f1f5f9; color:#475569; padding:11px 22px; border-radius:10px; font-weight:600; font-size:14px; text-decoration:none; border:1.5px solid #e2e8f0;">
            <i class="fa-solid fa-house"></i> Back to Home
          </a>
        </div>

      <?php else: ?>
        <!-- RESET FORM -->
        <div style="text-align:center; margin-bottom:24px;">
          <div style="width:54px; height:54px; background:#ffeeea; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px;">
            <i class="fa-solid fa-key" style="font-size:22px; color:var(--primary);"></i>
          </div>
          <h2 style="font-size:21px; font-weight:800; color:#0f172a; margin:0 0 6px;">Set New Password</h2>
          <p style="color:#64748b; font-size:13.5px; margin:0;">
            For <strong><?= htmlspecialchars($validate['email']) ?></strong>
          </p>
        </div>

        <?php if ($error): ?>
          <div style="background:#fee2e2; color:#dc2626; border-radius:10px; padding:12px 14px; font-size:13.5px; font-weight:500; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="" id="resetForm" onsubmit="return validateResetForm()">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />

          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">New Password</label>
            <div style="position:relative;">
              <input type="password" name="new_password" id="newPass" required minlength="6"
                placeholder="Min. 6 characters"
                style="width:100%; padding:11px 44px 11px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none; transition:border-color 0.2s; box-sizing:border-box;"
                onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'" />
              <button type="button" onclick="togglePassVis('newPass', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px;">
                <i class="fa-regular fa-eye" style="font-size:15px;"></i>
              </button>
            </div>
            <!-- Password strength bar -->
            <div id="strengthBar" style="height:3px; border-radius:2px; margin-top:6px; background:#e2e8f0; transition:all 0.3s;">
              <div id="strengthFill" style="height:100%; width:0%; border-radius:2px; transition:all 0.3s; background:#ef4444;"></div>
            </div>
            <div id="strengthLabel" style="font-size:11px; color:#94a3b8; margin-top:2px;"></div>
          </div>

          <div style="margin-bottom:22px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Confirm Password</label>
            <div style="position:relative;">
              <input type="password" name="confirm_password" id="confirmPass" required minlength="6"
                placeholder="Re-enter your password"
                style="width:100%; padding:11px 44px 11px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none; transition:border-color 0.2s; box-sizing:border-box;"
                onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'" />
              <button type="button" onclick="togglePassVis('confirmPass', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:4px;">
                <i class="fa-regular fa-eye" style="font-size:15px;"></i>
              </button>
            </div>
            <div id="matchMsg" style="font-size:12px; margin-top:4px;"></div>
          </div>

          <button type="submit" id="resetSubmitBtn"
            style="width:100%; padding:13px; background:var(--primary); color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; transition:background 0.2s; display:flex; align-items:center; justify-content:center; gap:8px;"
            onmouseover="this.style.background='#d42400'" onmouseout="this.style.background='var(--primary)'">
            <i class="fa-solid fa-shield-check"></i> Update Password
          </button>
        </form>
      <?php endif; ?>

    </div>

    <p style="text-align:center; margin-top:20px; font-size:13px; color:#94a3b8;">
      <a href="<?= htmlspecialchars(pov_url('index.php')) ?>" style="color:#64748b; text-decoration:none;">
        <i class="fa-solid fa-arrow-left" style="font-size:11px;"></i> Back to POV Indian
      </a>
    </p>
  </div>
</main>

<script>
// Password visibility toggle
function togglePassVis(fieldId, btn) {
  const field = document.getElementById(fieldId);
  const icon  = btn.querySelector('i');
  if (field.type === 'password') {
    field.type = 'text';
    icon.className = 'fa-regular fa-eye-slash';
    btn.style.color = 'var(--primary)';
  } else {
    field.type = 'password';
    icon.className = 'fa-regular fa-eye';
    btn.style.color = '#94a3b8';
  }
}

// Password strength meter
document.getElementById('newPass')?.addEventListener('input', function() {
  const val = this.value;
  const fill  = document.getElementById('strengthFill');
  const label = document.getElementById('strengthLabel');
  if (!fill || !label) return;

  let score = 0;
  if (val.length >= 6)  score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { pct: '20%', color: '#ef4444', text: 'Very weak' },
    { pct: '40%', color: '#f97316', text: 'Weak' },
    { pct: '60%', color: '#eab308', text: 'Fair' },
    { pct: '80%', color: '#22c55e', text: 'Good' },
    { pct: '100%', color: '#10b981', text: 'Strong' },
  ];
  const lvl = levels[Math.min(score, 4)];
  fill.style.width  = val.length > 0 ? lvl.pct : '0%';
  fill.style.background = lvl.color;
  label.style.color = lvl.color;
  label.textContent = val.length > 0 ? lvl.text : '';
});

// Confirm match checker
document.getElementById('confirmPass')?.addEventListener('input', function() {
  const pass    = document.getElementById('newPass')?.value;
  const matchEl = document.getElementById('matchMsg');
  if (!matchEl) return;
  if (this.value === '') { matchEl.textContent = ''; return; }
  if (this.value === pass) {
    matchEl.style.color = '#10b981';
    matchEl.innerHTML   = '<i class="fa-solid fa-check"></i> Passwords match';
    this.style.borderColor = '#10b981';
  } else {
    matchEl.style.color = '#ef4444';
    matchEl.innerHTML   = '<i class="fa-solid fa-xmark"></i> Passwords do not match';
    this.style.borderColor = '#ef4444';
  }
});

function validateResetForm() {
  const p1 = document.getElementById('newPass')?.value;
  const p2 = document.getElementById('confirmPass')?.value;
  if (p1 !== p2) { alert('Passwords do not match.'); return false; }
  if (p1.length < 6) { alert('Password must be at least 6 characters.'); return false; }
  const btn = document.getElementById('resetSubmitBtn');
  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...'; }
  return true;
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
