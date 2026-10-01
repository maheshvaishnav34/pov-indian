<?php
/**
 * Persist form submissions and user accounts to local storage (no DB required on XAMPP).
 * Implements proper folder-based storage for users: storage/users/<user_folder>/user.json
 */
if (!defined('POV_ROOT')) {
  require_once __DIR__ . '/config.php';
}

function pov_storage_dir(): string {
  $dir = POV_ROOT . '/storage/submissions';
  if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
  }
  return $dir;
}

function pov_users_dir(): string {
  $dir = POV_ROOT . '/storage/users';
  if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
  }
  // Ensure .htaccess prevents direct public access to users data
  $htaccess = $dir . '/.htaccess';
  if (!file_exists($htaccess)) {
    @file_put_contents($htaccess, "Require all denied\n");
  }
  return $dir;
}

/**
 * Generate a clean, deterministic, filesystem-safe user folder name based on email.
 * e.g. user_vikram_example_com_a1b2c3
 */
function pov_user_folder_name(string $email): string {
  $cleanEmail = strtolower(trim($email));
  $sanitized = preg_replace('/[^a-z0-9]/i', '_', $cleanEmail);
  $sanitized = trim(preg_replace('/_+/', '_', $sanitized), '_');
  $shortSanitized = substr($sanitized, 0, 32);
  $hash = substr(md5($cleanEmail), 0, 6);
  return 'user_' . $shortSanitized . '_' . $hash;
}

/**
 * Find user by email across all user storage folders.
 */
function pov_user_find_by_email(string $email): ?array {
  $cleanEmail = strtolower(trim($email));
  if ($cleanEmail === '') {
    return null;
  }

  $usersDir = pov_users_dir();

  // 1. Direct deterministic folder check: storage/users/<folder>/user.json
  $folderName = pov_user_folder_name($cleanEmail);
  $directFile = $usersDir . '/' . $folderName . '/user.json';
  if (file_exists($directFile)) {
    $content = @file_get_contents($directFile);
    if ($content) {
      $data = json_decode($content, true);
      if (is_array($data) && strtolower(trim($data['email'] ?? '')) === $cleanEmail) {
        return $data;
      }
    }
  }

  // 2. Scan all subdirectories in storage/users/ in case folder name differs
  $dirs = glob($usersDir . '/*', GLOB_ONLYDIR);
  if ($dirs) {
    foreach ($dirs as $dir) {
      $file = $dir . '/user.json';
      if (file_exists($file)) {
        $content = @file_get_contents($file);
        if ($content) {
          $data = json_decode($content, true);
          if (is_array($data) && strtolower(trim($data['email'] ?? '')) === $cleanEmail) {
            return $data;
          }
        }
      }
    }
  }

  // 3. Fallback scan for flat json files: storage/users/*.json
  $flatFiles = glob($usersDir . '/*.json');
  if ($flatFiles) {
    foreach ($flatFiles as $file) {
      $content = @file_get_contents($file);
      if ($content) {
        $data = json_decode($content, true);
        if (is_array($data) && strtolower(trim($data['email'] ?? '')) === $cleanEmail) {
          return $data;
        }
      }
    }
  }

  return null;
}

/**
 * Seed default superadmin into folder storage if not already present.
 */
