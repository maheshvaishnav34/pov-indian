(() => {
  const header = document.querySelector(".site-header");
  const menuToggle = document.querySelector(".menu-toggle");
  const toast = document.querySelector(".toast");

  const showToast = (message) => {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add("show");
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => toast.classList.remove("show"), 2400);
  };

  window.addEventListener(
    "scroll",
    () => {
      if (!header) return;
      header.classList.toggle("is-sticky", window.scrollY > 40);
    },
    { passive: true }
  );

  menuToggle?.addEventListener("click", () => {
    header?.classList.toggle("menu-open");
  });

  document.querySelectorAll(".nav > li > a").forEach((link) => {
    link.addEventListener("click", (e) => {
      const parent = link.parentElement;
      const hasDropdown = parent?.querySelector(".dropdown");
      if (window.innerWidth <= 991 && hasDropdown) {
        e.preventDefault();
        parent.classList.toggle("open");
        return;
      }
      if (header?.classList.contains("menu-open")) {
        header.classList.remove("menu-open");
      }
    });
  });

  const signupModal = document.getElementById("signupModal");
  const openSignup = () => {
    signupModal?.classList.add("is-open");
    document.body.style.overflow = "hidden";
  };
  const closeSignup = () => {
    signupModal?.classList.remove("is-open");
    document.body.style.overflow = "";
  };

  document.querySelectorAll("[data-open-signup]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      openSignup();
    });
  });

  signupModal?.querySelectorAll("[data-close-modal]").forEach((el) => {
    el.addEventListener("click", closeSignup);
  });

  const editInterestsModal = document.getElementById("editInterestsModal");
  const openEditInterests = () => {
    editInterestsModal?.classList.add("is-open");
    document.body.style.overflow = "hidden";
  };
  const closeEditInterests = () => {
    editInterestsModal?.classList.remove("is-open");
    document.body.style.overflow = "";
  };

  document.querySelectorAll("[data-open-edit-interests]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      openEditInterests();
    });
  });

  editInterestsModal?.querySelectorAll("[data-close-modal]").forEach((el) => {
    el.addEventListener("click", closeEditInterests);
  });

  // Unified Auth Modal Tab Switcher
  const switchAuthTab = (tabName) => {
    const isSignin = tabName === "signin";
    signupModal?.querySelectorAll(".auth-tab-btn").forEach((btn) => {
      btn.classList.toggle("is-active", btn.dataset.tab === tabName);
    });
    const paneSignin = document.getElementById("authPaneSignin");
    const paneSignup = document.getElementById("authPaneSignup");
    if (paneSignin && paneSignup) {
      paneSignin.classList.toggle("is-active", isSignin);
      paneSignup.classList.toggle("is-active", !isSignin);
    }
  };

  signupModal?.querySelectorAll(".auth-tab-btn").forEach((btn) => {
    btn.addEventListener("click", () => switchAuthTab(btn.dataset.tab));
  });

  signupModal?.querySelectorAll("[data-switch-auth]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      switchAuthTab(el.dataset.switchAuth);
    });
  });

  // Auto-open modal if URL has ?auth=login or ?auth=signup
  const urlAuthParam = new URLSearchParams(window.location.search).get("auth");
  if (urlAuthParam === "login") {
    switchAuthTab("signin");
    openSignup();
  } else if (urlAuthParam === "signup") {
    switchAuthTab("signup");
    openSignup();
  }

  document.querySelectorAll("[data-social]").forEach((btn) => {
    btn.addEventListener("click", () => {
      showToast(`Continue with ${btn.dataset.social}`);
    });
  });

  // Toggle user profile dropdown
  const userNavItem = document.querySelector(".user-nav-item");
  const userPillBtn = document.querySelector(".user-pill-btn");
  if (userPillBtn && userNavItem) {
    userPillBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      userNavItem.classList.toggle("is-open");
    });
    document.addEventListener("click", (e) => {
      if (!userNavItem.contains(e.target)) {
        userNavItem.classList.remove("is-open");
      }
    });
  }

  document.getElementById("heroSearch")?.addEventListener("submit", (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    const category = form.category.value;
    const location = form.location.value;
    const keyword = (form.keyword.value || "").trim();
    const base = document.body?.dataset?.base || "";
    if (category) {
      window.location.href = `${base}/category-${encodeURIComponent(category)}.php`;
      return;
    }
    const params = new URLSearchParams();
    if (location) params.set("location", location);
    if (keyword) params.set("q", keyword);
    const qs = params.toString();
    window.location.href = `${base}/listings.php${qs ? `?${qs}` : ""}`;
  });

  document.querySelectorAll(".fav-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("active");
      const icon = btn.querySelector("i");
      if (icon) {
        icon.classList.toggle("fa-solid", btn.classList.contains("active"));
        icon.classList.toggle("fa-regular", !btn.classList.contains("active"));
      }
      btn.setAttribute("aria-pressed", btn.classList.contains("active") ? "true" : "false");
      showToast(btn.classList.contains("active") ? "Saved to favourites" : "Removed from favourites");
    });
  });

  document.querySelectorAll("[data-purchase]").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      showToast(`Selected ${btn.dataset.purchase} plan on POV Indian`);
    });
  });

  // Newsletter + contact/list forms submit via PHP (form-submit.php)
  const bindSlider = (trackSelector, prevSelector, nextSelector) => {
    const track = document.querySelector(trackSelector);
    const prev = document.querySelector(prevSelector);
    const next = document.querySelector(nextSelector);
    if (!track) return;
    const scrollByCard = (dir) => {
      const card = track.querySelector(".listing-card, .testimonial-card");
      const amount = (card?.getBoundingClientRect().width || 280) + 22;
      track.scrollBy({ left: dir * amount, behavior: "smooth" });
    };
    prev?.addEventListener("click", () => scrollByCard(-1));
    next?.addEventListener("click", () => scrollByCard(1));
  };

  bindSlider("#recommendTrack", "#recommendPrev", "#recommendNext");
  bindSlider("#testimonialTrack", "#testimonialPrev", "#testimonialNext");
})();

