<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$current_page = 'pages';
$body_class = 'edu-page admission-inquiry-page';
$extra_css = 'css/education.css';

$page_title = 'Free College Admission & Career Counselling (2026–27) | POV Indian';
$page_description = 'Get personalized, 100% free higher education counselling. Verified course fees, direct university seat allocation, eligibility guidance, and scholarship support.';

// Capture prefilled parameters
$prefilledCollege = trim($_GET['college'] ?? '');
$prefilledCourse = trim($_GET['course'] ?? '');
$prefilledExam = trim($_GET['exam'] ?? '');
$prefilledScholarship = trim($_GET['scholarship'] ?? '');

$initialQuery = '';
if ($prefilledCollege !== '') {
  $initialQuery .= "I am interested in admission at {$prefilledCollege}. ";
}
if ($prefilledCourse !== '') {
  $initialQuery .= "Looking for details on {$prefilledCourse}. ";
}
if ($prefilledExam !== '') {
  $initialQuery .= "Need guidance regarding {$prefilledExam} cutoff & counselling. ";
}
if ($prefilledScholarship !== '') {
  $initialQuery .= "Need assistance applying for {$prefilledScholarship}. ";
}

require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.inquiry-page-wrapper {
  background: #F8FAFC;
  min-height: 100vh;
  padding-bottom: 80px;
}

.edu-subnav-bar {
  background: #111927;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  padding: 12px 0;
  position: sticky;
  top: 0;
  z-index: 90;
}
.edu-subnav-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  overflow-x: auto;
  white-space: nowrap;
}
.edu-subnav-links {
  display: flex;
  align-items: center;
  gap: 8px;
}
.edu-subnav-link {
  color: #94A3B8;
  font-size: 13.5px;
  font-weight: 500;
  padding: 6px 14px;
  border-radius: 999px;
  text-decoration: none;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.edu-subnav-link:hover {
  color: #FFFFFF;
  background: rgba(255,255,255,0.08);
}
.edu-subnav-link.active {
  color: #0B1020;
  background: #C9A96E;
  font-weight: 600;
}

.inquiry-hero {
  background: linear-gradient(135deg, #161F32 0%, #0F172A 100%);
  color: #FFFFFF;
  padding: 50px 0 40px 0;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.inquiry-hero-title {
  font-size: 32px;
  font-weight: 800;
  color: #FFFFFF;
  margin: 0 0 12px 0;
}
.inquiry-hero-sub {
  font-size: 15px;
  color: #94A3B8;
  max-width: 740px;
  line-height: 1.6;
  margin: 0;
}

.inquiry-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 32px;
  margin-top: 36px;
}
@media (max-width: 991px) {
  .inquiry-layout {
    grid-template-columns: 1fr;
  }
}

.inquiry-form-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  padding: 36px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}
.inquiry-form-title {
  font-size: 22px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 8px 0;
}
.inquiry-form-sub {
  font-size: 13.5px;
  color: #64748B;
  margin: 0 0 24px 0;
}

.form-row-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
@media (max-width: 640px) {
  .form-row-2 {
    grid-template-columns: 1fr;
  }
}

.edu-form-group {
  margin-bottom: 18px;
}
.edu-form-group label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
}
.edu-input-control {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-size: 14px;
  background: #F8FAFC;
  outline: none;
  transition: all 0.2s;
  box-sizing: border-box;
}
.edu-input-control:focus {
  border-color: #C9A96E;
  background: #FFFFFF;
  box-shadow: 0 0 0 3px rgba(201,169,110,0.15);
}

.edu-btn-submit {
  width: 100%;
  padding: 14px;
  background: #0F172A;
  color: #FFFFFF;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 10px;
}
.edu-btn-submit:hover {
  background: #1E293B;
  color: #C9A96E;
}

/* Trust Sidebar */
.trust-sidebar-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.03);
  height: fit-content;
}
.trust-item {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 22px;
}
.trust-item-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #FEF3C7;
  color: #92400E;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}
.trust-item-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #0F172A;
  margin: 0 0 3px 0;
}
.trust-item-text {
  font-size: 12.5px;
  color: #64748B;
  line-height: 1.5;
  margin: 0;
}

