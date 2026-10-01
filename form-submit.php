<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/form-store.php';
require_once __DIR__ . '/includes/api-client.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . pov_url('index.php'));
  exit;
}

$type = trim((string)($_POST['form_type'] ?? ''));
$redirect = pov_safe_redirect_path((string)($_POST['redirect'] ?? 'index.php'));

$messages = [
  'contact' => 'Message sent. We will get back to you soon.',
  'list-business' => 'Listing submitted for verification. Our team will review it shortly.',
  'contributor' => 'Pitch received — thank you! Our editorial team will follow up.',
  'signup' => 'Welcome to the POV Indian community!',
  'subscribe' => 'Subscribed to POV Indian updates.',
  'requirement-post' => 'Your property requirement has been posted! Matched verified owners & agents will contact you directly.',
  'schedule-visit' => 'Visit requested successfully! The owner/host has been notified and will confirm your slot.',
  'rental-enquiry' => 'Enquiry submitted! The host/owner will get back to you via your preferred channel.',
  'list-rental' => 'Rental listing submitted for verification! Our onboarding team will activate it shortly.',
];

$ok = false;
$fields = [];

// ---- Forgot Password: AJAX JSON response ----
if ($type === 'forgot_password') {
  header('Content-Type: application/json; charset=utf-8');
  $email = trim((string)($_POST['email'] ?? ''));
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
  }
  $result = pov_password_reset_request($email);
  echo json_encode($result);
  exit;
}

