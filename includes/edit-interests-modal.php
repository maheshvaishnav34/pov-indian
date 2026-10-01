<?php
/**
 * edit-interests-modal.php
 * Modal dialog allowing logged-in members to view and update their personalized category interests.
 */
if (!defined('POV_ROOT')) {
  require_once __DIR__ . '/config.php';
}
require_once __DIR__ . '/data.php';

$activeUser = $_SESSION['auth_user'] ?? $_SESSION['user'] ?? null;
$userInterests = pov_get_current_user_interests();
$allCategories = $POV_CATEGORIES ?? [];
?>
<div class="modal" id="editInterestsModal" role="dialog" aria-modal="true" aria-labelledby="editInterestsTitle">
  <div class="modal-backdrop" data-close-modal></div>
  <div class="modal-panel modal-panel-lg" style="max-width: 580px;">
    <button class="modal-close" type="button" data-close-modal aria-label="Close">×</button>

    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
      <div style="width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg, rgba(255,107,0,0.12), rgba(255,107,0,0.25)); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:20px;">
        <i class="fa-solid fa-sliders"></i>
      </div>
      <div>
        <h3 id="editInterestsTitle" style="margin:0 0 2px; font-size:20px; font-weight:700;">Personalize Your Feed</h3>
        <p class="modal-lead" style="margin:0; font-size:13px; color:#64748b;">
          Select the categories you care about. Your home feed and listings will only display matching places.
        </p>
      </div>
    </div>

    <form id="editInterestsForm" method="post" action="<?= htmlspecialchars(pov_url('form-submit.php')) ?>" style="margin-top:16px;">
      <input type="hidden" name="form_type" value="update_interests" />
      <input type="hidden" name="redirect" value="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'] ?? 'index.php') . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')) ?>" />

      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding:0 2px;">
        <span style="font-weight:600; font-size:13px; color:var(--ink);">Categories (<?= count($allCategories) ?> available)</span>
        <button type="button" onclick="toggleSelectAllInterests(this)" style="background:none; border:none; color:var(--primary); font-size:12.5px; font-weight:600; cursor:pointer; padding:0;">
          Select All
        </button>
      </div>

      <div class="interest-chips" id="editModalChipsGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:10px; max-height:280px; overflow-y:auto; padding:4px; margin-bottom:16px;">
        <?php foreach ($allCategories as $cat): ?>
          <?php
            $isAssigned = false;
            foreach ($userInterests as $uInt) {
              if (pov_category_matches_interest($cat, $uInt)) {
                $isAssigned = true;
                break;
              }
            }
            $catVal = $cat['name'] ?? $cat['slug'] ?? '';
          ?>
          <label style="
            display:flex; align-items:center; gap:10px; padding:10px 14px;
            background:<?= $isAssigned ? '#fff7ed' : '#f8fafc' ?>;
            border:1.5px solid <?= $isAssigned ? 'var(--primary)' : '#e2e8f0' ?>;
            border-radius:12px; cursor:pointer; transition:all 0.18s ease; user-select:none;
          " onmouseover="if(!this.querySelector('input').checked) this.style.borderColor='#cbd5e1';"
             onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e2e8f0';">
            <input
              type="checkbox"
              name="interest[]"
              value="<?= htmlspecialchars($catVal) ?>"
              <?= $isAssigned ? 'checked' : '' ?>
              style="accent-color:var(--primary); width:16px; height:16px; margin:0; cursor:pointer;"
              onchange="
                if(this.checked) {
                  this.closest('label').style.background='#fff7ed';
                  this.closest('label').style.borderColor='var(--primary)';
                } else {
                  this.closest('label').style.background='#f8fafc';
                  this.closest('label').style.borderColor='#e2e8f0';
                }
              "
            />
            <span style="font-size:13px; font-weight:600; color:var(--ink);">
              <?= htmlspecialchars($cat['name']) ?>
            </span>
          </label>
        <?php endforeach; ?>
      </div>

      <div id="editInterestError" style="display:none; color:#dc2626; font-size:12.5px; margin-bottom:12px; font-weight:600;">
        <i class="fa-solid fa-circle-exclamation"></i> Please select at least one category to continue.
      </div>

      <div class="modal-actions" style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
        <button class="btn btn-outline" type="button" data-close-modal>Cancel</button>
        <button class="btn" type="submit" style="display:inline-flex; align-items:center; gap:8px;">
          <i class="fa-solid fa-check"></i> Save Preferences
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleSelectAllInterests(btn) {
  const container = document.getElementById('editModalChipsGrid');
  if (!container) return;
  const checkboxes = container.querySelectorAll('input[type="checkbox"]');
  const allChecked = Array.from(checkboxes).every(cb => cb.checked);
  
  checkboxes.forEach(cb => {
    cb.checked = !allChecked;
    const label = cb.closest('label');
    if (label) {
      label.style.background = !allChecked ? '#fff7ed' : '#f8fafc';
      label.style.borderColor = !allChecked ? 'var(--primary)' : '#e2e8f0';
    }
  });
  
  btn.textContent = allChecked ? 'Select All' : 'Deselect All';
}

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('editInterestsForm');
  if (form) {
    form.addEventListener('submit', function (e) {
      const checked = form.querySelectorAll('input[name="interest[]"]:checked');
      const errBox = document.getElementById('editInterestError');
      if (checked.length === 0) {
        e.preventDefault();
        if (errBox) errBox.style.display = 'block';
        return false;
      }
      if (errBox) errBox.style.display = 'none';
    });
  }
});
</script>
