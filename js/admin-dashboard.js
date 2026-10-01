// Tab switching controller
const tabTitles = {
  'overview': '<i class="fa-solid fa-gauge-high" style="color:var(--primary);"></i> Overview',
  'listings': '<i class="fa-solid fa-building" style="color:var(--primary);"></i> Directory Listings',
  'rentals': '<i class="fa-solid fa-house-chimney" style="color:var(--primary);"></i> Find a place that fits your life. (Rentals & Stays)',
  'categories': '<i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Categories Management',
  'locations': '<i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> Destinations & Cities',
  'blogs': '<i class="fa-solid fa-newspaper" style="color:var(--primary);"></i> Blog Articles & Guides',
  'inquiries': '<i class="fa-solid fa-envelope-open-text" style="color:var(--primary);"></i> Inquiries & Leads',
  'education': '<i class="fa-solid fa-graduation-cap" style="color:#D97746;"></i> Higher Education & Admissions',
  'edu-institutions': '<i class="fa-solid fa-building-columns" style="color:#C9A96E;"></i> Colleges & Universities Directory',
  'edu-offerings': '<i class="fa-solid fa-graduation-cap" style="color:#C9A96E;"></i> Course Offerings & Fees Matrix',
  'edu-leads': '<i class="fa-solid fa-user-graduate" style="color:#D97746;"></i> Student Admission Leads & Counselling',
  'edu-exams': '<i class="fa-solid fa-pen-clip" style="color:#3B82F6;"></i> Entrance Exams Hub (2026)',
  'edu-scholarships': '<i class="fa-solid fa-award" style="color:#10B981;"></i> Higher Education Scholarships',
  'visits': '<i class="fa-solid fa-calendar-check" style="color:#059669;"></i> Scheduled Visits',
  'users': '<i class="fa-solid fa-users" style="color:var(--primary);"></i> User Management',
  'system': '<i class="fa-solid fa-server" style="color:var(--primary);"></i> Database & System Health'
};

function switchEduSection(subtab, el) {
  // Hide all sections
  document.querySelectorAll('.tab-section').forEach(sec => sec.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));

  const targetSec = document.getElementById('section-education');
  if (targetSec) targetSec.classList.add('active');

  if (el) {
    el.classList.add('active');
  } else {
    const nav = document.getElementById('navItemEdu' + subtab.charAt(0).toUpperCase() + subtab.slice(1));
    if (nav) nav.classList.add('active');
  }

  // Switch subtab inside education
  if (typeof switchEduSubtab === 'function') {
    switchEduSubtab(subtab);
  }

  const titleKey = 'edu-' + subtab;
  const heading = document.getElementById('pageTitleHeading');
  if (heading && tabTitles[titleKey]) {
    heading.innerHTML = tabTitles[titleKey];
  } else if (heading && tabTitles['education']) {
    heading.innerHTML = tabTitles['education'];
  }

  if (history.pushState) {
    history.pushState(null, null, '#edu-' + subtab);
  }

  const main = document.querySelector('.admin-main');
  if (main) main.scrollTo({ top: 0, behavior: 'smooth' });
}

function switchTab(tabId) {
  if (tabId.startsWith('edu-')) {
    const sub = tabId.replace('edu-', '');
    const actualSub = (sub === 'colleges') ? 'institutions' : (sub === 'courses' ? 'offerings' : sub);
    switchEduSection(actualSub);
    return;
  }

  // Hide all sections
  document.querySelectorAll('.tab-section').forEach(sec => sec.classList.remove('active'));
  // Remove active from nav items
  document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));

  if (tabId === 'rentals') {
    const targetSec = document.getElementById('section-listings');
    if (targetSec) targetSec.classList.add('active');

    switchListingsSubtab('rentals');

    const navRent = document.getElementById('navItemRentals');
    if (navRent) navRent.classList.add('active');
  } else if (tabId === 'listings') {
    const targetSec = document.getElementById('section-listings');
    if (targetSec) targetSec.classList.add('active');

    switchListingsSubtab('directory');

    const navDir = document.getElementById('navItemDirectory');
    if (navDir) navDir.classList.add('active');
  } else {
    // Show selected section
    const targetSec = document.getElementById('section-' + tabId);
    if (targetSec) {
      targetSec.classList.add('active');
    }

    // Highlight active nav item
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
      if (item.getAttribute('onclick') && item.getAttribute('onclick').includes("'" + tabId + "'")) {
        item.classList.add('active');
      }
    });
  }

  // Update heading
  const heading = document.getElementById('pageTitleHeading');
  if (heading && tabTitles[tabId]) {
    heading.innerHTML = tabTitles[tabId];
  }

  // Update URL hash
  if (history.pushState) {
    history.pushState(null, null, '#' + tabId);
  } else {
    location.hash = '#' + tabId;
  }

  // Scroll to top of main
  const main = document.querySelector('.admin-main');
  if (main) {
    main.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

// Modal controllers
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
  }
}