switch ($type) {
  case 'login':
    $email = trim((string)($_POST['email'] ?? ''));
    $password = trim((string)($_POST['password'] ?? ''));
    if ($email !== '' && $password !== '') {
      // 1. Try Node.js backend if online
      $loginRes = null;
      try {
        $loginRes = node_api_post('auth/login', [
          'email' => $email,
          'password' => $password,
        ]);
      } catch (\Throwable $e) {
        $loginRes = null;
      }

      if (!empty($loginRes['success']) && !empty($loginRes['token'])) {
        $_SESSION['auth_token'] = $loginRes['token'];
        $_SESSION['auth_user'] = $loginRes['user'];
        $_SESSION['user'] = $loginRes['user'];
        if (($loginRes['user']['role'] ?? '') === 'admin') {
          $_SESSION['admin_token'] = $loginRes['token'];
          $_SESSION['admin_user'] = $loginRes['user'];
          header('Location: ' . pov_url('admin-dashboard.php'));
          exit;
        }
        pov_flash_set('form_success', 'Welcome back, ' . ($loginRes['user']['name'] ?? 'User') . '!');
        header('Location: ' . pov_url($redirect));
        exit;
      }

      // 2. Folder-based user verification (storage/users/<folder>/user.json)
      $folderAuth = pov_user_verify_login($email, $password);
      if (!empty($folderAuth['success'])) {
        $_SESSION['auth_token'] = $folderAuth['token'];
        $_SESSION['auth_user'] = $folderAuth['user'];
        $_SESSION['user'] = $folderAuth['user'];
        if (($folderAuth['user']['role'] ?? '') === 'admin') {
          $_SESSION['admin_token'] = $folderAuth['token'];
          $_SESSION['admin_user'] = $folderAuth['user'];
          header('Location: ' . pov_url('admin-dashboard.php'));
          exit;
        }
        pov_flash_set('form_success', 'Welcome back, ' . ($folderAuth['user']['name'] ?? 'User') . '!');
        header('Location: ' . pov_url($redirect));
        exit;
      }

      // 3. Fallback for default superadmin
      if (strtolower($email) === 'admin@povindian.com' && $password === 'Admin@123456') {
        $_SESSION['admin_token'] = 'offline_admin_token';
        $_SESSION['admin_user'] = [
          'id' => 1,
          'name' => 'POV Indian SuperAdmin',
          'email' => 'admin@povindian.com',
          'role' => 'admin',
        ];
        header('Location: ' . pov_url('admin-dashboard.php'));
        exit;
      }

      pov_flash_set('form_error', $folderAuth['message'] ?? 'Invalid email or password.');
    } else {
      pov_flash_set('form_error', 'Please enter both email and password.');
    }
    header('Location: ' . pov_url($redirect));
    exit;

  case 'contact':
    $fields = [
      'name' => trim((string)($_POST['name'] ?? '')),
      'email' => trim((string)($_POST['email'] ?? '')),
      'subject' => trim((string)($_POST['subject'] ?? '')),
      'message' => trim((string)($_POST['message'] ?? '')),
    ];
    $ok = $fields['name'] !== '' && filter_var($fields['email'], FILTER_VALIDATE_EMAIL) && $fields['subject'] !== '' && $fields['message'] !== '';
    break;

  case 'list-business':
    $fields = [
      'business_name' => trim((string)($_POST['business_name'] ?? '')),
      'category' => trim((string)($_POST['category'] ?? '')),
      'city' => trim((string)($_POST['city'] ?? '')),
      'phone' => trim((string)($_POST['phone'] ?? '')),
      'email' => trim((string)($_POST['email'] ?? '')),
      'description' => trim((string)($_POST['description'] ?? '')),
    ];
    $ok = $fields['business_name'] !== '' && $fields['category'] !== '' && $fields['city'] !== '' && $fields['phone'] !== '' && filter_var($fields['email'], FILTER_VALIDATE_EMAIL) && $fields['description'] !== '';
    break;

  case 'contributor':
    $fields = [
      'name' => trim((string)($_POST['name'] ?? '')),
      'email' => trim((string)($_POST['email'] ?? '')),
      'topic' => trim((string)($_POST['topic'] ?? '')),
      'pitch' => trim((string)($_POST['pitch'] ?? '')),
    ];
    $ok = $fields['name'] !== '' && filter_var($fields['email'], FILTER_VALIDATE_EMAIL) && $fields['topic'] !== '' && $fields['pitch'] !== '';
    break;

  case 'signup':
    $full_name = trim((string)($_POST['full_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = trim((string)($_POST['password'] ?? ''));
    $interests = $_POST['interest'] ?? [];
    if (!is_array($interests)) {
      $interests = [];
    }

    if ($full_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      pov_flash_set('form_error', 'Please enter your full name and a valid email address.');
      header('Location: ' . pov_url($redirect));
      exit;
    }

    if (strlen($password) < 6) {
      pov_flash_set('form_error', 'Password must be at least 6 characters long.');
      header('Location: ' . pov_url($redirect));
      exit;
    }

    if (empty($interests)) {
      pov_flash_set('form_error', 'Please select at least one category of interest.');
      header('Location: ' . pov_url($redirect));
      exit;
    }

    $cleanInterests = array_values(array_filter(array_map('trim', $interests)));

    // 1. Create user in proper folder-based storage (storage/users/<user_folder>/user.json)
    $folderRes = pov_user_create([
      'name' => $full_name,
      'email' => $email,
      'password' => $password,
      'interests' => $cleanInterests,
      'role' => 'user',
    ]);

    // 2. Also try to sync with Node.js API if reachable
    try {
      @node_api_post('auth/register', [
        'name' => $full_name,
        'email' => $email,
        'password' => $password,
        'interests' => $cleanInterests,
        'role' => 'user',
      ]);
    } catch (\Throwable $e) {
      // Graceful fallback to folder storage
    }

    if (!empty($folderRes['success'])) {
      // Auto-login the newly created user
      $_SESSION['auth_user'] = $folderRes['user'];
      $_SESSION['user'] = $folderRes['user'];
      $_SESSION['auth_token'] = $folderRes['token'];
      pov_flash_set('form_success', 'Welcome to POV Indian, ' . htmlspecialchars($full_name) . '! Your feed has been personalized with your selected categories.');
    } else {
      pov_flash_set('form_error', $folderRes['message'] ?? 'Registration failed. Please try again.');
    }
    header('Location: ' . pov_url($redirect));
    exit;

  case 'update_interests':
    $user = $_SESSION['auth_user'] ?? $_SESSION['user'] ?? null;
    if (!$user || empty($user['email'])) {
      pov_flash_set('form_error', 'Please log in to update your interests.');
      header('Location: ' . pov_url($redirect));
      exit;
    }

    $interests = $_POST['interest'] ?? [];
    if (!is_array($interests)) {
      $interests = [];
    }

    if (empty($interests)) {
      pov_flash_set('form_error', 'Please select at least one category of interest.');
      header('Location: ' . pov_url($redirect));
      exit;
    }

    $cleanInterests = array_values(array_filter(array_map('trim', $interests)));

    // 1. Update in folder-based user JSON
    $userFolder = $user['folder'] ?? pov_user_folder_name($user['email']);
    $userFile = pov_users_dir() . '/' . $userFolder . '/user.json';
    if (file_exists($userFile)) {
      $uData = json_decode(@file_get_contents($userFile) ?: '', true);
      if (is_array($uData)) {
        $uData['interests'] = $cleanInterests;
        $uData['updated_at'] = date('c');
        @file_put_contents($userFile, json_encode($uData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
      }
    }

    // 2. Update in Node.js MySQL backend
    try {
      @node_api_post('auth/update-interests', [
        'email' => $user['email'],
        'interests' => $cleanInterests,
      ]);
    } catch (\Throwable $e) {}

    // 3. Update active session
    $_SESSION['auth_user']['interests'] = $cleanInterests;
    $_SESSION['user']['interests'] = $cleanInterests;

    pov_flash_set('form_success', 'Your category interests and personalized feed have been updated successfully!');
    header('Location: ' . pov_url($redirect));
    exit;

  case 'subscribe':
    $fields = [
      'email' => trim((string)($_POST['email'] ?? '')),
    ];
    $ok = (bool)filter_var($fields['email'], FILTER_VALIDATE_EMAIL);
    break;

  case 'requirement-post':
    $email = trim((string)($_POST['email'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email = 'seeker_' . substr(md5(uniqid()), 0, 8) . '@povindian-lead.local';
    }
    $fields = [
      'name' => trim((string)($_POST['name'] ?? 'Seeker')),
      'phone' => trim((string)($_POST['phone'] ?? '')),
      'email' => $email,
      'city' => trim((string)($_POST['city'] ?? '')),
      'landmark' => trim((string)($_POST['landmark'] ?? '')),
      'rental_type' => trim((string)($_POST['rental_type'] ?? 'long_term')),
      'category' => trim((string)($_POST['category'] ?? 'Apartment / Flat')),
      'budget_min' => trim((string)($_POST['budget_min'] ?? '')),
      'budget_max' => trim((string)($_POST['budget_max'] ?? '')),
      'move_in_date' => trim((string)($_POST['move_in_date'] ?? '')),
      'household_type' => trim((string)($_POST['household_type'] ?? 'Any')),
      'must_haves' => isset($_POST['must_haves']) && is_array($_POST['must_haves']) ? implode(', ', $_POST['must_haves']) : trim((string)($_POST['must_haves'] ?? '')),
      'contact_preference' => trim((string)($_POST['contact_preference'] ?? 'whatsapp')),
      'notes' => trim((string)($_POST['notes'] ?? '')),
      'subject' => 'New Requirement: ' . trim((string)($_POST['category'] ?? 'Property')) . ' near ' . trim((string)($_POST['landmark'] ?? $_POST['city'] ?? 'India')),
      'message' => 'Requirement in ' . trim((string)($_POST['city'] ?? '')) . ' near ' . trim((string)($_POST['landmark'] ?? '')) . ' (Budget: ' . trim((string)($_POST['budget_min'] ?? '')) . ' - ' . trim((string)($_POST['budget_max'] ?? '')) . ')',
    ];
    $ok = $fields['phone'] !== '' || filter_var($fields['email'], FILTER_VALIDATE_EMAIL);
    break;

  case 'schedule-visit':
    $email = trim((string)($_POST['email'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email = 'visitor_' . substr(md5(uniqid()), 0, 8) . '@povindian-visit.local';
    }
    $fields = [
      'name' => trim((string)($_POST['name'] ?? '')),
      'phone' => trim((string)($_POST['phone'] ?? '')),
      'email' => $email,
      'property_id' => trim((string)($_POST['property_id'] ?? '')),
      'property_title' => trim((string)($_POST['property_title'] ?? '')),
      'visit_date' => trim((string)($_POST['visit_date'] ?? '')),
      'visit_slot' => trim((string)($_POST['visit_slot'] ?? '10:00 AM - 1:00 PM')),
      'visitors_count' => trim((string)($_POST['visitors_count'] ?? '1')),
      'notes' => trim((string)($_POST['notes'] ?? '')),
      'subject' => 'Visit Request for: ' . trim((string)($_POST['property_title'] ?? 'Listing #' . ($_POST['property_id'] ?? ''))),
      'message' => 'Visit requested for date: ' . trim((string)($_POST['visit_date'] ?? '')) . ' (' . trim((string)($_POST['visit_slot'] ?? '')) . ') by ' . trim((string)($_POST['name'] ?? '')) . ' (' . trim((string)($_POST['phone'] ?? '')) . ')',
    ];
    $ok = $fields['name'] !== '' && $fields['phone'] !== '' && $fields['visit_date'] !== '';
    break;

  case 'rental-enquiry':
    $email = trim((string)($_POST['email'] ?? ''));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email = 'guest_' . substr(md5(uniqid()), 0, 8) . '@povindian-guest.local';
    }
    $fields = [
      'name' => trim((string)($_POST['name'] ?? '')),
      'phone' => trim((string)($_POST['phone'] ?? '')),
      'email' => $email,
      'property_id' => trim((string)($_POST['property_id'] ?? '')),
      'property_title' => trim((string)($_POST['property_title'] ?? '')),
      'enquiry_type' => trim((string)($_POST['enquiry_type'] ?? 'availability')),
      'check_in' => trim((string)($_POST['check_in'] ?? '')),
      'check_out' => trim((string)($_POST['check_out'] ?? '')),
      'guests' => trim((string)($_POST['guests'] ?? '1')),
      'message' => trim((string)($_POST['message'] ?? 'Is this property/room available for the requested period?')),
      'subject' => 'Rental/Stay Enquiry: ' . trim((string)($_POST['property_title'] ?? 'Property #' . ($_POST['property_id'] ?? ''))),
    ];
    $ok = $fields['phone'] !== '' || filter_var($fields['email'], FILTER_VALIDATE_EMAIL);
    break;

  case 'list-rental':
    $fields = [
      'name' => trim((string)($_POST['owner_name'] ?? '')),
      'phone' => trim((string)($_POST['phone'] ?? '')),
      'email' => trim((string)($_POST['email'] ?? '')),
      'role' => trim((string)($_POST['role'] ?? 'owner')),
      'rental_type' => trim((string)($_POST['rental_type'] ?? 'long_term')),
      'property_type' => trim((string)($_POST['property_type'] ?? 'Apartment')),
      'city' => trim((string)($_POST['city'] ?? '')),
      'landmark' => trim((string)($_POST['landmark'] ?? '')),
      'monthly_rent' => trim((string)($_POST['monthly_rent'] ?? '')),
      'security_deposit' => trim((string)($_POST['security_deposit'] ?? '')),
      'furnishing' => trim((string)($_POST['furnishing'] ?? 'Semi-Furnished')),
      'tenant_preference' => trim((string)($_POST['tenant_preference'] ?? 'Family or Working Professionals')),
      'amenities' => isset($_POST['amenities']) && is_array($_POST['amenities']) ? implode(', ', $_POST['amenities']) : trim((string)($_POST['amenities'] ?? '')),
      'description' => trim((string)($_POST['description'] ?? '')),
      'subject' => 'New Rental Listing Submission: ' . trim((string)($_POST['property_type'] ?? 'Property')) . ' in ' . trim((string)($_POST['city'] ?? '')),
      'message' => 'Submitted by ' . trim((string)($_POST['role'] ?? 'owner')) . ' ' . trim((string)($_POST['owner_name'] ?? '')) . ' near ' . trim((string)($_POST['landmark'] ?? '')),
    ];
    $ok = $fields['name'] !== '' && $fields['phone'] !== '' && $fields['city'] !== '';
    break;
}

if ($ok && isset($messages[$type])) {
  // Store locally as safe backup
  pov_store_submission($type, $fields);

  // Send to Node.js MySQL backend
  $node_payload = [
    'type' => $type,
    'name' => $fields['name'] ?? $fields['business_name'] ?? $fields['full_name'] ?? null,
    'email' => $fields['email'] ?? '',
    'phone' => $fields['phone'] ?? null,
    'subject' => $fields['subject'] ?? $fields['topic'] ?? null,
    'message' => $fields['message'] ?? $fields['description'] ?? $fields['pitch'] ?? null,
    'extra_data' => $fields,
  ];
  node_api_post('forms/submit', $node_payload);

  pov_flash_set('form_success', $messages[$type]);
} else {
  pov_flash_set('form_error', 'Please check the form and try again.');
}

header('Location: ' . pov_url($redirect));
exit;