// ---- Signup Modal: Interest Chips Expand / Collapse ----
function toggleInterestChips(btn) {
  const chips = document.querySelectorAll('#signupInterestChips .chip-hidden');
  const isExpanded = btn.dataset.expanded === '1';

  if (isExpanded) {
    // Collapse — hide extras again
    chips.forEach(c => { c.style.display = 'none'; });
    btn.dataset.expanded = '0';
    btn.innerHTML = '<i class="fa-solid fa-chevron-down" style="font-size:11px; margin-right:5px;"></i> View all ' + (chips.length + 3) + ' categories';
  } else {
    // Expand — show all
    chips.forEach(c => { c.style.display = ''; });
    btn.dataset.expanded = '1';
    btn.innerHTML = '<i class="fa-solid fa-chevron-up" style="font-size:11px; margin-right:5px;"></i> Show less';
  }
}

// Reset chips to collapsed state whenever the signup modal opens
document.addEventListener('click', function(e) {
  if (e.target && (e.target.matches('[data-open-modal="signupModal"]') || e.target.closest('[data-open-modal="signupModal"]') ||
      e.target.matches('[data-tab="signup"]') || e.target.closest('[data-tab="signup"]') ||
      e.target.matches('[data-switch-auth="signup"]') || e.target.closest('[data-switch-auth="signup"]'))) {
    setTimeout(function() {
      const hiddenChips = document.querySelectorAll('#signupInterestChips .chip-hidden');
      hiddenChips.forEach(c => { c.style.display = 'none'; });
      const btn = document.getElementById('chipsExpandBtn');
      if (btn) { btn.dataset.expanded = '0'; }
    }, 50);
  }
  // data-switch-auth handler (signin / signup / forgot)
  const sa = e.target.closest('[data-switch-auth]');
  if (sa) { e.preventDefault(); switchAuthPane(sa.dataset.switchAuth); }
});

// ---- Auth Pane Switcher (signin / signup / forgot) ----
function switchAuthPane(pane) {
  const panes = {
    signin: document.getElementById('authPaneSignin'),
    signup: document.getElementById('authPaneSignup'),
    forgot: document.getElementById('authPaneForgot'),
  };
  Object.values(panes).forEach(p => { if (p) { p.classList.remove('is-active'); p.style.display = 'none'; } });
  if (panes[pane]) { panes[pane].classList.add('is-active'); panes[pane].style.display = ''; }
  if (pane === 'forgot') showForgotStep1();
}

function showForgotStep1() {
  const s1 = document.getElementById('forgotStep1');
  const s2 = document.getElementById('forgotStep2');
  const s3 = document.getElementById('forgotStep3');
  const err = document.getElementById('forgotError');
  const emailInput = document.getElementById('forgotEmail');
  if (s1) s1.style.display = '';
  if (s2) s2.style.display = 'none';
  if (s3) s3.style.display = 'none';
  if (err) { err.style.display = 'none'; err.textContent = ''; }
  if (emailInput) emailInput.value = '';
}

// ---- Forgot Password: AJAX submit ----
async function handleForgotPassword(e) {
  e.preventDefault();
  const emailInput = document.getElementById('forgotEmail');
  const submitBtn  = document.getElementById('forgotSubmitBtn');
  const errBox     = document.getElementById('forgotError');
  const email      = emailInput ? emailInput.value.trim() : '';
  if (!email) return;

  if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating...'; }
  if (errBox)    { errBox.style.display = 'none'; errBox.textContent = ''; }

  try {
    const fd = new FormData();
    fd.append('form_type', 'forgot_password');
    fd.append('email', email);

    const res  = await fetch('form-submit.php', { method: 'POST', body: fd });
    const data = await res.json().catch(() => null);

    if (data && data.success) {
      if (data.found && data.reset_url) {
        // Show the clickable reset link
        const link = document.getElementById('forgotResetLink');
        if (link) link.href = data.reset_url;
        document.getElementById('forgotStep1').style.display = 'none';
        document.getElementById('forgotStep2').style.display = '';
      } else {
        // Email not in system — generic message
        document.getElementById('forgotStep1').style.display = 'none';
        document.getElementById('forgotStep3').style.display = '';
      }
    } else {
      if (errBox) { errBox.style.display = ''; errBox.textContent = (data && data.message) ? data.message : 'Something went wrong. Please try again.'; }
    }
  } catch (err) {
    if (errBox) { errBox.style.display = ''; errBox.textContent = 'Network error. Please try again.'; }
  } finally {
    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Send Reset Link'; }
  }
}
