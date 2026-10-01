<?php
/**
 * api-visits.php
 * REST API & Form Handler for POV Indian Schedule Visit Feature
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/visit-store.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'create';

switch ($action) {
  case 'create':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
      exit;
    }

    $input = $_POST;
    // Also check JSON body if posted via fetch
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
      $json = json_decode($rawInput, true);
      if (is_array($json)) {
        $input = array_merge($input, $json);
      }
    }

    $result = pov_create_visit_request($input);
    if (!empty($result['success'])) {
      http_response_code(201);
    } else {
      http_response_code(400);
    }
    echo json_encode($result);
    exit;

  case 'confirm':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
      exit;
    }

    $visitId = trim($_POST['visit_id'] ?? '');
    $ownerMessage = trim($_POST['owner_message'] ?? 'Confirmed by owner/agent. Please arrive on time.');
    $result = pov_update_visit_status($visitId, 'CONFIRMED', ['owner_message' => $ownerMessage], 'Owner');
    echo json_encode($result);
    exit;

  case 'reschedule':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
      exit;
    }

    $visitId = trim($_POST['visit_id'] ?? '');
    $newDate = trim($_POST['new_date'] ?? '');
    $newTime = trim($_POST['new_time'] ?? '');
    $ownerMessage = trim($_POST['owner_message'] ?? 'Proposed alternate time slot.');

    if (empty($newDate) || empty($newTime)) {
      echo json_encode(['success' => false, 'message' => 'Please provide both new date and new time slot.']);
      exit;
    }

    $result = pov_update_visit_status($visitId, 'RESCHEDULED', [
      'new_date' => $newDate,
      'new_time' => $newTime,
      'owner_message' => $ownerMessage,
    ], 'Owner');
    echo json_encode($result);
    exit;

  case 'cancel':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
      exit;
    }

    $visitId = trim($_POST['visit_id'] ?? '');
    $reason = trim($_POST['reason'] ?? 'Cancelled by request');
    $by = trim($_POST['cancelled_by'] ?? 'User');

    $result = pov_update_visit_status($visitId, 'CANCELLED', ['reason' => $reason], $by);
    echo json_encode($result);
    exit;

  case 'complete':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
      exit;
    }

    $visitId = trim($_POST['visit_id'] ?? '');
    $result = pov_update_visit_status($visitId, 'COMPLETED', [], 'Owner');
    echo json_encode($result);
    exit;

  case 'my':
    $phone = $_GET['phone'] ?? ($_SESSION['last_visitor_phone'] ?? null);
    $visits = pov_get_user_visits($phone);
    echo json_encode(['success' => true, 'visits' => $visits]);
    exit;

  case 'get':
    $visitId = trim($_GET['id'] ?? '');
    $visit = pov_find_visit($visitId);
    if ($visit) {
      echo json_encode(['success' => true, 'visit' => $visit]);
    } else {
      http_response_code(404);
      echo json_encode(['success' => false, 'message' => 'Visit not found.']);
    }
    exit;

  case 'slots':
    $propId = (int)($_GET['property_id'] ?? 0);
    $dateStr = trim($_GET['date'] ?? date('Y-m-d'));
    $allSlots = ['10:00 AM', '11:00 AM', '12:00 PM', '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM'];
    $booked = function_exists('pov_get_unavailable_slots') ? pov_get_unavailable_slots($propId, $dateStr) : [];
    
    // Also disable past slots if date is today
    $pastSlots = [];
    if ($dateStr === date('Y-m-d')) {
      $currentTime = time();
      foreach ($allSlots as $slot) {
        $slotTs = strtotime($dateStr . ' ' . $slot);
        if ($slotTs && $slotTs <= $currentTime + 1800) { // slot is earlier or within next 30 mins
          $pastSlots[] = $slot;
        }
      }
    }
    
    $unavailable = array_values(array_unique(array_merge($booked, $pastSlots)));
    echo json_encode([
      'success' => true,
      'date' => $dateStr,
      'all_slots' => $allSlots,
      'unavailable_slots' => $unavailable,
      'booked_slots' => $booked,
      'past_slots' => $pastSlots
    ]);
    exit;

  default:
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit;
}