function pov_seed_admin_if_missing(): void {
  $adminEmail = 'admin@povindian.com';
  if (pov_user_find_by_email($adminEmail) !== null) {
    return;
  }

  $usersDir = pov_users_dir();
  $folderName = pov_user_folder_name($adminEmail);
  $userDir = $usersDir . '/' . $folderName;
  if (!is_dir($userDir)) {
    @mkdir($userDir, 0755, true);
  }

  $adminRecord = [
    'id' => 1,
    'name' => 'POV Indian SuperAdmin',
    'email' => $adminEmail,
    'password_hash' => password_hash('Admin@123456', PASSWORD_DEFAULT),
    'role' => 'admin',
    'interests' => ['Editorial', 'Listings', 'System Administration'],
    'phone' => '+91 98765 43210',
    'status' => 'active',
    'is_active' => true,
    'created_at' => date('c'),
    'updated_at' => date('c'),
    'folder' => $folderName,
  ];

  @file_put_contents(
    $userDir . '/user.json',
    json_encode($adminRecord, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
  );
}

/**
 * Create a new user in proper folder-based storage: storage/users/<user_folder>/user.json
 */
function pov_user_create(array $fields): array {
  $name = trim((string)($fields['name'] ?? $fields['full_name'] ?? ''));
  $email = strtolower(trim((string)($fields['email'] ?? '')));
  $password = (string)($fields['password'] ?? '');
  $interests = $fields['interests'] ?? [];
  $role = trim((string)($fields['role'] ?? 'user')) ?: 'user';
  $phone = trim((string)($fields['phone'] ?? ''));

  if ($name === '') {
    return ['success' => false, 'message' => 'Please enter your full name.'];
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ['success' => false, 'message' => 'Please enter a valid email address.'];
  }
  if (strlen($password) < 6) {
    return ['success' => false, 'message' => 'Password must be at least 6 characters long.'];
  }

  // Ensure default admin exists
  pov_seed_admin_if_missing();

  // Check if user already exists
  if (pov_user_find_by_email($email) !== null) {
    return ['success' => false, 'message' => 'An account with this email already exists. Please sign in instead.'];
  }

  $usersDir = pov_users_dir();
  $folderName = pov_user_folder_name($email);
  $userDir = $usersDir . '/' . $folderName;

  if (!is_dir($userDir)) {
    if (!@mkdir($userDir, 0755, true)) {
      return ['success' => false, 'message' => 'Failed to create user directory. Check permissions.'];
    }
  }

  // Generate unique user ID
  $userId = 'usr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3));

  $interestsList = is_array($interests) ? array_values(array_map('strval', $interests)) : [$interests];

  $record = [
    'id' => $userId,
    'name' => $name,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'role' => $role,
    'interests' => $interestsList,
    'phone' => $phone,
    'status' => 'active',
    'is_active' => true,
    'created_at' => date('c'),
    'updated_at' => date('c'),
    'folder' => $folderName,
  ];

  $userJsonFile = $userDir . '/user.json';
  $saved = @file_put_contents(
    $userJsonFile,
    json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
  );

  if ($saved === false) {
    return ['success' => false, 'message' => 'Failed to write user profile file.'];
  }

  // Also log into submissions
  pov_store_submission('signup', [
    'user_id' => $userId,
    'full_name' => $name,
    'email' => $email,
    'phone' => $phone,
    'interests' => $interestsList,
    'role' => $role,
    'folder' => $folderName,
  ]);

  $safeUser = $record;
  unset($safeUser['password_hash']);

  return [
    'success' => true,
    'message' => 'Account created successfully!',
    'user' => $safeUser,
    'token' => 'token_' . bin2hex(random_bytes(16)),
  ];
}

/**
 * Verify user login from folder-based storage.
 */
function pov_user_verify_login(string $email, string $password): array {
  $cleanEmail = strtolower(trim($email));
  if ($cleanEmail === '' || $password === '') {
    return ['success' => false, 'message' => 'Email and password are required.'];
  }

  // Ensure admin seed
  pov_seed_admin_if_missing();

  // Check admin fallback
  if ($cleanEmail === 'admin@povindian.com' && $password === 'Admin@123456') {
    return [
      'success' => true,
      'user' => [
        'id' => 1,
        'name' => 'POV Indian SuperAdmin',
        'email' => 'admin@povindian.com',
        'role' => 'admin',
        'phone' => '+91 98765 43210',
        'interests' => 'SuperAdmin',
        'is_active' => true,
        'created_at' => date('c'),
      ],
      'token' => 'offline_admin_token_' . bin2hex(random_bytes(8)),
    ];
  }

  $user = pov_user_find_by_email($cleanEmail);
  if (!$user) {
    return ['success' => false, 'message' => 'No account found with this email. Please sign up.'];
  }

  $hash = $user['password_hash'] ?? '';
  if (!$hash || !password_verify($password, $hash)) {
    return ['success' => false, 'message' => 'Incorrect password. Please try again.'];
  }

  if (($user['status'] ?? 'active') !== 'active' || empty($user['is_active'])) {
    return ['success' => false, 'message' => 'This account is currently inactive.'];
  }

  $safeUser = $user;
  unset($safeUser['password_hash']);

  return [
    'success' => true,
    'user' => $safeUser,
    'token' => 'token_' . bin2hex(random_bytes(16)),
  ];
}

/**
 * Retrieve all users stored across folders for admin view.
 */
function pov_get_all_users(): array {
  pov_seed_admin_if_missing();

  $usersDir = pov_users_dir();
  $users = [];
  $seenEmails = [];

  // 1. Folders: storage/users/*/user.json
  $dirs = glob($usersDir . '/*', GLOB_ONLYDIR);
  if ($dirs) {
    foreach ($dirs as $dir) {
      $file = $dir . '/user.json';
      if (file_exists($file)) {
        $content = @file_get_contents($file);
        if ($content) {
          $data = json_decode($content, true);
          if (is_array($data) && !empty($data['email'])) {
            $emailKey = strtolower(trim($data['email']));
            if (!isset($seenEmails[$emailKey])) {
              $seenEmails[$emailKey] = true;
              $interestsStr = is_array($data['interests'] ?? null)
                ? implode(', ', $data['interests'])
                : (string)($data['interests'] ?? '-');

              $users[] = [
                'id' => $data['id'] ?? basename($dir),
                'name' => $data['name'] ?? 'User',
                'email' => $data['email'],
                'role' => $data['role'] ?? 'user',
                'phone' => $data['phone'] ?? '-',
                'interests' => $interestsStr ?: '-',
                'is_active' => ($data['status'] ?? 'active') === 'active' && !empty($data['is_active']),
                'created_at' => $data['created_at'] ?? date('c'),
              ];
            }
          }
        }
      }
    }
  }

  // Sort newest first
  usort($users, function($a, $b) {
    return strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0');
  });

  return $users;
}

/**
 * Retrieve all submissions from storage/submissions/*.json for admin view.
 */
function pov_get_all_submissions(): array {
  $dir = pov_storage_dir();
  $files = glob($dir . '/*.json');
  if (!$files) {
    return [];
  }

  $submissions = [];
  foreach ($files as $file) {
    $content = @file_get_contents($file);
    if (!$content) continue;
    $data = json_decode($content, true);
    if (!is_array($data)) continue;

    $fields = $data['fields'] ?? [];
    $type = $data['type'] ?? 'form';
    $name = $fields['name'] ?? $fields['business_name'] ?? $fields['full_name'] ?? 'User';
    $email = $fields['email'] ?? '';
    $phone = $fields['phone'] ?? '-';
    $subject = $fields['subject'] ?? $fields['topic'] ?? ucfirst($type) . ' Submission';
    $message = $fields['message'] ?? $fields['description'] ?? $fields['pitch'] ?? '';
    if ($message === '' && !empty($fields['interests'])) {
      $message = 'Interests: ' . (is_array($fields['interests']) ? implode(', ', $fields['interests']) : $fields['interests']);
    }

    $submissions[] = [
      'id' => basename($file, '.json'),
      'type' => $type,
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
      'subject' => $subject,
      'message' => $message,
      'created_at' => $data['created_at'] ?? date('c', filemtime($file)),
      'status' => 'received',
    ];
  }

  usort($submissions, function($a, $b) {
    return strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0');
  });

  return $submissions;
}

function pov_store_submission(string $type, array $fields): bool {
  $safe_type = preg_replace('/[^a-z0-9_-]/i', '', $type) ?: 'form';
  $payload = [
    'type' => $safe_type,
    'created_at' => date('c'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'fields' => $fields,
  ];
  $file = pov_storage_dir() . '/' . $safe_type . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.json';
  return file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function pov_flash_set(string $key, $value): void {
  $_SESSION['_flash'][$key] = $value;
}

function pov_flash_get(string $key) {
  if (!isset($_SESSION['_flash'][$key])) {
    return null;
  }
  $value = $_SESSION['_flash'][$key];
  unset($_SESSION['_flash'][$key]);
  return $value;
}

function pov_safe_redirect_path(string $path): string {
  $path = trim($path);
  if ($path === '' || preg_match('#^(https?:)?//#i', $path) || str_contains($path, '..')) {
    return 'index.php';
  }
  return ltrim($path, '/');
}

/**
 * =========================================================================
 * FOLDER-BASED LISTINGS STORAGE (storage/listings/listing_<id>.json)
 * =========================================================================
 */

function pov_listings_dir(): string {
  $dir = POV_ROOT . '/storage/listings';
  if (!is_dir($dir)) {
    @mkdir($dir, 0755, true);
  }
  return $dir;
}

/**
 * Seed initial 24 listings if folder has not yet been initialized.
 */
function pov_seed_listings_if_missing(): void {
  $dir = pov_listings_dir();
  $seedMarker = $dir . '/.seeded';
  if (file_exists($seedMarker)) {
    return;
  }

  $initial = [
    ['id' => 1, 'img' => 'realestate_03.webp', 'avatar' => 'ryan.webp', 'cat' => 'Real Estate', 'cat_slug' => 'real-estate', 'rating' => '5', 'title' => 'Heritage Haveli Stay in Jaipur', 'loc' => 'Jaipur, Rajasthan', 'phone' => '+91 98765 43210', 'price' => 'From ₹4,500/night', 'badge' => 'featured', 'desc' => 'A verified heritage stay with courtyard dining, local host experiences, and authentic Rajasthani hospitality.'],
    ['id' => 2, 'img' => 'realestate_01.webp', 'avatar' => 'james.webp', 'cat' => 'Real Estate', 'cat_slug' => 'real-estate', 'rating' => '4', 'title' => 'Verified 3BHK in Whitefield', 'loc' => 'Bangalore, Karnataka', 'phone' => '+91 99887 66554', 'price' => '₹1.05 Cr', 'badge' => '', 'desc' => 'Spacious family apartment close to IT parks, schools, and everyday conveniences in Whitefield.'],
    ['id' => 3, 'img' => 'restaurant_05.webp', 'avatar' => 'emma.webp', 'cat' => 'Restaurants & Cafés', 'cat_slug' => 'restaurants', 'rating' => '5', 'title' => 'Coastal Seafood Kitchen', 'loc' => 'Chennai, Tamil Nadu', 'phone' => '+91 91234 56780', 'price' => '₹800 – ₹2,200', 'badge' => 'featured', 'desc' => 'Fresh coastal flavours with chef-led tasting menus and verified local reviews.'],
    ['id' => 4, 'img' => 'cafe_01.webp', 'avatar' => 'david.webp', 'cat' => 'Restaurants & Cafés', 'cat_slug' => 'restaurants', 'rating' => '4', 'title' => 'Filter Coffee & Local Bites', 'loc' => 'Bangalore, Karnataka', 'phone' => '+91 90123 45678', 'price' => '₹150 – ₹600', 'badge' => 'top', 'desc' => 'Neighbourhood cafe known for strong filter coffee, regional snacks, and student-friendly seating.'],
    ['id' => 5, 'img' => 'tourism_01.jpg', 'avatar' => 'lisa.webp', 'cat' => 'Tourism & Travel', 'cat_slug' => 'tourism', 'rating' => '5', 'title' => 'Kerala Backwaters Day Experience', 'loc' => 'Alleppey, Kerala', 'phone' => '+91 98700 11223', 'price' => '₹2,999/person', 'badge' => '', 'desc' => 'Guided day cruise through canals with lunch on board and village stopovers curated by locals.'],
    ['id' => 6, 'img' => 'wellness_01.jpg', 'avatar' => 'emma.webp', 'cat' => 'Wellness & Ayurveda', 'cat_slug' => 'wellness', 'rating' => '5', 'title' => 'Ayurveda Wellness Retreat', 'loc' => 'Kozhikode, Kerala', 'phone' => '+91 97654 32100', 'price' => '₹3,500 – ₹12,000', 'badge' => '', 'desc' => 'Doctor-supervised therapies, yoga sessions, and sattvic meals in a calm Kerala setting.'],
    ['id' => 7, 'img' => 'hotel_02.jpg', 'avatar' => 'lisa.webp', 'cat' => 'Hotels & Accommodations', 'cat_slug' => 'hotels', 'rating' => '4', 'title' => 'Boutique Homestay Near Fort Kochi', 'loc' => 'Kochi, Kerala', 'phone' => '+91 95555 22110', 'price' => 'From ₹2,800/night', 'badge' => '', 'desc' => 'Design-forward rooms steps from cafes, art galleries, and the historic Fort Kochi promenade.'],
    ['id' => 8, 'img' => 'college_01.jpg', 'avatar' => 'james.webp', 'cat' => 'Colleges & Universities', 'cat_slug' => 'colleges', 'rating' => '5', 'title' => 'Campus Guide: Tech University Hub', 'loc' => 'Delhi NCR', 'phone' => '+91 91111 22334', 'price' => 'Student verified', 'badge' => 'bump', 'desc' => 'Local student insights on hostels, commute, clubs, and campus life for international applicants.'],
    ['id' => 9, 'img' => 'tourism_02.jpg', 'avatar' => 'david.webp', 'cat' => 'Tourism & Travel', 'cat_slug' => 'tourism', 'rating' => '4', 'title' => 'Golden Triangle Private Guide', 'loc' => 'Delhi NCR', 'phone' => '+91 93333 44556', 'price' => '₹6,500/day', 'badge' => '', 'desc' => 'Licensed private guide for Delhi–Agra–Jaipur with flexible itineraries beyond tourist checklists.'],
    ['id' => 10, 'img' => 'hotel_01.jpg', 'avatar' => 'ryan.webp', 'cat' => 'Hotels & Accommodations', 'cat_slug' => 'hotels', 'rating' => '5', 'title' => 'Luxury Heritage Resort Udaipur', 'loc' => 'Udaipur, Rajasthan', 'phone' => '+91 94444 55667', 'price' => 'From ₹9,900/night', 'badge' => 'featured', 'desc' => 'Lake-facing suites, curated cultural evenings, and verified concierge support for travelers.'],
    ['id' => 11, 'img' => 'healthcare_01.jpg', 'avatar' => 'emma.webp', 'cat' => 'Healthcare & Hospitals', 'cat_slug' => 'healthcare', 'rating' => '5', 'title' => 'Multispecialty Care Centre', 'loc' => 'Hyderabad, Telangana', 'phone' => '+91 98765 10101', 'price' => 'Consultation from ₹600', 'badge' => 'featured', 'desc' => 'Verified hospital network with specialist OPD, diagnostics, and international patient support.'],
    ['id' => 12, 'img' => 'healthcare_02.jpg', 'avatar' => 'james.webp', 'cat' => 'Healthcare & Hospitals', 'cat_slug' => 'healthcare', 'rating' => '4', 'title' => 'Family Clinic & Diagnostics', 'loc' => 'Pune, Maharashtra', 'phone' => '+91 98765 10102', 'price' => 'From ₹400', 'badge' => '', 'desc' => 'Neighbourhood clinic known for transparent pricing and reliable lab partners.'],
    ['id' => 13, 'img' => 'manufacturing_01.jpg', 'avatar' => 'david.webp', 'cat' => 'Manufacturing & Industrial', 'cat_slug' => 'manufacturing', 'rating' => '5', 'title' => 'Precision Components Unit', 'loc' => 'Ahmedabad, Gujarat', 'phone' => '+91 98765 20201', 'price' => 'B2B quotes', 'badge' => 'top', 'desc' => 'ISO-aligned manufacturer supplying automotive and appliance OEMs across west India.'],
    ['id' => 14, 'img' => 'manufacturing_02.jpg', 'avatar' => 'lisa.webp', 'cat' => 'Manufacturing & Industrial', 'cat_slug' => 'manufacturing', 'rating' => '4', 'title' => 'Textile Processing Hub', 'loc' => 'Surat, Gujarat', 'phone' => '+91 98765 20202', 'price' => 'Bulk orders', 'badge' => '', 'desc' => 'Dyeing and finishing partner for apparel brands with verified capacity and lead times.'],
    ['id' => 15, 'img' => 'legal_01.jpg', 'avatar' => 'ryan.webp', 'cat' => 'Legal & Consulting Firms', 'cat_slug' => 'legal', 'rating' => '5', 'title' => 'Corporate Counsel Associates', 'loc' => 'Mumbai, Maharashtra', 'phone' => '+91 98765 30301', 'price' => 'Retainer / hourly', 'badge' => 'featured', 'desc' => 'Company law, contracts, and startup advisory with clear engagement terms.'],
    ['id' => 16, 'img' => 'legal_02.jpg', 'avatar' => 'emma.webp', 'cat' => 'Legal & Consulting Firms', 'cat_slug' => 'legal', 'rating' => '4', 'title' => 'Business Strategy Consultants', 'loc' => 'Bangalore, Karnataka', 'phone' => '+91 98765 30302', 'price' => 'Project based', 'badge' => '', 'desc' => 'Go-to-market and operations consulting for Indian SMEs expanding nationally.'],
    ['id' => 17, 'img' => 'garden_01.jpg', 'avatar' => 'lisa.webp', 'cat' => 'Home & Garden', 'cat_slug' => 'home-garden', 'rating' => '5', 'title' => 'Urban Garden Studio', 'loc' => 'Delhi NCR', 'phone' => '+91 98765 40401', 'price' => 'From ₹2,500', 'badge' => 'top', 'desc' => 'Balcony and terrace landscaping with native plants and maintenance plans.'],
    ['id' => 18, 'img' => 'home_interior_01.jpg', 'avatar' => 'james.webp', 'cat' => 'Home & Garden', 'cat_slug' => 'home-garden', 'rating' => '4', 'title' => 'Interior Refresh Collective', 'loc' => 'Chennai, Tamil Nadu', 'phone' => '+91 98765 40402', 'price' => 'From ₹15,000', 'badge' => '', 'desc' => 'Home styling and modular upgrades with transparent material lists.'],
    ['id' => 19, 'img' => 'automotive_03.webp', 'avatar' => 'david.webp', 'cat' => 'Auto Services', 'cat_slug' => 'auto', 'rating' => '5', 'title' => 'Express Detailing Garage', 'loc' => 'Mumbai, Maharashtra', 'phone' => '+91 98765 50501', 'price' => '₹999 – ₹7,500', 'badge' => 'featured', 'desc' => 'Doorstep pickup detailing and periodic service packages for city cars.'],
    ['id' => 20, 'img' => 'car_01.webp', 'avatar' => 'ryan.webp', 'cat' => 'Auto Services', 'cat_slug' => 'auto', 'rating' => '4', 'title' => 'Trusted Multi-Brand Workshop', 'loc' => 'Jaipur, Rajasthan', 'phone' => '+91 98765 50502', 'price' => 'Estimate on inspection', 'badge' => '', 'desc' => 'Verified mechanics for routine service, AC repair, and insurance jobs.'],
    ['id' => 21, 'img' => 'beauty_01.jpg', 'avatar' => 'emma.webp', 'cat' => 'Health & Beauty', 'cat_slug' => 'beauty', 'rating' => '5', 'title' => 'Heritage Beauty Lounge', 'loc' => 'Kolkata, West Bengal', 'phone' => '+91 98765 60601', 'price' => '₹500 – ₹4,000', 'badge' => 'featured', 'desc' => 'Bridal and everyday beauty services with hygienic, verified studio standards.'],
    ['id' => 22, 'img' => 'beauty_02.jpg', 'avatar' => 'lisa.webp', 'cat' => 'Health & Beauty', 'cat_slug' => 'beauty', 'rating' => '4', 'title' => 'Skin & Wellness Studio', 'loc' => 'Hyderabad, Telangana', 'phone' => '+91 98765 60602', 'price' => 'From ₹1,200', 'badge' => '', 'desc' => 'Dermatology-informed facials and wellness add-ons with appointment booking.'],
    ['id' => 23, 'img' => 'college_02.jpg', 'avatar' => 'james.webp', 'cat' => 'Colleges & Universities', 'cat_slug' => 'colleges', 'rating' => '4', 'title' => 'Design School Orientation Guide', 'loc' => 'Ahmedabad, Gujarat', 'phone' => '+91 98765 70701', 'price' => 'Free guide', 'badge' => '', 'desc' => 'Student-led overview of studios, housing, and portfolio culture for design campuses.'],
    ['id' => 24, 'img' => 'wellness_02.jpg', 'avatar' => 'david.webp', 'cat' => 'Wellness & Ayurveda', 'cat_slug' => 'wellness', 'rating' => '5', 'title' => 'Yoga & Panchakarma House', 'loc' => 'Rishikesh, Uttarakhand', 'phone' => '+91 98765 80801', 'price' => 'From ₹2,000/day', 'badge' => 'top', 'desc' => 'River-side yoga packages with authentic Ayurveda consultations.'],
  ];

  foreach ($initial as $item) {
    $file = $dir . '/listing_' . (int)$item['id'] . '.json';
    if (!file_exists($file)) {
      @file_put_contents($file, json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
  }

  @file_put_contents($seedMarker, date('c'));
}

/**
 * Retrieve all listings from storage/listings/*.json.
 */
function pov_get_all_listings(string $sort = 'asc'): array {
  pov_seed_listings_if_missing();

  $dir = pov_listings_dir();
  $files = glob($dir . '/listing_*.json');
  if (!$files) {
    return [];
  }

  $listings = [];
  foreach ($files as $file) {
    $content = @file_get_contents($file);
    if (!$content) continue;
    $data = json_decode($content, true);
    if (is_array($data) && isset($data['id'])) {
      $listings[] = $data;
    }
  }

  usort($listings, function($a, $b) use ($sort) {
    $idA = (int)($a['id'] ?? 0);
    $idB = (int)($b['id'] ?? 0);
    return $sort === 'desc' ? ($idB <=> $idA) : ($idA <=> $idB);
  });

  return $listings;
}

/**
 * Add a new listing to folder-based storage.
 */
function pov_listing_add(array $data): array {
  pov_seed_listings_if_missing();

  $title = trim((string)($data['title'] ?? ''));
  if ($title === '') {
    return ['success' => false, 'message' => 'Listing title is required.'];
  }

  $dir = pov_listings_dir();

  // Find highest ID
  $maxId = 0;
  $files = glob($dir . '/listing_*.json');
  if ($files) {
    foreach ($files as $f) {
      if (preg_match('/listing_(\d+)\.json$/', $f, $m)) {
        $maxId = max($maxId, (int)$m[1]);
      }
    }
  }
  $newId = $maxId + 1;

  // Resolve Category name & slug
  $categoryId = (int)($data['category_id'] ?? 1);
  $catLookup = [
    1 => ['name' => 'Hotels & Accommodations', 'slug' => 'hotels'],
    2 => ['name' => 'Restaurants & Cafés', 'slug' => 'restaurants'],
    3 => ['name' => 'Colleges & Universities', 'slug' => 'colleges'],
    4 => ['name' => 'Wellness & Ayurveda', 'slug' => 'wellness'],
    5 => ['name' => 'Real Estate', 'slug' => 'real-estate'],
    6 => ['name' => 'Tourism & Travel', 'slug' => 'tourism'],
    7 => ['name' => 'Healthcare & Hospitals', 'slug' => 'healthcare'],
    8 => ['name' => 'Manufacturing & Industrial', 'slug' => 'manufacturing'],
    9 => ['name' => 'Legal & Consulting Firms', 'slug' => 'legal'],
    10 => ['name' => 'Home & Garden', 'slug' => 'home-garden'],
    11 => ['name' => 'Auto Services', 'slug' => 'auto'],
    12 => ['name' => 'Health & Beauty', 'slug' => 'beauty'],
  ];

  $catName = $data['cat'] ?? ($catLookup[$categoryId]['name'] ?? 'Business');
  $catSlug = $data['cat_slug'] ?? ($catLookup[$categoryId]['slug'] ?? 'business');

  // Resolve Location
  $locationId = !empty($data['location_id']) ? (int)$data['location_id'] : null;
  $locLookup = [
    1 => 'Delhi NCR', 2 => 'Mumbai', 3 => 'Bangalore', 4 => 'Kolkata',
    5 => 'Chennai', 6 => 'Jaipur', 7 => 'Hyderabad', 8 => 'Pune',
    9 => 'Kochi', 10 => 'Udaipur', 11 => 'Goa', 12 => 'Ahmedabad'
  ];
  $locationText = trim((string)($data['location_text'] ?? $data['loc'] ?? ''));
  if ($locationText === '' && $locationId && isset($locLookup[$locationId])) {
    $locationText = $locLookup[$locationId];
  }
  if ($locationText === '') {
    $locationText = 'India';
  }

  $ratingVal = !empty($data['rating']) ? (string)$data['rating'] : '5.0';
  $badgeVal = trim((string)($data['badge'] ?? ''));

  $record = [
    'id' => $newId,
    'title' => $title,
    'cat' => $catName,
    'cat_slug' => $catSlug,
    'category_id' => $categoryId,
    'loc' => $locationText,
    'location_id' => $locationId,
    'phone' => trim((string)($data['phone'] ?? '')) ?: '+91 98765 43210',
    'price' => trim((string)($data['price'] ?? '')) ?: 'On Request',
    'rating' => $ratingVal,
    'badge' => $badgeVal,
    'img' => trim((string)($data['image'] ?? $data['img'] ?? 'realestate_03.webp')),
    'avatar' => trim((string)($data['avatar'] ?? 'ryan.webp')),
    'desc' => trim((string)($data['description'] ?? $data['desc'] ?? '')),
    'meta_title' => trim((string)($data['meta_title'] ?? '')),
    'meta_description' => trim((string)($data['meta_description'] ?? '')),
    'meta_keywords' => trim((string)($data['meta_keywords'] ?? '')),
    'canonical_url' => trim((string)($data['canonical_url'] ?? '')),
    'og_image' => trim((string)($data['og_image'] ?? '')),
    'schema_type' => trim((string)($data['schema_type'] ?? 'LocalBusiness')),
    'created_at' => date('c'),
    'updated_at' => date('c'),
  ];

  $file = $dir . '/listing_' . $newId . '.json';
  $saved = @file_put_contents($file, json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  if ($saved === false) {
    return ['success' => false, 'message' => 'Failed to save listing file to storage.'];
  }

  // Also sync to Node API in background if online
  try {
    @node_api_post('listings', [
      'title' => $record['title'],
      'category_id' => $record['category_id'],
      'location_id' => $record['location_id'],
      'location_text' => $record['loc'],
      'phone' => $record['phone'],
      'price' => $record['price'],
      'rating' => (float)$record['rating'],
      'badge' => $record['badge'],
      'image' => $record['img'],
      'avatar' => $record['avatar'],
      'description' => $record['desc'],
      'meta_title' => $record['meta_title'],
      'meta_description' => $record['meta_description'],
      'meta_keywords' => $record['meta_keywords'],
      'canonical_url' => $record['canonical_url'],
      'og_image' => $record['og_image'],
      'schema_type' => $record['schema_type']
    ]);
  } catch (\Throwable $e) {}

  return [
    'success' => true,
    'id' => $newId,
    'listing' => $record,
    'message' => "Listing '{$title}' (#{$newId}) created successfully!",
  ];
}

/**
 * Update an existing listing in folder-based storage and sync to MySQL.
 */
function pov_listing_update(int $id, array $data): array {
  pov_seed_listings_if_missing();

  $dir = pov_listings_dir();
  $file = $dir . '/listing_' . $id . '.json';

  $record = [];
  if (file_exists($file)) {
    $existing = json_decode(@file_get_contents($file) ?: '', true);
    if (is_array($existing)) {
      $record = $existing;
    }
  }

  $record['id'] = $id;
  if (!empty($data['title'])) {
    $record['title'] = trim((string)$data['title']);
  }

  $catLookup = [
    1 => ['name' => 'Hotels & Accommodations', 'slug' => 'hotels'],
    2 => ['name' => 'Restaurants & Cafés', 'slug' => 'restaurants'],
    3 => ['name' => 'Colleges & Universities', 'slug' => 'colleges'],
    4 => ['name' => 'Wellness & Ayurveda', 'slug' => 'wellness'],
    5 => ['name' => 'Real Estate', 'slug' => 'real-estate'],
    6 => ['name' => 'Tourism & Travel', 'slug' => 'tourism'],
    7 => ['name' => 'Healthcare & Hospitals', 'slug' => 'healthcare'],
    8 => ['name' => 'Manufacturing & Industrial', 'slug' => 'manufacturing'],
    9 => ['name' => 'Legal & Consulting Firms', 'slug' => 'legal'],
    10 => ['name' => 'Home & Garden', 'slug' => 'home-garden'],
    11 => ['name' => 'Auto Services', 'slug' => 'auto'],
    12 => ['name' => 'Health & Beauty', 'slug' => 'beauty'],
  ];

  if (isset($data['category_id'])) {
    $catId = (int)$data['category_id'];
    $record['category_id'] = $catId;
    if (isset($catLookup[$catId])) {
      $record['cat'] = $catLookup[$catId]['name'];
      $record['cat_slug'] = $catLookup[$catId]['slug'];
    }
  }
  if (!empty($data['cat'])) {
    $record['cat'] = trim((string)$data['cat']);
  }
  if (!empty($data['cat_slug'])) {
    $record['cat_slug'] = trim((string)$data['cat_slug']);
  }

  $locLookup = [
    1 => 'Delhi NCR', 2 => 'Mumbai', 3 => 'Bangalore', 4 => 'Kolkata',
    5 => 'Chennai', 6 => 'Jaipur', 7 => 'Hyderabad', 8 => 'Pune',
    9 => 'Kochi', 10 => 'Udaipur', 11 => 'Goa', 12 => 'Ahmedabad'
  ];
  if (isset($data['location_id'])) {
    $record['location_id'] = !empty($data['location_id']) ? (int)$data['location_id'] : null;
  }
  if (!empty($data['location_text'])) {
    $record['loc'] = trim((string)$data['location_text']);
  } elseif (!empty($data['loc'])) {
    $record['loc'] = trim((string)$data['loc']);
  } elseif (!empty($record['location_id']) && isset($locLookup[$record['location_id']])) {
    $record['loc'] = $locLookup[$record['location_id']];
  }

  if (isset($data['phone'])) $record['phone'] = trim((string)$data['phone']);
  if (isset($data['price'])) $record['price'] = trim((string)$data['price']);
  if (isset($data['rating'])) $record['rating'] = (string)$data['rating'];
  if (isset($data['badge'])) $record['badge'] = trim((string)$data['badge']);
  if (!empty($data['image'])) $record['img'] = trim((string)$data['image']);
  elseif (!empty($data['img'])) $record['img'] = trim((string)$data['img']);
  if (!empty($data['avatar'])) $record['avatar'] = trim((string)$data['avatar']);
  if (isset($data['description'])) $record['desc'] = trim((string)$data['description']);
  elseif (isset($data['desc'])) $record['desc'] = trim((string)$data['desc']);
  if (isset($data['meta_title'])) $record['meta_title'] = trim((string)$data['meta_title']);
  if (isset($data['meta_description'])) $record['meta_description'] = trim((string)$data['meta_description']);
  if (isset($data['meta_keywords'])) $record['meta_keywords'] = trim((string)$data['meta_keywords']);
  if (isset($data['canonical_url'])) $record['canonical_url'] = trim((string)$data['canonical_url']);
  if (isset($data['og_image'])) $record['og_image'] = trim((string)$data['og_image']);
  if (isset($data['schema_type'])) $record['schema_type'] = trim((string)$data['schema_type']);
  $record['updated_at'] = date('c');

  @file_put_contents($file, json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  // Sync to Node.js MySQL backend
  try {
    if (function_exists('node_api_put')) {
      @node_api_put('listings/' . $id, [
        'title' => $record['title'] ?? '',
        'category_id' => $record['category_id'] ?? 1,
        'location_id' => $record['location_id'] ?? null,
        'location_text' => $record['loc'] ?? '',
        'phone' => $record['phone'] ?? '',
        'price' => $record['price'] ?? '',
        'rating' => (float)($record['rating'] ?? 5.0),
        'badge' => $record['badge'] ?? '',
        'image' => $record['img'] ?? '',
        'avatar' => $record['avatar'] ?? '',
        'description' => $record['desc'] ?? '',
        'meta_title' => $record['meta_title'] ?? null,
        'meta_description' => $record['meta_description'] ?? null,
        'meta_keywords' => $record['meta_keywords'] ?? null,
        'canonical_url' => $record['canonical_url'] ?? null,
        'og_image' => $record['og_image'] ?? null,
        'schema_type' => $record['schema_type'] ?? 'LocalBusiness'
      ]);
    }
  } catch (\Throwable $e) {}

  return [
    'success' => true,
    'id' => $id,
    'listing' => $record,
    'message' => "Listing #{$id} ('" . ($record['title'] ?? '') . "') updated successfully!",
  ];
}

/**
 * Delete a listing from folder-based storage.
 */
function pov_listing_delete(int $id): array {
  pov_seed_listings_if_missing();

  $dir = pov_listings_dir();
  $file = $dir . '/listing_' . $id . '.json';

  $deleted = false;
  if (file_exists($file)) {
    $deleted = @unlink($file);
  } else {
    // If already missing from disk, treat as deleted
    $deleted = true;
  }

  // Also attempt delete on Node API if available
  try {
    @node_api_delete('listings/' . $id);
  } catch (\Throwable $e) {}

  if ($deleted) {
    return [
      'success' => true,
      'message' => "Listing #{$id} was successfully deleted.",
    ];
  }

  return [
    'success' => false,
    'message' => "Could not delete listing file #{$id}.",
  ];
}

/**
 * =========================================================================
 * FOLDER-BASED CATEGORIES, LOCATIONS & BLOG STORAGE
 * =========================================================================
 */

function pov_categories_dir(): string {
  $dir = POV_ROOT . '/storage/categories';
  if (!is_dir($dir)) @mkdir($dir, 0755, true);
  return $dir;
}

function pov_seed_categories_if_missing(): void {
  $dir = pov_categories_dir();
  $seedMarker = $dir . '/.seeded';
  if (file_exists($seedMarker)) {
    return;
  }

  $defaults = [
    ['id' => 1, 'name' => 'Hotels & Accommodations', 'count' => '1,240+', 'slug' => 'hotels', 'icon' => 'event.svg', 'desc' => 'From luxury resorts to authentic homestays across India.', 'show_in_signup' => true],
    ['id' => 2, 'name' => 'Restaurants & Cafés', 'count' => '2,850+', 'slug' => 'restaurants', 'icon' => 'cafe.svg', 'desc' => 'Authentic culinary experiences from street food to fine dining.', 'show_in_signup' => true],
    ['id' => 3, 'name' => 'Colleges & Universities', 'count' => '980+', 'slug' => 'colleges', 'icon' => 'colleges.svg', 'desc' => 'Top educational institutions with student reviews and campus guides.', 'show_in_signup' => true],
    ['id' => 4, 'name' => 'Wellness & Ayurveda', 'count' => '760+', 'slug' => 'wellness', 'icon' => 'beauty-spas.svg', 'desc' => 'Traditional and modern wellness centers, retreats, and Ayurveda.', 'show_in_signup' => true],
    ['id' => 5, 'name' => 'Real Estate', 'count' => '1,120+', 'slug' => 'real-estate', 'icon' => 'home-service.svg', 'desc' => 'Property listings with verified information.', 'show_in_signup' => true],
    ['id' => 6, 'name' => 'Tourism & Travel', 'count' => '1,560+', 'slug' => 'tourism', 'icon' => 'cars.svg', 'desc' => 'Tour operators, guides, and authentic travel experiences.', 'show_in_signup' => true],
    ['id' => 7, 'name' => 'Healthcare & Hospitals', 'count' => '1,480+', 'slug' => 'healthcare', 'icon' => 'hospital.svg', 'desc' => 'Trusted hospitals, clinics, and healthcare providers.', 'show_in_signup' => true],
    ['id' => 8, 'name' => 'Manufacturing & Industrial', 'count' => '920+', 'slug' => 'manufacturing', 'icon' => 'manufacturing.svg', 'desc' => 'Industrial suppliers, manufacturers, and B2B partners.', 'show_in_signup' => false],
    ['id' => 9, 'name' => 'Legal & Consulting Firms', 'count' => '640+', 'slug' => 'legal', 'icon' => 'legal.svg', 'desc' => 'Law firms, consultants, and professional advisors.', 'show_in_signup' => false],
    ['id' => 10, 'name' => 'Home & Garden', 'count' => '1,050+', 'slug' => 'home-garden', 'icon' => 'home-garden.svg', 'desc' => 'Home improvement, interiors, landscaping, and garden services.', 'show_in_signup' => true],
    ['id' => 11, 'name' => 'Auto Services', 'count' => '1,310+', 'slug' => 'auto', 'icon' => 'auto-services.svg', 'desc' => 'Car care, repairs, detailing, and automotive services.', 'show_in_signup' => false],
    ['id' => 12, 'name' => 'Health & Beauty', 'count' => '1,720+', 'slug' => 'beauty', 'icon' => 'health-beauty.svg', 'desc' => 'Salons, spas, and beauty studios offering authentic treatments.', 'show_in_signup' => true],
  ];

  foreach ($defaults as $cat) {
    $file = $dir . '/cat_' . (int)$cat['id'] . '.json';
    if (!file_exists($file)) {
      @file_put_contents($file, json_encode($cat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
  }

  @file_put_contents($seedMarker, date('c'));
}

function pov_get_all_categories(): array {
  pov_seed_categories_if_missing();

  $dir = pov_categories_dir();
  $files = glob($dir . '/cat_*.json');
  if (!$files) {
    return [];
  }

  $categories = [];
  foreach ($files as $file) {
    $content = @file_get_contents($file);
    if (!$content) continue;
    $data = json_decode($content, true);
    if (is_array($data) && isset($data['id'])) {
      if (!isset($data['show_in_signup'])) {
        $data['show_in_signup'] = true;
      }
      $categories[] = $data;
    }
  }

  usort($categories, function($a, $b) {
    return ((int)($a['id'] ?? 0)) <=> ((int)($b['id'] ?? 0));
  });

  return $categories;
}

function pov_get_signup_categories(): array {
  $apiCats = function_exists('node_api_get') ? node_api_get('categories') : null;
  if (!empty($apiCats) && is_array($apiCats)) {
    return array_values(array_filter($apiCats, function($c) {
      return !empty($c['show_in_signup']);
    }));
  }

  $all = pov_get_all_categories();
  return array_values(array_filter($all, function($c) {
    return !empty($c['show_in_signup']);
  }));
}

function pov_category_toggle_signup(int $id): array {
  pov_seed_categories_if_missing();

  $dir = pov_categories_dir();
  $file = $dir . '/cat_' . $id . '.json';
  
  $data = null;
  if (file_exists($file)) {
    $data = json_decode(@file_get_contents($file) ?: '', true);
  }

  // Also call Node.js API to toggle in MySQL
  $apiRes = null;
  if (function_exists('node_api_post')) {
    try {
      $apiRes = node_api_post("categories/{$id}/toggle-signup", []);
    } catch (\Throwable $e) {}
  }

  $newState = null;
  if (!empty($apiRes['success']) && isset($apiRes['show_in_signup'])) {
    $newState = (bool)$apiRes['show_in_signup'];
  }

  if (is_array($data)) {
    if ($newState === null) {
      $current = !empty($data['show_in_signup']);
      $newState = !$current;
    }
    $data['show_in_signup'] = $newState;
    @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $name = $data['name'] ?? "Category #{$id}";
  } else {
    $name = $apiRes['message'] ?? "Category #{$id}";
    if ($newState === null) {
      $newState = true;
    }
  }

  $msg = $newState 
    ? "Category will now show in Sign Up form!" 
    : "Category is now hidden from Sign Up form.";

  return [
    'success' => true,
    'id' => $id,
    'show_in_signup' => $newState,
    'message' => $apiRes['message'] ?? $msg,
  ];
}

function pov_category_delete(int $id): array {
  pov_seed_categories_if_missing();

  $dir = pov_categories_dir();
  $file = $dir . '/cat_' . $id . '.json';
  $deleted = false;
  if (file_exists($file)) {
    $deleted = @unlink($file);
  } else {
    $deleted = true;
  }

  try { @node_api_delete('categories/' . $id); } catch (\Throwable $e) {}

  if ($deleted) {
    return ['success' => true, 'message' => "Category #{$id} was successfully deleted."];
  }
  return ['success' => false, 'message' => "Could not delete category #{$id}."];
}

function pov_category_add(array $data): array {
  pov_seed_categories_if_missing();

  $name = trim((string)($data['name'] ?? ''));
  if ($name === '') return ['success' => false, 'message' => 'Category name is required.'];

  $dir = pov_categories_dir();
  $maxId = 0;
  $files = glob($dir . '/cat_*.json');
  if ($files) {
    foreach ($files as $f) {
      if (preg_match('/cat_(\d+)\.json$/', $f, $m)) {
        $maxId = max($maxId, (int)$m[1]);
      }
    }
  }
  $newId = $maxId + 1;

  $slug = trim((string)($data['slug'] ?? '')) ?: strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
  $showInSignup = isset($data['show_in_signup']) ? (bool)$data['show_in_signup'] : true;

  $record = [
    'id' => $newId,
    'name' => $name,
    'slug' => $slug,
    'icon' => trim((string)($data['icon'] ?? 'fa-solid fa-layer-group')),
    'count' => trim((string)($data['count_text'] ?? '0+ Listings')),
    'desc' => trim((string)($data['description'] ?? '')),
    'show_in_signup' => $showInSignup,
    'is_active' => true,
    'created_at' => date('c'),
  ];

  $file = $dir . "/cat_{$newId}.json";
  $saved = @file_put_contents($file, json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  if ($saved === false) {
    return ['success' => false, 'message' => 'Failed to save category to storage.'];
  }

  try { @node_api_post('categories', $data); } catch (\Throwable $e) {}

  return [
    'success' => true,
    'id' => $newId,
    'category' => $record,
    'message' => "Category '{$name}' created successfully!" . ($showInSignup ? " (Showing in Sign Up)" : " (Hidden from Sign Up)")
  ];
}

function pov_locations_dir(): string {
  $dir = POV_ROOT . '/storage/locations';
  if (!is_dir($dir)) @mkdir($dir, 0755, true);
  return $dir;
}

function pov_get_all_locations(): array {
  $defaults = [
    ['id' => 1, 'name' => 'Delhi NCR', 'count' => '2,450+', 'slug' => 'delhi-ncr'],
    ['id' => 2, 'name' => 'Mumbai', 'count' => '2,180+', 'slug' => 'mumbai'],
    ['id' => 3, 'name' => 'Bangalore', 'count' => '1,890+', 'slug' => 'bangalore'],
    ['id' => 4, 'name' => 'Kolkata', 'count' => '1,340+', 'slug' => 'kolkata'],
    ['id' => 5, 'name' => 'Chennai', 'count' => '1,250+', 'slug' => 'chennai'],
    ['id' => 6, 'name' => 'Jaipur', 'count' => '980+', 'slug' => 'jaipur'],
    ['id' => 7, 'name' => 'Hyderabad', 'count' => '1,120+', 'slug' => 'hyderabad'],
    ['id' => 8, 'name' => 'Pune', 'count' => '1,050+', 'slug' => 'pune'],
    ['id' => 9, 'name' => 'Kochi', 'count' => '760+', 'slug' => 'kochi'],
    ['id' => 10, 'name' => 'Udaipur', 'count' => '540+', 'slug' => 'udaipur'],
    ['id' => 11, 'name' => 'Goa', 'count' => '890+', 'slug' => 'goa'],
    ['id' => 12, 'name' => 'Ahmedabad', 'count' => '720+', 'slug' => 'ahmedabad'],
  ];

  $indexed = [];
  foreach ($defaults as $loc) {
    $indexed[$loc['id']] = $loc;
  }

  $files = glob(pov_locations_dir() . '/*.json');
  if ($files) {
    foreach ($files as $file) {
      $data = json_decode(@file_get_contents($file) ?: '', true);
      if (is_array($data) && isset($data['id'])) {
        $indexed[$data['id']] = $data;
      }
    }
  }

  return array_values($indexed);
}

function pov_location_add(array $data): array {
  $name = trim((string)($data['name'] ?? ''));
  if ($name === '') return ['success' => false, 'message' => 'Location name is required.'];

  $locations = pov_get_all_locations();
  $maxId = 0;
  foreach ($locations as $l) $maxId = max($maxId, (int)($l['id'] ?? 0));
  $newId = $maxId + 1;

  $slug = trim((string)($data['slug'] ?? '')) ?: strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
  $record = [
    'id' => $newId,
    'name' => $name,
    'slug' => $slug,
    'count' => trim((string)($data['count_text'] ?? '0+ Listings')),
  ];

  @file_put_contents(pov_locations_dir() . "/loc_{$newId}.json", json_encode($record, JSON_PRETTY_PRINT));
  try { @node_api_post('locations', $data); } catch (\Throwable $e) {}

  return ['success' => true, 'id' => $newId, 'location' => $record, 'message' => "Location '{$name}' added successfully!"];
}

function pov_blogs_dir(): string {
  $dir = POV_ROOT . '/storage/blogs';
  if (!is_dir($dir)) @mkdir($dir, 0755, true);
  return $dir;
}

function pov_get_all_blogs(): array {
  $defaults = [
    ['slug' => 'unexplored-waterfalls-kerala', 'img' => 'hotel.webp', 'tag' => 'Hidden Gems', 'tag_slug' => 'hidden-gems', 'date' => 'June 15, 2025', 'title' => "Unexplored Waterfalls of Kerala: A Local's Guide", 'excerpt' => 'Discover the lesser-known waterfalls of Kerala that most tourists miss but locals treasure.', 'author' => 'Arjun Menon', 'read' => '8 min read', 'body' => "Kerala's famous cascades draw crowds, but locals know quieter falls tucked behind spice trails."],
    ['slug' => 'engineering-student-bangalore', 'img' => 'cafe.webp', 'tag' => 'Student Diaries', 'tag_slug' => 'student-diaries', 'date' => 'June 10, 2025', 'title' => 'A Day in the Life: Engineering Student in Bangalore', 'excerpt' => "Experience the daily routine, challenges, and joys of being an engineering student in India's tech capital.", 'author' => 'Priya Sharma', 'read' => '6 min read', 'body' => "From 7am metro rides to late-lab debugging sessions, Bangalore student life is a mix of ambition and filter coffee."],
    ['slug' => 'lesser-known-indian-festivals', 'img' => 'service.webp', 'tag' => 'Cultural Insights', 'tag_slug' => 'cultural-insights', 'date' => 'June 5, 2025', 'title' => 'Beyond Diwali: Lesser-Known Indian Festivals', 'excerpt' => "Explore the rich tapestry of regional Indian festivals that showcase the country's diverse cultural heritage.", 'author' => 'Meera Patel', 'read' => '10 min read', 'body' => "India's calendar is crowded with celebrations beyond the national headlines."],
  ];

  $blogs = $defaults;
  $files = glob(pov_blogs_dir() . '/*.json');
  if ($files) {
    foreach ($files as $file) {
      $data = json_decode(@file_get_contents($file) ?: '', true);
      if (is_array($data) && !empty($data['slug'])) {
        $blogs[] = $data;
      }
    }
  }

  return $blogs;
}

function pov_blog_add(array $data): array {
  $title = trim((string)($data['title'] ?? ''));
  if ($title === '') return ['success' => false, 'message' => 'Article title is required.'];

  $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
  $record = [
    'slug' => $slug,
    'title' => $title,
    'tag' => trim((string)($data['tag'] ?? 'Business')),
    'tag_slug' => strtolower(preg_replace('/[^a-z0-9]+/i', '-', (string)($data['tag'] ?? 'business'))),
    'author' => trim((string)($data['author'] ?? 'POV Editorial')),
    'read' => trim((string)($data['read_time'] ?? '5 min read')),
    'excerpt' => trim((string)($data['excerpt'] ?? '')),
    'body' => trim((string)($data['body'] ?? '')),
    'img' => trim((string)($data['image'] ?? 'hotel.webp')),
    'date' => date('F j, Y'),
    'created_at' => date('c'),
  ];

  @file_put_contents(pov_blogs_dir() . "/blog_{$slug}.json", json_encode($record, JSON_PRETTY_PRINT));
  try { @node_api_post('blogs', $data); } catch (\Throwable $e) {}

  return ['success' => true, 'blog' => $record, 'message' => "Blog post '{$title}' published successfully!"];
}



/**
 * =========================================================================
 * PASSWORD RESET — Folder-Based Token Storage (storage/password_resets/)
 * =========================================================================
 */

function pov_reset_tokens_dir(): string {
  $dir = POV_ROOT . '/storage/password_resets';
  if (!is_dir($dir)) @mkdir($dir, 0755, true);
  $ht = $dir . '/.htaccess';
  if (!file_exists($ht)) @file_put_contents($ht, "Require all denied\n");
  return $dir;
}

function pov_password_reset_request(string $email): array {
  $cleanEmail = strtolower(trim($email));
  if (!filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
    return ['success' => false, 'message' => 'Please enter a valid email address.'];
  }
  $user = pov_user_find_by_email($cleanEmail);
  $dir  = pov_reset_tokens_dir();

  // Delete any old tokens for this email
  $existing = glob($dir . '/*.json');
  if ($existing) {
    foreach ($existing as $f) {
      $d = json_decode(@file_get_contents($f) ?: '', true);
      if (is_array($d) && strtolower($d['email'] ?? '') === $cleanEmail) @unlink($f);
    }
  }

  if ($user === null) {
    return ['success' => true, 'found' => false, 'email' => $cleanEmail, 'message' => 'If registered, a reset link will appear.'];
  }

  $token  = bin2hex(random_bytes(32));
  $record = [
    'token'      => $token,
    'email'      => $cleanEmail,
    'name'       => $user['name'] ?? 'User',
    'expires_at' => time() + 3600,
    'created_at' => date('c'),
    'used'       => false,
  ];
  @file_put_contents($dir . '/' . $token . '.json', json_encode($record, JSON_PRETTY_PRINT));

  $scheme   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
  $host     = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000';
  $resetUrl = $scheme . '://' . $host . pov_url('reset-password.php') . '?token=' . $token;

  return [
    'success'   => true,
    'found'     => true,
    'token'     => $token,
    'email'     => $cleanEmail,
    'name'      => $user['name'] ?? 'User',
    'reset_url' => $resetUrl,
    'message'   => 'Reset link generated.',
  ];
}

function pov_password_reset_validate(string $token): array {
  $token = preg_replace('/[^a-f0-9]/', '', strtolower($token));
  if (strlen($token) !== 64) return ['valid' => false, 'message' => 'Invalid reset link.'];
  $file = pov_reset_tokens_dir() . '/' . $token . '.json';
  if (!file_exists($file)) return ['valid' => false, 'message' => 'Reset link not found or already used.'];
  $data = json_decode(@file_get_contents($file) ?: '', true);
  if (!is_array($data)) return ['valid' => false, 'message' => 'Invalid reset data.'];
  if (!empty($data['used'])) return ['valid' => false, 'message' => 'This link has already been used.'];
  if (($data['expires_at'] ?? 0) < time()) {
    @unlink($file);
    return ['valid' => false, 'message' => 'This link has expired. Please request a new one.'];
  }
  return ['valid' => true, 'email' => $data['email'], 'name' => $data['name'] ?? 'User', 'token' => $token];
}

function pov_password_reset_complete(string $token, string $newPassword): array {
  $validate = pov_password_reset_validate($token);
  if (!$validate['valid']) return ['success' => false, 'message' => $validate['message']];
  if (strlen($newPassword) < 6) return ['success' => false, 'message' => 'Password must be at least 6 characters.'];

  $email    = $validate['email'];
  $usersDir = pov_users_dir();
  $userFile = $usersDir . '/' . pov_user_folder_name($email) . '/user.json';

  if (!file_exists($userFile)) {
    $dirs = glob($usersDir . '/*', GLOB_ONLYDIR);
    if ($dirs) foreach ($dirs as $d2) {
      $f2 = $d2 . '/user.json';
      if (!file_exists($f2)) continue;
      $u = json_decode(@file_get_contents($f2) ?: '', true);
      if (is_array($u) && strtolower($u['email'] ?? '') === $email) { $userFile = $f2; break; }
    }
  }

  if (!file_exists($userFile)) return ['success' => false, 'message' => 'User account not found.'];
  $userData = json_decode(@file_get_contents($userFile) ?: '', true);
  if (!is_array($userData)) return ['success' => false, 'message' => 'Failed to read user data.'];

  $userData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
  $userData['updated_at']    = date('c');

  if (@file_put_contents($userFile, json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    return ['success' => false, 'message' => 'Failed to save new password.'];
  }
  @unlink(pov_reset_tokens_dir() . '/' . $token . '.json');
  return ['success' => true, 'message' => 'Password updated successfully! You can now sign in.'];
}