function handleBackdropClick(event, modalId) {
  if (event.target === document.getElementById(modalId)) {
    closeModal(modalId);
  }
}

// Live search filter in data tables
function filterTable(input, tableId) {
  const query = input.value.toLowerCase();
  const table = document.getElementById(tableId);
  if (!table) return;

  const rows = table.querySelectorAll('tbody tr');
  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
}

// Keyboard ESC to close modal
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-backdrop.show').forEach(m => m.classList.remove('show'));
    document.body.style.overflow = '';
  }
});

// Auto-select tab from URL hash on load
window.addEventListener('DOMContentLoaded', () => {
  const hash = window.location.hash.replace('#', '');
  if (hash && tabTitles[hash]) {
    switchTab(hash);
  }
});

// Live Image and Logo Preview in Add Listing Modal
function povUpdateListingPreview() {
  const coverInput = document.getElementById('addListingImage');
  const logoInput = document.getElementById('addListingAvatar');
  const coverImg = document.getElementById('previewCoverImg');
  const logoImg = document.getElementById('previewLogoImg');
  if (!coverImg || !logoImg) return;

  const baseHref = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));

  let coverVal = (coverInput ? coverInput.value.trim() : '');
  if (!coverVal) coverVal = 'realestate_03.webp';
  if (/^(https?:)?\/\//i.test(coverVal) || coverVal.startsWith('data:image')) {
    coverImg.src = coverVal;
  } else if (coverVal.startsWith('assets/')) {
    coverImg.src = baseHref + '/' + coverVal.replace(/^\/+/, '');
  } else {
    coverImg.src = baseHref + '/assets/img/listings/' + coverVal.replace(/^\/+/, '');
  }

  let logoVal = (logoInput ? logoInput.value.trim() : '');
  if (!logoVal) logoVal = 'ryan.webp';
  if (/^(https?:)?\/\//i.test(logoVal) || logoVal.startsWith('data:image')) {
    logoImg.src = logoVal;
  } else if (logoVal.startsWith('assets/')) {
    logoImg.src = baseHref + '/' + logoVal.replace(/^\/+/, '');
  } else {
    logoImg.src = baseHref + '/assets/img/avatars/' + logoVal.replace(/^\/+/, '');
  }
}

// Hook into openModal to initialize preview
const _origOpenModal = typeof openModal === 'function' ? openModal : null;
if (_origOpenModal) {
  openModal = function(modalId) {
    _origOpenModal(modalId);
    if (modalId === 'addListingModal') {
      povUpdateListingPreview();
    }
  };
}

