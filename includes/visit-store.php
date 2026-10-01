<?php
/**
 * includes/visit-store.php
 * POV Indian Rentals & Stays - Visit Request & Scheduling Engine
 * Handles persistent JSON storage, validation, audit logs, and lifecycle workflow:
 * REQUESTED -> CONFIRMED -> COMPLETED
 * REQUESTED -> RESCHEDULED
 * REQUESTED -> CANCELLED
 */

if (!defined('POV_ROOT')) {
  require_once __DIR__ . '/config.php';
}

function pov_visits_dir(): string {
  $dir = POV_ROOT . '/storage/visits';
  if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
  }
  $htaccess = $dir . '/.htaccess';
  if (!file_exists($htaccess)) {
    @file_put_contents($htaccess, "Require all denied\n");
  }
  return $dir;
}

function pov_visits_file(): string {
  return pov_visits_dir() . '/visits.json';
}

function pov_audit_file(): string {
  return pov_visits_dir() . '/audit_log.json';
}

/**
 * Load all visits with initial default seed if empty
 */
function pov_load_all_visits(): array {
  $file = pov_visits_file();
  if (file_exists($file)) {
    $content = @file_get_contents($file);
    if (!empty($content)) {
      $data = json_decode($content, true);
      if (is_array($data)) {
        return $data;
      }
    }
  }

  // Initial seed demo visits for testing all statuses
  $seed = [
    [
      'id' => 'POV-VISIT-101-901',
      'property_id' => 101,
      'property_title' => 'Sunlit 2 BHK Apartment near Sindhi Camp Metro',
      'property_location' => 'Station Road / Banipark, Jaipur, Rajasthan',
      'property_img' => 'realestate_03.webp',
      'owner_name' => 'Rajeshwar Sharma',
      'owner_phone' => '+91 98290 11223',
      'owner_role' => 'Direct Owner',
      'visitor_name' => 'Aditya Verma',
      'visitor_phone' => '9820123456',
      'visitor_email' => 'aditya.verma@example.com',
      'visitor_count' => 2,
      'preferred_date' => date('Y-m-d', strtotime('+2 days')),
      'preferred_time' => '11:00 AM',
      'alternate_date' => date('Y-m-d', strtotime('+3 days')),
      'alternate_time' => '04:00 PM',
      'message' => 'Moving to Jaipur with family. Would like to see natural sunlight in master bedroom and parking space.',
      'status' => 'CONFIRMED',
      'owner_message' => 'Visit confirmed! Please meet at Building A lobby entrance. Security has your name.',
      'confirmed_date' => date('Y-m-d', strtotime('+2 days')),
      'confirmed_time' => '11:00 AM',
      'cancelled_by' => null,
      'cancellation_reason' => null,
      'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
      'updated_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
    ],
    [
      'id' => 'POV-VISIT-102-902',
      'property_id' => 102,
      'property_title' => 'Ananya Premium Girls PG & Hostel with Mess',
      'property_location' => 'Landmark City / Kunhari, Kota, Rajasthan',
      'property_img' => 'college_01.jpg',
      'owner_name' => 'Mrs. Sunita Verma',
      'owner_phone' => '+91 94140 88990',
      'owner_role' => 'Warden Managed',
      'visitor_name' => 'Sneha Agarwal',
      'visitor_phone' => '9414567890',
      'visitor_email' => 'sneha.allen@example.com',
      'visitor_count' => 1,
      'preferred_date' => date('Y-m-d', strtotime('+1 day')),
      'preferred_time' => '04:00 PM',
      'alternate_date' => null,
      'alternate_time' => null,
      'message' => 'Enrolling in Allen Samyak batch. Need to inspect AC double sharing room and pure-veg mess.',
      'status' => 'REQUESTED',
      'owner_message' => null,
      'confirmed_date' => null,
      'confirmed_time' => null,
      'cancelled_by' => null,
      'cancellation_reason' => null,
      'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
      'updated_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
    ],
    [
      'id' => 'POV-VISIT-103-903',
      'property_id' => 103,
      'property_title' => 'Verified 3 BHK Builder Floor near Cyber Hub',
      'property_location' => 'DLF Phase 2, Gurugram, Delhi NCR',
      'property_img' => 'realestate_01.webp',
      'owner_name' => 'Kapoor Real Estate',
      'owner_phone' => '+91 99100 44556',
      'owner_role' => 'Verified Local Agent',
      'visitor_name' => 'Vikram Malhotra',
      'visitor_phone' => '9811234567',
      'visitor_email' => 'v.malhotra@corporate.in',
      'visitor_count' => 3,
      'preferred_date' => date('Y-m-d', strtotime('+4 days')),
      'preferred_time' => '02:00 PM',
      'alternate_date' => null,
      'alternate_time' => null,
      'message' => 'Relocating from Bangalore for tech executive role at Cyber Hub.',
      'status' => 'RESCHEDULED',
      'owner_message' => 'Current tenant is hosting family on Thursday 2 PM. Can we do Saturday 4:00 PM instead?',
      'confirmed_date' => date('Y-m-d', strtotime('+6 days')),
      'confirmed_time' => '04:00 PM',
      'cancelled_by' => null,
      'cancellation_reason' => null,
      'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
      'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
    ]
  ];

  @file_put_contents($file, json_encode($seed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
  return $seed;
}

/**
 * Save visits to persistent JSON storage
 */
function pov_save_all_visits(array $visits): bool {
  $file = pov_visits_file();
  return (bool)@file_put_contents($file, json_encode(array_values($visits), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Add audit trail entry
 */
function pov_add_visit_audit(string $visitId, string $action, string $by, ?string $prevStatus, string $newStatus, string $note = ''): void {
  $file = pov_audit_file();
  $log = [];
  if (file_exists($file)) {
    $c = @file_get_contents($file);
    if ($c) $log = json_decode($c, true) ?: [];
  }

  $entry = [
    'id' => 'AUDIT-' . time() . '-' . mt_rand(100, 999),
    'visit_id' => $visitId,
    'action' => $action,
    'by' => $by,
    'prev_status' => $prevStatus,
    'new_status' => $newStatus,
    'note' => $note,
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
  ];

  array_unshift($log, $entry);
  if (count($log) > 500) {
    $log = array_slice($log, 0, 500);
  }
  @file_put_contents($file, json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Validate visit date
 */
function pov_validate_visit_date(string $dateStr): array {
  $dateStr = trim($dateStr);
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
    return ['valid' => false, 'error' => 'Please provide a valid date in YYYY-MM-DD format.'];
  }

  $ts = strtotime($dateStr);
  if ($ts === false) {
    return ['valid' => false, 'error' => 'Invalid calendar date provided.'];
  }

  $today = strtotime(date('Y-m-d'));
  if ($ts < $today) {
    return ['valid' => false, 'error' => 'Visit date cannot be in the past. Please select today or an upcoming date.'];
  }

  $maxDate = strtotime('+30 days', $today);
  if ($ts > $maxDate) {
    return ['valid' => false, 'error' => 'Visits can only be scheduled up to 30 days in advance.'];
  }

  return ['valid' => true, 'timestamp' => $ts];
}

/**
 * Create a new visit request
 */
function pov_create_visit_request(array $input): array {
  // 1. Validate property
  $propId = (int)($input['property_id'] ?? 0);
  require_once __DIR__ . '/data.php';
  $property = pov_find_rental($propId);
  if (!$property) {
    $property = pov_get_rental_full_detail($propId);
  }
  if (!$property) {
    return ['success' => false, 'message' => 'This property is currently unavailable for visits.'];
  }

  // 2. Validate visitor details
  $name = trim($input['visitor_name'] ?? '');
  if (strlen($name) < 2) {
    return ['success' => false, 'message' => 'Please enter your full name (at least 2 characters).'];
  }

  $phoneRaw = trim($input['visitor_phone'] ?? '');
  $phoneClean = preg_replace('/[^0-9]/', '', $phoneRaw);
  if (strlen($phoneClean) === 12 && str_starts_with($phoneClean, '91')) {
    $phoneClean = substr($phoneClean, 2);
  }
  if (!preg_match('/^[6-9]\d{9}$/', $phoneClean)) {
    return ['success' => false, 'message' => 'Please enter a valid 10-digit Indian mobile number starting with 6-9.'];
  }

  $email = trim($input['visitor_email'] ?? '');
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ['success' => false, 'message' => 'Please enter a valid email address or leave it empty.'];
  }

  $visitorCount = max(1, min(10, (int)($input['visitor_count'] ?? 1)));

  // 3. Validate Date & Time
  $prefDate = trim($input['preferred_date'] ?? '');
  $dateCheck = pov_validate_visit_date($prefDate);
  if (!$dateCheck['valid']) {
    return ['success' => false, 'message' => $dateCheck['error']];
  }

  $allowedSlots = [
    '10:00 AM', '11:00 AM', '12:00 PM', 
    '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM'
  ];
  $prefTime = trim($input['preferred_time'] ?? '');
  if (!in_array($prefTime, $allowedSlots, true)) {
    $prefTime = '11:00 AM';
  }

  $altDate = trim($input['alternate_date'] ?? '');
  if ($altDate !== '') {
    $altCheck = pov_validate_visit_date($altDate);
    if (!$altCheck['valid']) {
      $altDate = null;
    }
  } else {
    $altDate = null;
  }

  $altTime = trim($input['alternate_time'] ?? '');
  if ($altTime !== '' && !in_array($altTime, $allowedSlots, true)) {
    $altTime = null;
  }

  $message = trim($input['message'] ?? '');
  if (strlen($message) > 500) {
    $message = substr($message, 0, 500);
  }

  // 4. Generate unique ID
  $visitId = 'POV-VISIT-' . $propId . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 5));

  $newVisit = [
    'id' => $visitId,
    'property_id' => $propId,
    'property_title' => $property['title'],
    'property_location' => ($property['locality'] ?? '') . ', ' . ($property['city'] ?? '') . ' (' . ($property['state'] ?? '') . ')',
    'property_img' => $property['img'] ?? 'realestate_03.webp',
    'owner_name' => $property['provider_name'] ?? 'Direct Owner',
    'owner_phone' => $property['phone'] ?? '+91 98290 11223',
    'owner_role' => $property['provider_type'] ?? 'Host',
    'visitor_name' => $name,
    'visitor_phone' => $phoneClean,
    'visitor_email' => $email,
    'visitor_count' => $visitorCount,
    'preferred_date' => $prefDate,
    'preferred_time' => $prefTime,
    'alternate_date' => $altDate,
    'alternate_time' => $altTime,
    'message' => $message,
    'status' => 'REQUESTED',
    'owner_message' => null,
    'confirmed_date' => null,
    'confirmed_time' => null,
    'cancelled_by' => null,
    'cancellation_reason' => null,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ];

  // 5. Save to storage
  $visits = pov_load_all_visits();
  array_unshift($visits, $newVisit);
  pov_save_all_visits($visits);

  // 6. Log audit trail
  pov_add_visit_audit($visitId, 'VISIT_REQUESTED', $name . ' (' . $phoneClean . ')', null, 'REQUESTED', 'Initial visit request submitted');

  // Save phone in session for automatic "My Visits" tracking
  $_SESSION['last_visitor_phone'] = $phoneClean;
  $_SESSION['last_visitor_name'] = $name;

  return [
    'success' => true,
    'message' => 'Visit request submitted successfully.',
    'sub_message' => 'Your preferred date and time have been sent to ' . htmlspecialchars($property['provider_name'] ?? 'the host') . '. They will confirm your visit.',
    'visit' => $newVisit,
  ];
}

/**
 * Find single visit by ID
 */
function pov_find_visit(string $visitId): ?array {
  $visits = pov_load_all_visits();
  foreach ($visits as $v) {
    if ($v['id'] === $visitId) {
      return $v;
    }
  }
  return null;
}

/**
 * Update visit status with proper audit and transitions
 */
function pov_update_visit_status(string $visitId, string $newStatus, array $extra = [], string $by = 'System'): array {
  $newStatus = strtoupper(trim($newStatus));
  $allowedStatuses = ['REQUESTED', 'CONFIRMED', 'RESCHEDULED', 'CANCELLED', 'COMPLETED', 'NO_SHOW'];
  if (!in_array($newStatus, $allowedStatuses, true)) {
    return ['success' => false, 'message' => 'Invalid status transition.'];
  }

  $visits = pov_load_all_visits();
  $found = false;

  foreach ($visits as &$v) {
    if ($v['id'] === $visitId) {
      $found = true;
      $prevStatus = $v['status'];

      $v['status'] = $newStatus;
      $v['updated_at'] = date('Y-m-d H:i:s');

      if ($newStatus === 'CONFIRMED') {
        $v['confirmed_date'] = $extra['confirmed_date'] ?? $v['preferred_date'];
        $v['confirmed_time'] = $extra['confirmed_time'] ?? $v['preferred_time'];
        if (!empty($extra['owner_message'])) {
          $v['owner_message'] = trim($extra['owner_message']);
        }
      } elseif ($newStatus === 'RESCHEDULED') {
        if (!empty($extra['new_date'])) {
          $v['confirmed_date'] = $extra['new_date'];
        }
        if (!empty($extra['new_time'])) {
          $v['confirmed_time'] = $extra['new_time'];
        }
        if (!empty($extra['owner_message'])) {
          $v['owner_message'] = trim($extra['owner_message']);
        }
      } elseif ($newStatus === 'CANCELLED') {
        $v['cancelled_by'] = $by;
        $v['cancellation_reason'] = $extra['reason'] ?? 'Cancelled by request';
      }

      pov_add_visit_audit($visitId, 'STATUS_UPDATE_' . $newStatus, $by, $prevStatus, $newStatus, $extra['owner_message'] ?? ($extra['reason'] ?? ''));
      break;
    }
  }

  if (!$found) {
    return ['success' => false, 'message' => 'Visit request not found.'];
  }

  pov_save_all_visits($visits);
  return ['success' => true, 'message' => 'Visit status updated to ' . $newStatus];
}

/**
 * Filter visits for User / Visitor
 */
function pov_get_user_visits(?string $phone = null): array {
  $visits = pov_load_all_visits();
  if (!$phone || trim($phone) === '') {
    return $visits; // Return all in local dev / demo mode
  }
  $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
  return array_values(array_filter($visits, function($v) use ($cleanPhone) {
    return str_contains($v['visitor_phone'] ?? '', $cleanPhone);
  }));
}

/**
 * Get summary metrics for Owner / Admin
 */
function pov_get_visit_metrics(): array {
  $visits = pov_load_all_visits();
  $metrics = [
    'total' => count($visits),
    'requested' => 0,
    'confirmed' => 0,
    'rescheduled' => 0,
    'cancelled' => 0,
    'completed' => 0,
  ];

  foreach ($visits as $v) {
    $st = strtolower($v['status'] ?? 'requested');
    if (isset($metrics[$st])) {
      $metrics[$st]++;
    }
  }
  return $metrics;
}

/**
 * Indian friendly date formatter (e.g. "Wed, 15 Oct 2026")
 */
function pov_format_visit_date(?string $dateStr): string {
  if (!$dateStr) return 'TBD';
  $ts = strtotime($dateStr);
  if (!$ts) return $dateStr;
  return date('D, d M Y', $ts);
}

/**
 * Return unavailable/booked slots for a property on a specific date
 */
function pov_get_unavailable_slots(int $propId, string $dateStr): array {
  $visits = pov_load_all_visits();
  $unavailable = [];
  foreach ($visits as $v) {
    if ((int)($v['property_id'] ?? 0) === $propId) {
      $status = strtoupper($v['status'] ?? '');
      if ($status === 'CONFIRMED' || $status === 'REQUESTED') {
        $vDate = $v['confirmed_date'] ?? $v['preferred_date'] ?? '';
        if ($vDate === $dateStr) {
          $vTime = $v['confirmed_time'] ?? $v['preferred_time'] ?? '';
          if (!empty($vTime)) {
            $unavailable[] = $vTime;
          }
        }
      }
    }
  }
  return array_values(array_unique($unavailable));
}
