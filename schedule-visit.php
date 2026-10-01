<?php
/**
 * schedule-visit.php
 * POV Indian Rentals & Stays - Dedicated Schedule Visit Page
 * Flow:
 * Schedule Visit -> Pick Visit Slot (Date + Time) -> Continue -> Visitor Details -> Request Visit -> Owner/Agent Confirmation
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/visit-store.php';

$propId = isset($_GET['property_id']) ? (int)$_GET['property_id'] : 101;
$property = pov_get_rental_full_detail($propId);

if (!$property) {
  $propId = 101;
  $property = pov_get_rental_full_detail(101);
}

$page_title = 'Pick a Visit Slot: ' . $property['title'] . ' | POV Indian';
$page_description = 'Pick a convenient physical walkthrough slot for ' . $property['title'] . ' with the verified owner or host.';
$current_page = 'listings';
$body_class = 'category-landing rentals-page schedule-visit-page';
$extra_css = 'css/schedule-visit.css';

// Prefill visitor details if user is in session
$authUser = $_SESSION['auth_user'] ?? $_SESSION['user'] ?? null;
$defaultName = $authUser['name'] ?? ($_SESSION['last_visitor_name'] ?? '');
$defaultPhone = $authUser['phone'] ?? ($_SESSION['last_visitor_phone'] ?? '');
$defaultEmail = $authUser['email'] ?? '';

// Configurable date limits (Future dates only: tomorrow to +30 days)
$minDate = date('Y-m-d', strtotime('+1 day'));
$maxDate = date('Y-m-d', strtotime('+30 days'));

// Quick dates array for next 6 days
$quickDates = [];
for ($i = 1; $i <= 6; $i++) {
  $ts = strtotime("+$i day");
  $quickDates[] = [
    'date' => date('Y-m-d', $ts),
    'label' => $i === 1 ? 'Tomorrow' : date('D', $ts),
    'sub' => date('M j', $ts),
    'is_active' => $i === 1
  ];
}

$standardSlots = ['10:00 AM', '11:00 AM', '12:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM'];
$bookedInitial = pov_get_unavailable_slots($propId, $minDate);

include __DIR__ . '/includes/head.php';
include __DIR__ . '/includes/header.php';
?>

  <!-- Breadcrumbs Bar -->
  <div class="sv-breadcrumbs-bar">
    <div class="container">
      <nav class="sv-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?= htmlspecialchars(pov_url('index.php')) ?>">Home</a>
        <span class="sv-crumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>">Rentals & Stays</a>
        <span class="sv-crumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $propId)) ?>"><?= htmlspecialchars($property['short_title'] ?? $property['title']) ?></a>
        <span class="sv-crumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span class="sv-crumb-curr">Pick a Visit Slot</span>
      </nav>
    </div>
  </div>

  <main class="container">
    <div class="sv-layout-grid">
      <!-- Left Column: Property Summary Card & Trust Policy -->
      <aside>
        <div class="sv-property-card">
          <div class="sv-property-thumb-wrap">
            <img src="<?= htmlspecialchars(pov_url('assets/img/listings/' . ($property['gallery'][0] ?? $property['img']))) ?>" 
                 alt="<?= htmlspecialchars($property['title']) ?>" />
            <span class="sv-thumb-cat-badge"><?= htmlspecialchars($property['category']) ?></span>
            <span class="sv-thumb-verified-badge"><i class="fa-solid fa-certificate"></i> <?= htmlspecialchars($property['trust_badge']) ?></span>
          </div>

          <div class="sv-property-body">
            <h3 class="sv-prop-title"><?= htmlspecialchars($property['title']) ?></h3>
            <div class="sv-prop-loc">
              <i class="fa-solid fa-location-dot" style="color:var(--sv-terracotta);"></i>
              <span><?= htmlspecialchars($property['locality']) ?>, <?= htmlspecialchars($property['city']) ?></span>
            </div>

            <div class="sv-prop-price-row">
              <span class="sv-price-big"><?= htmlspecialchars($property['price']) ?></span>
              <span class="sv-price-unit"><?= htmlspecialchars($property['price_unit']) ?></span>
            </div>

            <div class="sv-prop-provider">
              <i class="fa-solid fa-user-shield" style="color:var(--sv-terracotta); font-size:15px;"></i>
              <div>
                <span><?= htmlspecialchars($property['provider_type']) ?></span>: 
                <strong><?= htmlspecialchars($property['provider_name']) ?></strong>
              </div>
            </div>
          </div>
        </div>

        <!-- Policy Reminder -->
        <div class="sv-policy-card">
          <h4><i class="fa-solid fa-circle-info"></i> POV Indian Visit Policy</h4>
          <p style="margin:0 0 8px;">
            <strong>Request, Not Instant Guarantee:</strong> Your selected visit slot will be marked as <em>Requested</em> and submitted to the owner/agent for confirmation.
          </p>
          <p style="margin:0;">
            <strong>Free & Safe:</strong> Physical visits are 100% free with zero token advance required before personal inspection.
          </p>
        </div>
      </aside>

      <!-- Right Column: Step-by-Step Flow Form -->
      <div>
        <div class="sv-form-card" id="formCard">
          
          <!-- Stepper Progress Bar -->
          <div class="sv-flow-stepper">
            <div class="sv-step-node is-active" id="stepNode1">
              <div class="sv-step-num">1</div>
              <span>Pick Visit Slot</span>
            </div>
            <div class="sv-step-divider"></div>
            <div class="sv-step-node" id="stepNode2">
              <div class="sv-step-num">2</div>
              <span>Visitor Details</span>
            </div>
            <div class="sv-step-divider"></div>
            <div class="sv-step-node" id="stepNode3">
              <div class="sv-step-num">3</div>
              <span>Confirmation</span>
            </div>
          </div>

          <!-- Alert message container -->
          <div id="svAlertBox" style="display:none; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:600;"></div>

          <form id="scheduleVisitMainForm" onsubmit="handleVisitSubmit(event)">
            <input type="hidden" name="property_id" id="hiddenPropertyId" value="<?= $propId ?>" />
            <input type="hidden" name="preferred_date" id="selectedDateInput" value="<?= $minDate ?>" />
            <input type="hidden" name="preferred_time" id="selectedTimeSlot" value="11:00 AM" />

            <!-- ================= STEP 1: PICK VISIT SLOT ================= -->
            <div class="sv-form-step is-active" id="step1Container">
              <div class="sv-form-header" style="margin-bottom:20px; padding-bottom:14px;">
                <span class="sv-form-eyebrow">STEP 1 OF 2 · SCHEDULE VISIT</span>
                <h1 class="sv-form-title" style="font-size:24px;">Pick a Visit Slot</h1>
                <p class="sv-form-sub">Select your preferred future date and one available walkthrough time slot.</p>
              </div>

              <!-- 1. Date Selection (Calendar + Quick Days) -->
              <div class="sv-section-block">
                <span class="sv-quick-dates-label"><i class="fa-regular fa-calendar"></i> Select Date (Future Dates Only)</span>
                
                <!-- Quick Date Shortcut Buttons -->
                <div class="sv-quick-dates-scroll">
                  <?php foreach ($quickDates as $qd): ?>
                    <button type="button" class="sv-quick-date-btn <?= $qd['is_active'] ? 'is-active' : '' ?>" 
                            data-date="<?= $qd['date'] ?>" onclick="selectQuickDate('<?= $qd['date'] ?>', this)">
                      <span><?= $qd['label'] ?></span>
                      <small><?= $qd['sub'] ?></small>
                    </button>
                  <?php endforeach; ?>
                </div>

                <!-- Full Calendar Picker -->
                <div class="sv-form-group" style="margin-top:10px;">
                  <label class="sv-label">Or choose another date from calendar *</label>
                  <input type="date" class="sv-input" id="calendarDateInput" 
                         min="<?= $minDate ?>" max="<?= $maxDate ?>" value="<?= $minDate ?>" onchange="handleCalendarChange(this.value)" />
                  <span style="font-size:11.5px; color:var(--sv-muted); margin-top:4px; display:block;">
                    Visits can be requested from tomorrow up to 30 days in advance. Past dates are disabled.
                  </span>
                </div>
              </div>

              <!-- 2. Available Time Slots for Selected Date -->
              <div class="sv-section-block">
                <label class="sv-label" style="display:flex; justify-content:space-between; align-items:center;">
                  <span><i class="fa-regular fa-clock"></i> Available Time Slots for Selected Date *</span>
                  <span style="font-size:11.5px; color:var(--sv-muted); font-weight:normal;">Select 1 slot only</span>
                </label>
                
                <div class="sv-slots-grid" id="slotsContainer">
                  <?php foreach ($standardSlots as $slot): ?>
                    <?php 
                      $isBooked = in_array($slot, $bookedInitial, true);
                      $isSelected = ($slot === '11:00 AM' && !$isBooked);
                    ?>
                    <div class="sv-slot-chip <?= $isSelected ? 'is-selected' : '' ?> <?= $isBooked ? 'is-disabled' : '' ?>" 
                         data-slot="<?= $slot ?>" 
                         onclick="selectTimeSlot('<?= $slot ?>', this)">
                      <div><?= $slot ?></div>
                      <?php if ($isBooked): ?>
                        <span class="sv-slot-chip-sub" style="color:#DC2626;">Booked</span>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- 3. Selected Date + Time Live Summary -->
              <div class="sv-slot-summary-strip" id="liveSlotSummary">
                <div class="sv-summary-left">
                  <div class="sv-summary-icon">
                    <i class="fa-regular fa-calendar-check"></i>
                  </div>
                  <div>
                    <span class="sv-summary-title">SELECTED WALKTHROUGH SLOT</span>
                    <div class="sv-summary-val" id="summarySlotText">
                      <?= pov_format_visit_date($minDate) ?> at 11:00 AM
                    </div>
                  </div>
                </div>
                <div style="font-size:12px; color:var(--sv-green); font-weight:700;">
                  <i class="fa-solid fa-circle-check"></i> Slot Ready
                </div>
              </div>

              <!-- Step 1 Action: Continue Button -->
              <div class="sv-actions-row">
                <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $propId)) ?>" class="sv-btn-cancel">
                  Back to Property
                </a>
                <button type="button" class="sv-btn-submit" id="btnContinueStep1" onclick="goToStep(2)">
                  Continue to Visitor Details <i class="fa-solid fa-arrow-right"></i>
                </button>
              </div>
            </div>

            <!-- ================= STEP 2: VISITOR DETAILS ================= -->
            <div class="sv-form-step" id="step2Container">
              <!-- Picked Slot Recap Banner -->
              <div class="sv-picked-slot-banner">
                <div class="sv-picked-info">
                  <i class="fa-solid fa-calendar-day" style="color:#059669; font-size:18px;"></i>
                  <div>
                    <strong style="display:block; color:#065F46;">Picked Visit Slot</strong>
                    <span id="recapSlotText" style="font-weight:700; color:#142132;">
                      <?= pov_format_visit_date($minDate) ?> at 11:00 AM
                    </span>
                  </div>
                </div>
                <button type="button" class="sv-btn-change-slot" onclick="goToStep(1)">
                  <i class="fa-solid fa-pencil"></i> Change Slot
                </button>
              </div>

              <div class="sv-form-header" style="margin-bottom:20px; padding-bottom:14px;">
                <span class="sv-form-eyebrow">STEP 2 OF 2 · VISITOR DETAILS</span>
                <h2 class="sv-form-title" style="font-size:24px;">Who is visiting?</h2>
                <p class="sv-form-sub">Enter your contact details so the owner/agent can send gate security pass upon confirmation.</p>
              </div>

              <!-- Visitor Information Inputs -->
              <div class="sv-section-block">
                <div class="sv-form-row-2">
                  <div class="sv-form-group">
                    <label class="sv-label">Full Name *</label>
                    <input type="text" class="sv-input" name="visitor_name" id="visitorNameInput" required 
                           placeholder="e.g. Rahul Sharma" value="<?= htmlspecialchars($defaultName) ?>" />
                  </div>

                  <div class="sv-form-group">
                    <label class="sv-label">Mobile Number * <span>(10-digit Indian Number)</span></label>
                    <div style="display:flex; align-items:center;">
                      <span style="background:#ECE6DD; border:1px solid var(--sv-border); border-right:none; padding:12px 10px; border-radius:8px 0 0 8px; font-size:13.5px; font-weight:700; color:#475569;">+91</span>
                      <input type="tel" class="sv-input" name="visitor_phone" id="visitorPhoneInput" required maxlength="10" 
                             placeholder="9876543210" value="<?= htmlspecialchars($defaultPhone) ?>" style="border-radius:0 8px 8px 0;" />
                    </div>
                  </div>
                </div>

                <div class="sv-form-row-2">
                  <div class="sv-form-group">
                    <label class="sv-label">Email Address <span>(Optional for confirmation receipt)</span></label>
                    <input type="email" class="sv-input" name="visitor_email" 
                           placeholder="e.g. rahul@example.com" value="<?= htmlspecialchars($defaultEmail) ?>" />
                  </div>

                  <div class="sv-form-group">
                    <label class="sv-label">Number of Visitors</label>
                    <select class="sv-select" name="visitor_count">
                      <option value="1" selected>1 Person (Just me)</option>
                      <option value="2">2 People (Me + Family/Friend)</option>
                      <option value="3">3 People</option>
                      <option value="4">4 People</option>
                      <option value="5">5+ People (Group)</option>
                    </select>
                  </div>
                </div>

                <div class="sv-form-group">
                  <label class="sv-label">Note for Host / Owner <span>(Optional)</span></label>
                  <textarea class="sv-textarea" name="message" rows="3" 
                            placeholder="e.g. Relocating next month, would like to see sunlight, parking space, and ask about lease duration."></textarea>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="sv-actions-row">
                <button type="button" class="sv-btn-cancel" onclick="goToStep(1)">
                  <i class="fa-solid fa-arrow-left"></i> Change Slot
                </button>
                <button type="submit" class="sv-btn-submit" id="btnSubmitVisit">
                  <i class="fa-regular fa-calendar-check"></i> Request Visit
                </button>
              </div>
            </div>

          </form>
        </div>

        <!-- ================= STEP 3: SUCCESS RECEIPT SCREEN ================= -->
        <div class="sv-form-card" id="successCard" style="display:none;">
          <div class="sv-success-card">
            <div class="sv-success-icon-wrap" style="background:#FEF3C7; color:#D97706;">
              <i class="fa-solid fa-clock"></i>
            </div>
            <h2 class="sv-success-title">Visit Request Submitted!</h2>
            <p class="sv-success-sub" id="successMsg">
              Your preferred visit slot has been sent to the property owner/agent for confirmation.
            </p>

            <div class="sv-receipt-box">
              <div class="sv-receipt-row">
                <span class="sv-receipt-label">Visit Reference ID</span>
                <span class="sv-receipt-val" id="receiptId">POV-VISIT-000</span>
              </div>
              <div class="sv-receipt-row">
                <span class="sv-receipt-label">Property</span>
                <span class="sv-receipt-val"><?= htmlspecialchars($property['title']) ?></span>
              </div>
              <div class="sv-receipt-row">
                <span class="sv-receipt-label">Host / Provider</span>
                <span class="sv-receipt-val"><?= htmlspecialchars($property['provider_name']) ?> (<?= htmlspecialchars($property['provider_type']) ?>)</span>
              </div>
              <div class="sv-receipt-row">
                <span class="sv-receipt-label">Requested Date</span>
                <span class="sv-receipt-val" id="receiptDate">--</span>
              </div>
              <div class="sv-receipt-row">
                <span class="sv-receipt-label">Requested Time</span>
                <span class="sv-receipt-val" id="receiptTime">--</span>
              </div>
              <div class="sv-receipt-row">
                <span class="sv-receipt-label">Current Status</span>
                <span class="sv-badge-status sv-status-requested" id="receiptStatus">REQUESTED (Pending Host Approval)</span>
              </div>
            </div>

            <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:8px; padding:12px 14px; margin-bottom:24px; font-size:13px; color:#92400E; text-align:left;">
              <i class="fa-solid fa-circle-info"></i> <strong>What happens next:</strong>
              The owner or agent will review your requested slot. Once confirmed, you will receive full gate access details and directions.
            </div>

            <div class="sv-success-actions">
              <a href="<?= htmlspecialchars(pov_url('rental-detail.php?id=' . $propId)) ?>" class="sv-btn-submit" style="text-decoration:none;">
                Back to Property
              </a>
              <a href="<?= htmlspecialchars(pov_url('rentals-stays.php')) ?>" class="sv-btn-cancel">
                Explore More Rentals
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    const standardSlotsList = ['10:00 AM', '11:00 AM', '12:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM'];
    let selectedDate = '<?= $minDate ?>';
    let selectedSlot = '11:00 AM';
    const propId = <?= (int)$propId ?>;

    // Quick Date Selection
    function selectQuickDate(dateStr, btnEl) {
      document.querySelectorAll('.sv-quick-date-btn').forEach(b => b.classList.remove('is-active'));
      if (btnEl) btnEl.classList.add('is-active');

      selectedDate = dateStr;
      document.getElementById('selectedDateInput').value = dateStr;
      document.getElementById('calendarDateInput').value = dateStr;

      fetchAvailableSlots(dateStr);
      updateSlotSummary();
    }

    // Calendar Picker Selection
    function handleCalendarChange(val) {
      if (!val) return;
      selectedDate = val;
      document.getElementById('selectedDateInput').value = val;

      // Update quick date buttons active state if matches
      document.querySelectorAll('.sv-quick-date-btn').forEach(b => {
        b.classList.toggle('is-active', b.getAttribute('data-date') === val);
      });

      fetchAvailableSlots(val);
      updateSlotSummary();
    }

    // Fetch Booked / Unavailable Slots from Backend
    function fetchAvailableSlots(dateStr) {
      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=slots')) ?>&property_id=' + propId + '&date=' + encodeURIComponent(dateStr))
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            renderSlots(data.unavailable_slots || []);
          } else {
            renderSlots([]);
          }
        })
        .catch(err => {
          renderSlots([]);
        });
    }

    function renderSlots(unavailableSlots) {
      const container = document.getElementById('slotsContainer');
      container.innerHTML = '';

      let foundSelected = false;

      standardSlotsList.forEach(slot => {
        const isUnavailable = unavailableSlots.includes(slot);
        const chip = document.createElement('div');
        chip.className = 'sv-slot-chip';
        chip.setAttribute('data-slot', slot);

        if (isUnavailable) {
          chip.classList.add('is-disabled');
          chip.innerHTML = '<div>' + slot + '</div><span class="sv-slot-chip-sub" style="color:#DC2626;">Unavailable</span>';
        } else {
          if (slot === selectedSlot && !isUnavailable) {
            chip.classList.add('is-selected');
            foundSelected = true;
          }
          chip.innerHTML = '<div>' + slot + '</div>';
          chip.onclick = function() { selectTimeSlot(slot, chip); };
        }

        container.appendChild(chip);
      });

      // If previously selected slot became unavailable, pick the first available
      if (!foundSelected) {
        const firstAvailable = container.querySelector('.sv-slot-chip:not(.is-disabled)');
        if (firstAvailable) {
          const slotVal = firstAvailable.getAttribute('data-slot');
          selectTimeSlot(slotVal, firstAvailable);
        } else {
          selectedSlot = '';
          document.getElementById('selectedTimeSlot').value = '';
          updateSlotSummary();
        }
      }
    }

    // Select a Single Time Slot
    function selectTimeSlot(slot, el) {
      if (el.classList.contains('is-disabled')) return;

      document.querySelectorAll('.sv-slot-chip').forEach(c => c.classList.remove('is-selected'));
      el.classList.add('is-selected');

      selectedSlot = slot;
      document.getElementById('selectedTimeSlot').value = slot;
      updateSlotSummary();
    }

    // Live Date + Time Summary Update
    function updateSlotSummary() {
      const summaryText = document.getElementById('summarySlotText');
      const recapText = document.getElementById('recapSlotText');
      const continueBtn = document.getElementById('btnContinueStep1');

      if (!selectedDate || !selectedSlot) {
        summaryText.innerHTML = '<span style="color:#DC2626;">Please select both a date and an available slot</span>';
        continueBtn.disabled = true;
        return;
      }

      continueBtn.disabled = false;
      const formattedDate = formatDatePretty(selectedDate);
      const fullText = formattedDate + ' at ' + selectedSlot;

      summaryText.innerText = fullText;
      if (recapText) recapText.innerText = fullText;
    }

    function formatDatePretty(dateStr) {
      try {
        const parts = dateStr.split('-');
        if (parts.length === 3) {
          const d = new Date(parts[0], parts[1] - 1, parts[2]);
          const options = { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' };
          return d.toLocaleDateString('en-IN', options);
        }
      } catch (e) {}
      return dateStr;
    }

    // Stepper Navigation
    function goToStep(stepNum) {
      const step1 = document.getElementById('step1Container');
      const step2 = document.getElementById('step2Container');
      const node1 = document.getElementById('stepNode1');
      const node2 = document.getElementById('stepNode2');

      if (stepNum === 2) {
        if (!selectedDate || !selectedSlot) {
          showAlert('Please select a valid date and available time slot before continuing.', 'error');
          return;
        }

        // Validate future date
        const todayStr = '<?= date('Y-m-d') ?>';
        if (selectedDate <= todayStr) {
          showAlert('Please choose tomorrow or an upcoming date.', 'error');
          return;
        }

        step1.classList.remove('is-active');
        step2.classList.add('is-active');

        node1.classList.remove('is-active');
        node1.classList.add('is-completed');
        node2.classList.add('is-active');

        hideAlert();
        window.scrollTo({ top: 140, behavior: 'smooth' });

        // Focus on name input
        const nameInput = document.getElementById('visitorNameInput');
        if (nameInput && !nameInput.value) {
          setTimeout(() => nameInput.focus(), 200);
        }
      } else {
        step2.classList.remove('is-active');
        step1.classList.add('is-active');

        node2.classList.remove('is-active');
        node1.classList.remove('is-completed');
        node1.classList.add('is-active');

        hideAlert();
        window.scrollTo({ top: 140, behavior: 'smooth' });
      }
    }

    // Handle Final Visit Submission
    function handleVisitSubmit(e) {
      e.preventDefault();
      const form = document.getElementById('scheduleVisitMainForm');
      const btn = document.getElementById('btnSubmitVisit');

      // Validate phone number
      const phoneInput = document.getElementById('visitorPhoneInput');
      const phoneVal = phoneInput.value.replace(/\D/g, '');
      if (phoneVal.length !== 10) {
        showAlert('Please enter a valid 10-digit Indian mobile number.', 'error');
        phoneInput.focus();
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting Request...';

      const formData = new FormData(form);

      fetch('<?= htmlspecialchars(pov_url('api-visits.php?action=create')) ?>', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-regular fa-calendar-check"></i> Request Visit';

        if (data.success) {
          // Switch to Step 3: Success Receipt Screen
          document.getElementById('formCard').style.display = 'none';
          document.getElementById('successCard').style.display = 'block';

          document.getElementById('stepNode1').classList.remove('is-active');
          document.getElementById('stepNode1').classList.add('is-completed');
          document.getElementById('stepNode2').classList.remove('is-active');
          document.getElementById('stepNode2').classList.add('is-completed');
          document.getElementById('stepNode3').classList.add('is-active');

          document.getElementById('receiptId').innerText = data.visit.id;
          document.getElementById('receiptDate').innerText = formatDatePretty(data.visit.preferred_date);
          document.getElementById('receiptTime').innerText = data.visit.preferred_time;
          document.getElementById('receiptStatus').innerText = 'REQUESTED (Awaiting Owner Approval)';

          window.scrollTo({ top: 100, behavior: 'smooth' });
        } else {
          showAlert(data.message || 'Unable to submit visit request. Please check your inputs.', 'error');
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-regular fa-calendar-check"></i> Request Visit';
        showAlert('Network error: Unable to submit request. Please try again.', 'error');
      });
    }

    function showAlert(msg, type) {
      const box = document.getElementById('svAlertBox');
      box.style.display = 'block';
      if (type === 'error') {
        box.style.background = '#FEF2F2';
        box.style.color = '#B91C1C';
        box.style.border = '1px solid #FECACA';
      } else {
        box.style.background = '#ECFDF5';
        box.style.color = '#047857';
        box.style.border = '1px solid #A7F3D0';
      }
      box.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + msg;
      box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideAlert() {
      const box = document.getElementById('svAlertBox');
      if (box) box.style.display = 'none';
    }

    // Initial load: fetch slots for tomorrow
    document.addEventListener('DOMContentLoaded', function() {
      fetchAvailableSlots(selectedDate);
      updateSlotSummary();
    });
  </script>

<?php include __DIR__ . '/includes/footer.php'; ?>