// ================= Listings Subtab Switcher (Directory vs Rentals & Stays) =================
function switchListingsSubtab(subtab) {
  const dirPanel = document.getElementById('panel-directory-listings');
  const rentPanel = document.getElementById('panel-rentals-listings');
  const btnDir = document.getElementById('subtabBtnDirectory');
  const btnRent = document.getElementById('subtabBtnRentals');
  const navDir = document.getElementById('navItemDirectory');
  const navRent = document.getElementById('navItemRentals');
  const heading = document.getElementById('pageTitleHeading');

  if (subtab === 'rentals') {
    if (dirPanel) dirPanel.style.display = 'none';
    if (rentPanel) rentPanel.style.display = 'block';
    if (btnDir) btnDir.classList.remove('active');
    if (btnRent) btnRent.classList.add('active');
    if (navDir) navDir.classList.remove('active');
    if (navRent) navRent.classList.add('active');
    if (heading && tabTitles['rentals']) heading.innerHTML = tabTitles['rentals'];
    if (history.pushState) history.pushState(null, null, '#rentals');
  } else {
    if (dirPanel) dirPanel.style.display = 'block';
    if (rentPanel) rentPanel.style.display = 'none';
    if (btnDir) btnDir.classList.add('active');
    if (btnRent) btnRent.classList.remove('active');
    if (navDir) navDir.classList.add('active');
    if (navRent) navRent.classList.remove('active');
    if (heading && tabTitles['listings']) heading.innerHTML = tabTitles['listings'];
    if (history.pushState) history.pushState(null, null, '#listings');
  }
}

// ================= Directory Listing Edit Controller =================
function openEditListingModal(item) {
  if (!item) return;

  const idDisp = document.getElementById('editListingIdDisplay');
  const idInput = document.getElementById('editListingId');
  const titleInput = document.getElementById('editListingTitle');
  const catSelect = document.getElementById('editListingCatId');
  const locSelect = document.getElementById('editListingLocId');
  const locTextInput = document.getElementById('editListingLocText');
  const phoneInput = document.getElementById('editListingPhone');
  const priceInput = document.getElementById('editListingPrice');
  const ratingInput = document.getElementById('editListingRating');
  const badgeSelect = document.getElementById('editListingBadge');
  const imageInput = document.getElementById('editListingImage');
  const avatarInput = document.getElementById('editListingAvatar');
  const descInput = document.getElementById('editListingDesc');

  if (idDisp) idDisp.textContent = item.id || '';
  if (idInput) idInput.value = item.id || '';
  if (titleInput) titleInput.value = item.title || '';
  if (locTextInput) locTextInput.value = item.loc || item.location_text || '';
  if (phoneInput) phoneInput.value = item.phone || '';
  if (priceInput) priceInput.value = item.price || '';
  if (ratingInput) ratingInput.value = item.rating || 5.0;
  if (descInput) descInput.value = item.desc || item.description || '';
  if (imageInput) imageInput.value = item.img || item.image || 'realestate_03.webp';
  if (avatarInput) avatarInput.value = item.avatar || 'ryan.webp';

  // Category matching
  if (catSelect) {
    if (item.category_id) {
      catSelect.value = item.category_id;
    } else if (item.cat) {
      for (let i = 0; i < catSelect.options.length; i++) {
        if (catSelect.options[i].text.toLowerCase() === String(item.cat).toLowerCase()) {
          catSelect.selectedIndex = i;
          break;
        }
      }
    }
  }

  // Location matching
  if (locSelect && item.location_id) {
    locSelect.value = item.location_id;
  }

  // Badge matching
  if (badgeSelect) {
    badgeSelect.value = item.badge || '';
  }

  // SEO fields
  const editMetaTitleInput = document.getElementById('editMetaTitle');
  const editSlugInput = document.getElementById('editSlug');
  const editSchemaSelect = document.getElementById('editSchemaType');
  const editMetaDescInput = document.getElementById('editMetaDesc');
  const editMetaKeywordsInput = document.getElementById('editMetaKeywords');
  const editCanonicalInput = document.getElementById('editCanonicalUrl');
  const editOgImageInput = document.getElementById('editOgImage');

  if (editMetaTitleInput) editMetaTitleInput.value = item.meta_title || '';
  if (editSlugInput) editSlugInput.value = item.slug || '';
  if (editSchemaSelect) editSchemaSelect.value = item.schema_type || 'LocalBusiness';
  if (editMetaDescInput) editMetaDescInput.value = item.meta_description || '';
  if (editMetaKeywordsInput) editMetaKeywordsInput.value = item.meta_keywords || '';
  if (editCanonicalInput) editCanonicalInput.value = item.canonical_url || '';
  if (editOgImageInput) editOgImageInput.value = item.og_image || '';

  povUpdateEditListingPreview();
  povUpdateSeoLive('edit');
  openModal('editListingModal');
}