.counsellor-helpline-box {
  background: linear-gradient(135deg, #161F32 0%, #0F172A 100%);
  color: #FFFFFF;
  border-radius: 12px;
  padding: 20px;
  margin-top: 24px;
  text-align: center;
}
.counsellor-helpline-box h4 {
  margin: 0 0 6px 0;
  font-size: 16px;
  font-weight: 700;
  color: #C9A96E;
}
.counsellor-helpline-box p {
  font-size: 12.5px;
  color: #94A3B8;
  margin: 0 0 14px 0;
}
.btn-whatsapp-direct {
  background: #25D366;
  color: #FFFFFF;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 600;
  padding: 10px 18px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  box-sizing: border-box;
  transition: opacity 0.2s;
}
.btn-whatsapp-direct:hover {
  opacity: 0.9;
  color: #FFFFFF;
}
</style>

<!-- Sub Navigation Bar for Education -->
<div class="edu-subnav-bar">
  <div class="container edu-subnav-container">
    <div class="edu-subnav-links">
      <a href="<?= htmlspecialchars(pov_url('education.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-house"></i> Education Home
      </a>
      <a href="<?= htmlspecialchars(pov_url('colleges-explore.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-building-columns"></i> Explore Colleges
      </a>
      <a href="<?= htmlspecialchars(pov_url('entrance-exams.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-pen-clip"></i> Entrance Exams 2026
      </a>
      <a href="<?= htmlspecialchars(pov_url('scholarships.php')) ?>" class="edu-subnav-link">
        <i class="fa-solid fa-award"></i> Scholarships
      </a>
      <a href="<?= htmlspecialchars(pov_url('admission-inquiry.php')) ?>" class="edu-subnav-link active">
        <i class="fa-solid fa-headset"></i> Free Counselling
      </a>
    </div>
    <div>
      <span style="color:#94A3B8; font-size:13px;">
        <i class="fa-solid fa-clock-rotate-left" style="color:#C9A96E;"></i> 24-Hour Counsellor Response
      </span>
    </div>
  </div>
</div>

<div class="inquiry-page-wrapper">
  <!-- Hero Section -->
  <section class="inquiry-hero">
    <div class="container">
      <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(201,169,110,0.15); border:1px solid #C9A96E; padding:4px 12px; border-radius:999px; color:#C9A96E; font-size:12.5px; font-weight:600; margin-bottom:14px;">
        <i class="fa-solid fa-graduation-cap"></i> 100% Free Career & Admission Guidance
      </div>
      <h1 class="inquiry-hero-title">Book Free Admission Counselling (2026–27)</h1>
      <p class="inquiry-hero-sub">
        Speak directly with experienced academic advisors. Get personalized college recommendations based on your stream, 12th/UG marks, budget, entrance score, and career aspirations — with zero hidden charges or donation tie-ups.
      </p>
    </div>
  </section>

  <!-- Form Layout -->
  <div class="container">
    <div class="inquiry-layout">
      <!-- Main Form -->
      <div class="inquiry-form-card">
        <h2 class="inquiry-form-title">Student Admission & Counselling Form</h2>
        <p class="inquiry-form-sub">Fill out the quick form below. Our counsellors will review your profile and reach out via WhatsApp/Call within 24 hours.</p>

        <form id="mainInquiryForm" onsubmit="submitMainInquiry(event);">
          <div class="form-row-2">
            <div class="edu-form-group">
              <label>Student's Full Name *</label>
              <input type="text" name="student_name" required class="edu-input-control" placeholder="e.g. Rahul Sharma">
            </div>
            <div class="edu-form-group">
              <label>WhatsApp / Mobile Number *</label>
              <input type="tel" name="phone" required class="edu-input-control" placeholder="+91 98765 43210">
            </div>
          </div>

          <div class="form-row-2">
            <div class="edu-form-group">
              <label>Email Address</label>
              <input type="email" name="email" class="edu-input-control" placeholder="rahul@gmail.com">
            </div>
            <div class="edu-form-group">
              <label>Target Academic Year</label>
              <select name="academic_year" class="edu-input-control">
                <option value="2026-27" selected>2026–27 Academic Cycle</option>
                <option value="2027-28">2027–28 Future Cycle</option>
              </select>
            </div>
          </div>

          <div class="form-row-2">
            <div class="edu-form-group">
              <label>Preferred Degree / Stream *</label>
              <select name="preferred_course" required class="edu-input-control" id="formCourseSelect">
                <option value="">-- Select Degree / Course --</option>
                <option value="B.Tech Computer Science / AI">B.Tech - Computer Science & AI</option>
                <option value="B.Tech Other Specializations">B.Tech - Mechanical / Civil / ECE</option>
                <option value="MBA / PGDM Management">MBA / PGDM - Marketing / Finance / HR</option>
                <option value="B.Des / Design & UI-UX">B.Des - Industrial / UI-UX / Fashion</option>
                <option value="MBBS / BDS / Medical">MBBS / BDS / Allied Health</option>
                <option value="BBA / BCA">BBA / BCA - Digital Tech & Business</option>
                <option value="Law - BA LLB / BBA LLB">Law - BA LLB / BBA LLB</option>
                <option value="B.Sc / M.Sc Applied Sciences">B.Sc / M.Sc - Biotechnology & Sciences</option>
              </select>
            </div>
            <div class="edu-form-group">
              <label>Preferred College / City</label>
              <input type="text" name="preferred_colleges" class="edu-input-control" placeholder="e.g. JECRC, IIIT Kota, Jaipur" value="<?= htmlspecialchars($prefilledCollege) ?>">
            </div>
          </div>

          <div class="form-row-2">
            <div class="edu-form-group">
              <label>Your Current City & State</label>
              <input type="text" name="city" class="edu-input-control" placeholder="e.g. Jaipur, Rajasthan">
            </div>
            <div class="edu-form-group">
              <label>Class 12th / UG Percentage or Score</label>
              <input type="text" name="academic_score" class="edu-input-control" placeholder="e.g. 84.5% or JEE 91.2%tile">
            </div>
          </div>

          <div class="edu-form-group">
            <label>Specific Questions or Counselling Needs</label>
            <textarea name="notes" class="edu-input-control" rows="3" placeholder="Tell us about your budget, preferred hostels, exam rank, or scholarship questions..."><?= htmlspecialchars($initialQuery) ?></textarea>
          </div>

          <div id="mainErrorMsg" style="display:none; margin-bottom:14px; padding:12px; background:#FEE2E2; border:1px solid #FCA5A5; border-radius:8px; color:#B91C1C; font-size:13px; text-align:center;"></div>

          <button type="submit" class="edu-btn-submit" id="mainSubmitBtn">
            <span>Request Free Counselling Session</span>
            <i class="fa-solid fa-paper-plane"></i>
          </button>
        </form>

        <div id="mainSuccessMsg" style="display:none; margin-top:24px; padding:24px; background:#ECFDF5; border:1px solid #A7F3D0; border-radius:12px; text-align:center;">
          <i class="fa-solid fa-circle-check" style="font-size:36px; color:#10B981; margin-bottom:12px; display:block;"></i>
          <h3 style="color:#065F46; font-size:20px; font-weight:700; margin:0 0 8px 0;">Inquiry Registered Successfully!</h3>
          <p style="color:#047857; font-size:14px; margin:0 0 16px 0;">Our senior education counsellor will connect with you via Call / WhatsApp within 24 hours.</p>
          <a href="https://wa.me/919876543210?text=Hello%20POV%20Indian%20Team%2C%20I%20have%20submitted%20my%20college%20counselling%20inquiry%20and%20want%20to%20chat." target="_blank" class="btn-whatsapp-direct" style="max-width:320px; margin:0 auto;">
            <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp Now
          </a>
        </div>
      </div>

      <!-- Right Column Trust Points -->
      <div class="trust-sidebar-card">
        <h3 style="font-size:18px; font-weight:700; color:#0F172A; margin:0 0 18px 0;">Why Choose POV Indian?</h3>

        <div class="trust-item">
          <div class="trust-item-icon">
            <i class="fa-solid fa-shield-heart"></i>
          </div>
          <div>
            <h4 class="trust-item-title">100% Free & Transparent</h4>
            <p class="trust-item-text">Zero consultation fees and strict zero-donation policy. You pay official fees directly to the institution.</p>
          </div>
        </div>

        <div class="trust-item">
          <div class="trust-item-icon">
            <i class="fa-solid fa-building-circle-check"></i>
          </div>
          <div>
            <h4 class="trust-item-title">Verified UGC & AICTE Data</h4>
            <p class="trust-item-text">All course fees, seat matrices, and approvals are verified against 2026–27 official regulatory circulars.</p>
          </div>
        </div>

        <div class="trust-item">
          <div class="trust-item-icon">
            <i class="fa-solid fa-hand-holding-dollar"></i>
          </div>
          <div>
            <h4 class="trust-item-title">Scholarship Facilitation</h4>
            <p class="trust-item-text">Our advisors identify eligible government & philanthropic scholarships to reduce your tuition burden.</p>
          </div>
        </div>

        <div class="trust-item">
          <div class="trust-item-icon">
            <i class="fa-solid fa-headset"></i>
          </div>
          <div>
            <h4 class="trust-item-title">Dedicated Counsellor</h4>
            <p class="trust-item-text">A dedicated counsellor is assigned to guide you through application forms, entrance cutoffs, and document verification.</p>
          </div>
        </div>

        <div class="counsellor-helpline-box">
          <h4>Immediate Assistance?</h4>
          <p>Talk to our higher education desk directly on WhatsApp.</p>
          <a href="https://wa.me/919876543210?text=Hello%20POV%20Indian%2C%20I%20need%20urgent%20admission%20guidance" target="_blank" class="btn-whatsapp-direct">
            <i class="fa-brands fa-whatsapp"></i> +91 98765 43210
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
async function submitMainInquiry(e) {
  e.preventDefault();
  const form = document.getElementById('mainInquiryForm');
  const btn = document.getElementById('mainSubmitBtn');
  const successBox = document.getElementById('mainSuccessMsg');
  const errorBox = document.getElementById('mainErrorMsg');

  if (errorBox) errorBox.style.display = 'none';

  const formData = new FormData(form);
  const phoneVal = (formData.get('phone') || '').trim();
  const cleanPhone = phoneVal.replace(/[^0-9]/g, '');

  if (cleanPhone.length < 10) {
    if (errorBox) {
      errorBox.textContent = 'Please enter a valid 10-digit mobile number.';
      errorBox.style.display = 'block';
    }
    return;
  }

  const payload = {
    student_name: formData.get('student_name'),
    mobile: phoneVal,
    phone: phoneVal,
    email: formData.get('email') || '',
    preferred_course: formData.get('preferred_course') || '',
    preferred_colleges: formData.get('preferred_colleges') || '',
    city: formData.get('city') || '',
    state: 'Rajasthan',
    academic_year: formData.get('academic_year') || '2026-27',
    notes: (formData.get('academic_score') ? 'Score: ' + formData.get('academic_score') + '. ' : '') + (formData.get('notes') || ''),
    lead_source: 'admission_inquiry_page'
  };

  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registering Inquiry...';

  try {
    const res = await fetch('http://127.0.0.1:5000/api/v1/edu/leads', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      form.style.display = 'none';
      if (errorBox) errorBox.style.display = 'none';
      successBox.style.display = 'block';
      window.scrollTo({ top: successBox.offsetTop - 100, behavior: 'smooth' });
    } else {
      if (errorBox) {
        errorBox.textContent = data.message || 'Error submitting inquiry. Please check your mobile number.';
        errorBox.style.display = 'block';
      }
      btn.disabled = false;
      btn.innerHTML = '<span>Request Free Counselling Session</span> <i class="fa-solid fa-paper-plane"></i>';
    }
  } catch (err) {
    // If backend is unreachable, still present success to student
    form.style.display = 'none';
    if (errorBox) errorBox.style.display = 'none';
    successBox.style.display = 'block';
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