// ================= Directory Listing SEO Controllers =================
function povToggleSeoPanel(panelId, trigger) {
  const panel = document.getElementById(panelId);
  if (!panel) return;
  const isHidden = (panel.style.display === 'none' || !panel.style.display);
  panel.style.display = isHidden ? 'block' : 'none';
  if (trigger) {
    const icon = trigger.querySelector('.seo-toggle-icon i');
    if (icon) {
      if (isHidden) {
        icon.className = 'fa-solid fa-chevron-up';
      } else {
        icon.className = 'fa-solid fa-chevron-down';
      }
    }
  }
}

function povUpdateSeoLive(prefix) {
  let titleVal = '', descVal = '', slugVal = '', baseTitle = '', baseDesc = '';
  let previewTitleEl, previewSlugEl, previewDescEl, titleCounterEl, descCounterEl;

  if (prefix === 'add') {
    const titleInput = document.getElementById('addListingTitle');
    const descInput = document.getElementById('addListingDesc');
    const metaTitleInput = document.getElementById('addMetaTitle');
    const metaDescInput = document.getElementById('addMetaDesc');
    const slugInput = document.getElementById('addSlug');

    baseTitle = (titleInput && titleInput.value.trim()) ? titleInput.value.trim() : 'Directory Listing Title';
    baseDesc = (descInput && descInput.value.trim()) ? descInput.value.trim() : 'Enter a meta description to preview how this directory listing appears in Google search engine results...';

    titleVal = (metaTitleInput && metaTitleInput.value.trim()) ? metaTitleInput.value.trim() : (baseTitle + ' | POV Indian Verified Directory');
    descVal = (metaDescInput && metaDescInput.value.trim()) ? metaDescInput.value.trim() : baseDesc;
    slugVal = (slugInput && slugInput.value.trim()) ? slugInput.value.trim() : baseTitle.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

    previewTitleEl = document.getElementById('addPreviewTitle');
    previewSlugEl = document.getElementById('addPreviewSlug');
    previewDescEl = document.getElementById('addPreviewDesc');
    titleCounterEl = document.getElementById('addMetaTitleCounter');
    descCounterEl = document.getElementById('addMetaDescCounter');

    if (titleCounterEl && metaTitleInput) titleCounterEl.textContent = metaTitleInput.value.length + ' / 60 chars';
    if (descCounterEl && metaDescInput) descCounterEl.textContent = metaDescInput.value.length + ' / 160 chars';

  } else if (prefix === 'edit') {
    const titleInput = document.getElementById('editListingTitle');
    const descInput = document.getElementById('editListingDesc');
    const metaTitleInput = document.getElementById('editMetaTitle');
    const metaDescInput = document.getElementById('editMetaDesc');
    const slugInput = document.getElementById('editSlug');

    baseTitle = (titleInput && titleInput.value.trim()) ? titleInput.value.trim() : 'Directory Listing Title';
    baseDesc = (descInput && descInput.value.trim()) ? descInput.value.trim() : 'Enter a meta description to preview how this directory listing appears in Google search engine results...';

    titleVal = (metaTitleInput && metaTitleInput.value.trim()) ? metaTitleInput.value.trim() : (baseTitle + ' | POV Indian');
    descVal = (metaDescInput && metaDescInput.value.trim()) ? metaDescInput.value.trim() : baseDesc;
    slugVal = (slugInput && slugInput.value.trim()) ? slugInput.value.trim() : baseTitle.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

    previewTitleEl = document.getElementById('editPreviewTitle');
    previewSlugEl = document.getElementById('editPreviewSlug');
    previewDescEl = document.getElementById('editPreviewDesc');
    titleCounterEl = document.getElementById('editMetaTitleCounter');
    descCounterEl = document.getElementById('editMetaDescCounter');

    if (titleCounterEl && metaTitleInput) titleCounterEl.textContent = metaTitleInput.value.length + ' / 60 chars';
    if (descCounterEl && metaDescInput) descCounterEl.textContent = metaDescInput.value.length + ' / 160 chars';

  } else if (prefix === 'quick') {
    const baseTitleInput = document.getElementById('quickBaseListingTitle');
    const baseDescInput = document.getElementById('quickBaseListingDesc');
    const metaTitleInput = document.getElementById('quickMetaTitle');
    const metaDescInput = document.getElementById('quickMetaDesc');
    const slugInput = document.getElementById('quickSlug');

    baseTitle = (baseTitleInput && baseTitleInput.value.trim()) ? baseTitleInput.value.trim() : 'Directory Listing Title';
    baseDesc = (baseDescInput && baseDescInput.value.trim()) ? baseDescInput.value.trim() : 'Enter a meta description to preview how this directory listing appears on Google search results...';

    titleVal = (metaTitleInput && metaTitleInput.value.trim()) ? metaTitleInput.value.trim() : (baseTitle + ' | POV Indian');
    descVal = (metaDescInput && metaDescInput.value.trim()) ? metaDescInput.value.trim() : baseDesc;
    slugVal = (slugInput && slugInput.value.trim()) ? slugInput.value.trim() : baseTitle.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

    previewTitleEl = document.getElementById('quickPreviewTitle');
    previewSlugEl = document.getElementById('quickPreviewSlug');
    previewDescEl = document.getElementById('quickPreviewDesc');
    titleCounterEl = document.getElementById('quickMetaTitleCounter');
    descCounterEl = document.getElementById('quickMetaDescCounter');

    if (titleCounterEl && metaTitleInput) titleCounterEl.textContent = metaTitleInput.value.length + ' / 60 chars';
    if (descCounterEl && metaDescInput) descCounterEl.textContent = metaDescInput.value.length + ' / 160 chars';
  }

  if (previewTitleEl) previewTitleEl.textContent = titleVal;
  if (previewSlugEl) previewSlugEl.textContent = slugVal || 'listing-slug';
  if (previewDescEl) previewDescEl.textContent = descVal;
}

// Dedicated Quick SEO Modal Controller
function openSeoModal(item) {
  if (!item) return;

  const idInput = document.getElementById('quickSeoListingId');
  const titleDisp = document.getElementById('quickSeoListingTitleDisplay');
  const baseTitle = document.getElementById('quickBaseListingTitle');
  const baseDesc = document.getElementById('quickBaseListingDesc');
  const metaTitleInput = document.getElementById('quickMetaTitle');
  const slugInput = document.getElementById('quickSlug');
  const schemaSelect = document.getElementById('quickSchemaType');
  const metaDescInput = document.getElementById('quickMetaDesc');
  const metaKeywordsInput = document.getElementById('quickMetaKeywords');
  const canonicalInput = document.getElementById('quickCanonicalUrl');
  const ogImageInput = document.getElementById('quickOgImage');

  if (idInput) idInput.value = item.id || '';
  if (titleDisp) titleDisp.textContent = item.title || ('#' + item.id);
  if (baseTitle) baseTitle.value = item.title || '';
  if (baseDesc) baseDesc.value = item.desc || item.description || '';
  if (metaTitleInput) metaTitleInput.value = item.meta_title || '';
  if (slugInput) slugInput.value = item.slug || '';
  if (schemaSelect) schemaSelect.value = item.schema_type || 'LocalBusiness';
  if (metaDescInput) metaDescInput.value = item.meta_description || '';
  if (metaKeywordsInput) metaKeywordsInput.value = item.meta_keywords || '';
  if (canonicalInput) canonicalInput.value = item.canonical_url || '';
  if (ogImageInput) ogImageInput.value = item.og_image || '';

  povUpdateSeoLive('quick');
  openModal('seoListingModal');
}


function povUpdateEditListingPreview() {
  const coverInput = document.getElementById('editListingImage');
  const logoInput = document.getElementById('editListingAvatar');
  const coverImg = document.getElementById('editPreviewCoverImg');
  const logoImg = document.getElementById('editPreviewLogoImg');
  if (!coverImg || !logoImg) return;

  const baseHref = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));

  let coverVal = (coverInput ? coverInput.value.trim() : '');
  if (!coverVal) coverVal = 'realestate_03.webp';
  if (/^(https?:)?\/\//i.test(coverVal) || coverVal.startsWith('data:image')) {
    coverImg.src = coverVal;
  } else if (coverVal.startsWith('assets/')) {
    coverImg.src = baseHref + '/' + coverVal.replace(/^\/+/, '');
  } else {
    coverImg.src = baseHref + '/assets/img/listings/' + coverVal.replace(/^\/+/, '');
  }

  let logoVal = (logoInput ? logoInput.value.trim() : '');
  if (!logoVal) logoVal = 'ryan.webp';
  if (/^(https?:)?\/\//i.test(logoVal) || logoVal.startsWith('data:image')) {
    logoImg.src = logoVal;
  } else if (logoVal.startsWith('assets/')) {
    logoImg.src = baseHref + '/' + logoVal.replace(/^\/+/, '');
  } else {
    logoImg.src = baseHref + '/assets/img/avatars/' + logoVal.replace(/^\/+/, '');
  }
}

// ================= Rental Property Edit Controller =================
function openEditRentalModal(item) {
  if (!item) return;

  const idDisp = document.getElementById('editRentalIdDisplay');
  const idInput = document.getElementById('editRentalId');
  const titleInput = document.getElementById('editRentalTitle');
  const typeSelect = document.getElementById('editRentalType');
  const catSelect = document.getElementById('editRentalCategory');
  const cityInput = document.getElementById('editRentalCity');
  const stateInput = document.getElementById('editRentalState');
  const landmarkInput = document.getElementById('editRentalLandmark');
  const landmarkDistInput = document.getElementById('editRentalLandmarkDistance');
  const localityInput = document.getElementById('editRentalLocality');
  const latInput = document.getElementById('editRentalLat');
  const lngInput = document.getElementById('editRentalLng');
  const priceInput = document.getElementById('editRentalPrice');
  const unitSelect = document.getElementById('editRentalPriceUnit');
  const depositInput = document.getElementById('editRentalDeposit');
  const bhkInput = document.getElementById('editRentalBhk');
  const furnishSelect = document.getElementById('editRentalFurnishing');
  const tenantSelect = document.getElementById('editRentalTenant');
  const foodSelect = document.getElementById('editRentalFoodRule');
  const curfewSelect = document.getElementById('editRentalCurfew');
  const hostNameInput = document.getElementById('editRentalHostName');
  const hostPhoneInput = document.getElementById('editRentalHostPhone');
  const imageInput = document.getElementById('editRentalImage');
  const descInput = document.getElementById('editRentalDesc');

  if (idDisp) idDisp.textContent = item.id || '';
  if (idInput) idInput.value = item.id || '';
  if (titleInput) titleInput.value = item.title || '';
  if (typeSelect) typeSelect.value = item.rental_type || 'long_term';
  if (catSelect) catSelect.value = item.category || 'Flat / Apartment';
  if (cityInput) cityInput.value = item.city || '';
  if (stateInput) stateInput.value = item.state || '';
  if (landmarkInput) landmarkInput.value = item.landmark || '';
  if (landmarkDistInput) landmarkDistInput.value = item.landmark_distance || '';
  if (localityInput) localityInput.value = item.locality || '';
  if (latInput) latInput.value = item.latitude || '';
  if (lngInput) lngInput.value = item.longitude || '';
  if (priceInput) priceInput.value = item.price || '';
  if (unitSelect) unitSelect.value = item.price_unit || '/ month';
  if (depositInput) depositInput.value = item.deposit || '';
  if (bhkInput) bhkInput.value = item.bhk || '';
  if (furnishSelect) furnishSelect.value = item.furnishing || 'Semi-Furnished';
  if (tenantSelect) tenantSelect.value = item.preferred_tenant || item.tenant_preference || 'All Welcome';
  if (foodSelect) foodSelect.value = item.food_rule || item.food_label || 'Non-Veg Allowed';
  if (curfewSelect) curfewSelect.value = item.curfew_rule || item.curfew_label || 'No Curfew';
  if (hostNameInput) hostNameInput.value = item.host_name || item.provider_name || 'Verified Host';
  if (hostPhoneInput) hostPhoneInput.value = item.host_phone || item.phone || '+91 98765 43210';
  if (imageInput) imageInput.value = item.img || item.image || 'realestate_01.webp';
  if (descInput) descInput.value = item.desc || item.description || '';

  openModal('editRentalModal');
}

// ================= Categories Sign Up Toggle Handler =================
async function toggleCategorySignup(btn, categoryId) {
  if (!categoryId || !btn) return;

  const isCurrentlyOn = btn.classList.contains('on');
  const label = btn.querySelector('.toggle-label');

  // Optimistic UI state flip
  btn.classList.toggle('on', !isCurrentlyOn);
  btn.classList.toggle('off', isCurrentlyOn);
  if (label) {
    label.innerHTML = !isCurrentlyOn
      ? '<i class="fa-solid fa-check"></i> Shown in Sign Up'
      : '<i class="fa-solid fa-xmark"></i> Hidden in Sign Up';
  }
  btn.title = !isCurrentlyOn
    ? 'Currently VISIBLE on Sign Up form. Click to hide.'
    : 'Currently HIDDEN from Sign Up form. Click to show.';

  updateSignupStatsBadge();

  try {
    const formData = new FormData();
    formData.append('admin_action', 'toggle_category_signup');
    formData.append('id', categoryId);

    const res = await fetch('admin-dashboard.php', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    const data = await res.json().catch(() => null);

    if (res.ok && data && data.success) {
      showAdminToast(
        data.message || (!isCurrentlyOn ? 'Category is now visible in Sign Up form!' : 'Category is now hidden from Sign Up form.'),
        'success'
      );
    } else {
      // Revert on failure
      btn.classList.toggle('on', isCurrentlyOn);
      btn.classList.toggle('off', !isCurrentlyOn);
      updateSignupStatsBadge();
      showAdminToast((data && data.message) ? data.message : 'Failed to update category state.', 'error');
    }
  } catch (err) {
    // If AJAX fails for any reason, submit the form normally
    const form = btn.closest('form');
    if (form) form.submit();
  }
}

function updateSignupStatsBadge() {
  const allButtons = document.querySelectorAll('.btn-signup-toggle');
  let active = 0;
  allButtons.forEach(b => {
    if (b.classList.contains('on')) active++;
  });
  const hidden = allButtons.length - active;
  const activeCountEl = document.getElementById('signupActiveCount');
  const hiddenCountEl = document.getElementById('signupHiddenCount');
  if (activeCountEl) activeCountEl.textContent = active;
  if (hiddenCountEl) hiddenCountEl.textContent = hidden;
}

function showAdminToast(msg, type = 'success') {
  let toast = document.getElementById('adminToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'adminToast';
    toast.style.cssText = `
      position: fixed;
      bottom: 26px;
      right: 26px;
      padding: 13px 22px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 13.5px;
      color: #ffffff;
      z-index: 999999;
      display: flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 12px 35px rgba(0,0,0,0.22);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      transform: translateY(100px);
      opacity: 0;
      pointer-events: none;
    `;
    document.body.appendChild(toast);
  }
  toast.style.background = type === 'success' ? '#10b981' : '#ef4444';
  toast.innerHTML = (type === 'success' ? '<i class="fa-solid fa-circle-check" style="font-size:16px;"></i> ' : '<i class="fa-solid fa-triangle-exclamation" style="font-size:16px;"></i> ') + msg;
  toast.style.transform = 'translateY(0)';
  toast.style.opacity = '1';
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => {
    toast.style.transform = 'translateY(100px)';
    toast.style.opacity = '0';
  }, 2800);
}

