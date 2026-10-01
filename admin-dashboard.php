<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/form-store.php';
require_once __DIR__ . '/includes/api-client.php';
require_once __DIR__ . '/includes/visit-store.php';

// Check auth - Only Admin can access
if (empty($_SESSION['admin_user'])) {
    header('Location: ' . pov_url('index.php?auth=login'));
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_user'], $_SESSION['admin_token']);
    header('Location: ' . pov_url('index.php'));
    exit;
}

$user = $_SESSION['admin_user'];
$token = $_SESSION['admin_token'] ?? '';
$alert = null;
$activeTab = isset($_GET['tab']) ? trim($_GET['tab']) : 'overview';
$activeSubtab = isset($_GET['subtab']) ? trim($_GET['subtab']) : ($activeTab === 'rentals' ? 'rentals' : 'directory');

if ($activeTab === 'rentals') {
    $activeTab = 'listings';
    $activeSubtab = 'rentals';
}

if (str_starts_with($activeTab, 'edu-')) {
    $sub = substr($activeTab, 4);
    $activeTab = 'education';
    $activeSubtab = ($sub === 'colleges') ? 'institutions' : (($sub === 'courses') ? 'offerings' : $sub);
}

// Handle Data Actions (CRUD)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['admin_action'] ?? '';

    if ($action === 'add_listing') {
        $activeTab = 'listings';
        $activeSubtab = 'directory';
        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;
        $locationText = trim($_POST['location_text'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $rating = !empty($_POST['rating']) ? (float)$_POST['rating'] : 5.0;
        $badge = trim($_POST['badge'] ?? '');
        $image = trim($_POST['image'] ?? 'realestate_03.webp');
        $avatar = trim($_POST['avatar'] ?? 'ryan.webp');
        $desc = trim($_POST['description'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $metaKeywords = trim($_POST['meta_keywords'] ?? '');
        $canonicalUrl = trim($_POST['canonical_url'] ?? '');
        $ogImage = trim($_POST['og_image'] ?? '');
        $schemaType = trim($_POST['schema_type'] ?? 'LocalBusiness');

        if (!empty($title)) {
            $res = pov_listing_add([
                'title' => $title,
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'location_text' => $locationText,
                'phone' => $phone,
                'price' => $price,
                'rating' => $rating,
                'badge' => $badge,
                'image' => $image,
                'avatar' => $avatar,
                'description' => $desc,
                'slug' => $slug,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDesc,
                'meta_keywords' => $metaKeywords,
                'canonical_url' => $canonicalUrl,
                'og_image' => $ogImage,
                'schema_type' => $schemaType
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to create listing: ' . ($res['message'] ?? 'Unknown error')];
            }
        } else {
            $alert = ['type' => 'error', 'msg' => 'Listing title is required.'];
        }

    } elseif ($action === 'edit_listing') {
        $activeTab = 'listings';
        $activeSubtab = 'directory';
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $locationId = !empty($_POST['location_id']) ? (int)$_POST['location_id'] : null;
        $locationText = trim($_POST['location_text'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $rating = !empty($_POST['rating']) ? (float)$_POST['rating'] : 5.0;
        $badge = trim($_POST['badge'] ?? '');
        $image = trim($_POST['image'] ?? 'realestate_03.webp');
        $avatar = trim($_POST['avatar'] ?? 'ryan.webp');
        $desc = trim($_POST['description'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $metaKeywords = trim($_POST['meta_keywords'] ?? '');
        $canonicalUrl = trim($_POST['canonical_url'] ?? '');
        $ogImage = trim($_POST['og_image'] ?? '');
        $schemaType = trim($_POST['schema_type'] ?? 'LocalBusiness');

        if ($id > 0 && !empty($title)) {
            $res = pov_listing_update($id, [
                'title' => $title,
                'category_id' => $categoryId,
                'location_id' => $locationId,
                'location_text' => $locationText,
                'phone' => $phone,
                'price' => $price,
                'rating' => $rating,
                'badge' => $badge,
                'image' => $image,
                'avatar' => $avatar,
                'description' => $desc,
                'slug' => $slug,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDesc,
                'meta_keywords' => $metaKeywords,
                'canonical_url' => $canonicalUrl,
                'og_image' => $ogImage,
                'schema_type' => $schemaType
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to update listing: ' . ($res['message'] ?? 'Error')];
            }
        } else {
            $alert = ['type' => 'error', 'msg' => 'Listing ID and Title are required.'];
        }

    } elseif ($action === 'quick_update_seo') {
        $activeTab = 'listings';
        $activeSubtab = 'directory';
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $res = pov_listing_update($id, [
                'meta_title' => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? ''),
                'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
                'canonical_url' => trim($_POST['canonical_url'] ?? ''),
                'og_image' => trim($_POST['og_image'] ?? ''),
                'schema_type' => trim($_POST['schema_type'] ?? 'LocalBusiness'),
                'slug' => trim($_POST['slug'] ?? '')
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "SEO settings updated successfully for listing #{$id}!"];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to update SEO: ' . ($res['message'] ?? 'Error')];
            }
        }

    } elseif ($action === 'delete_listing') {
        $activeTab = 'listings';
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $res = pov_listing_delete($id);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to delete listing: ' . ($res['message'] ?? 'Could not delete')];
            }
        }

    } elseif ($action === 'add_rental_listing') {
        $activeTab = 'listings';
        $activeSubtab = 'rentals';
        $title = trim($_POST['title'] ?? '');
        $rentalType = trim($_POST['rental_type'] ?? 'long_term');
        $category = trim($_POST['category'] ?? 'Flat / Apartment');
        $city = trim($_POST['city'] ?? 'Pune');
        $state = trim($_POST['state'] ?? 'Maharashtra');
        $locality = trim($_POST['locality'] ?? '');
        $landmark = trim($_POST['landmark'] ?? '');
        $landmarkDistance = trim($_POST['landmark_distance'] ?? '');
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : 0.0;
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : 0.0;
        $price = trim($_POST['price'] ?? '₹12,000');
        $priceUnit = trim($_POST['price_unit'] ?? '/ month');
        $deposit = trim($_POST['deposit'] ?? '₹20,000');
        $bhk = trim($_POST['bhk'] ?? '1 BHK');
        $bathrooms = (int)($_POST['bathrooms'] ?? 1);
        $furnishing = trim($_POST['furnishing'] ?? 'Semi-Furnished');
        $preferredTenant = trim($_POST['preferred_tenant'] ?? 'All Welcome');
        $foodRule = trim($_POST['food_rule'] ?? 'Non-Veg Allowed');
        $curfewRule = trim($_POST['curfew_rule'] ?? 'No Curfew');
        $powerBackup = trim($_POST['power_backup'] ?? 'Inverter Backup');
        $waterSupply = trim($_POST['water_supply'] ?? '24x7 Water Supply');
        $hostName = trim($_POST['host_name'] ?? 'Verified Host');
        $hostPhone = trim($_POST['host_phone'] ?? '+91 98765 43210');
        $image = trim($_POST['image'] ?? 'realestate_01.webp');
        $desc = trim($_POST['description'] ?? '');

        if (!empty($title)) {
            $res = node_rentals_create([
                'title' => $title,
                'rental_type' => $rentalType,
                'category' => $category,
                'city' => $city,
                'state' => $state,
                'locality' => $locality,
                'landmark' => $landmark,
                'landmark_distance' => $landmarkDistance,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'price' => $price,
                'price_unit' => $priceUnit,
                'deposit' => $deposit,
                'bhk' => $bhk,
                'bathrooms' => $bathrooms,
                'furnishing' => $furnishing,
                'preferred_tenant' => $preferredTenant,
                'food_rule' => $foodRule,
                'curfew_rule' => $curfewRule,
                'power_backup' => $powerBackup,
                'water_supply' => $waterSupply,
                'host_name' => $hostName,
                'host_phone' => $hostPhone,
                'image' => $image,
                'description' => $desc
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Rental property '{$title}' added successfully! (Find a place that fits your life)"];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to create rental property: ' . ($res['message'] ?? 'Error')];
            }
        } else {
            $alert = ['type' => 'error', 'msg' => 'Property title is required.'];
        }

    } elseif ($action === 'edit_rental_listing') {
        $activeTab = 'listings';
        $activeSubtab = 'rentals';
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        if ($id > 0 && !empty($title)) {
            $res = node_rentals_update($id, [
                'title' => $title,
                'rental_type' => trim($_POST['rental_type'] ?? 'long_term'),
                'category' => trim($_POST['category'] ?? 'Flat / Apartment'),
                'city' => trim($_POST['city'] ?? 'Pune'),
                'state' => trim($_POST['state'] ?? 'Maharashtra'),
                'locality' => trim($_POST['locality'] ?? ''),
                'landmark' => trim($_POST['landmark'] ?? ''),
                'landmark_distance' => trim($_POST['landmark_distance'] ?? ''),
                'latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : 0.0,
                'longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : 0.0,
                'price' => trim($_POST['price'] ?? '₹12,000'),
                'price_unit' => trim($_POST['price_unit'] ?? '/ month'),
                'deposit' => trim($_POST['deposit'] ?? '₹20,000'),
                'bhk' => trim($_POST['bhk'] ?? '1 BHK'),
                'bathrooms' => (int)($_POST['bathrooms'] ?? 1),
                'furnishing' => trim($_POST['furnishing'] ?? 'Semi-Furnished'),
                'preferred_tenant' => trim($_POST['preferred_tenant'] ?? 'All Welcome'),
                'food_rule' => trim($_POST['food_rule'] ?? 'Non-Veg Allowed'),
                'curfew_rule' => trim($_POST['curfew_rule'] ?? 'No Curfew'),
                'power_backup' => trim($_POST['power_backup'] ?? 'Inverter Backup'),
                'water_supply' => trim($_POST['water_supply'] ?? '24x7 Water Supply'),
                'host_name' => trim($_POST['host_name'] ?? 'Verified Host'),
                'host_phone' => trim($_POST['host_phone'] ?? '+91 98765 43210'),
                'image' => trim($_POST['image'] ?? 'realestate_01.webp'),
                'description' => trim($_POST['description'] ?? '')
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Rental property #{$id} updated successfully!"];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to update rental property: ' . ($res['message'] ?? 'Error')];
            }
        }

    } elseif ($action === 'delete_rental_listing') {
        $activeTab = 'listings';
        $activeSubtab = 'rentals';
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $res = node_rentals_delete($id);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Rental property #{$id} deleted successfully."];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to delete rental property.'];
            }
        }

    } elseif ($action === 'add_category') {
        $activeTab = 'categories';
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fa-solid fa-layer-group');
        $countText = trim($_POST['count_text'] ?? '0+ Listings');
        $desc = trim($_POST['description'] ?? '');
        $showInSignup = !empty($_POST['show_in_signup']);

        if (!empty($name)) {
            $res = pov_category_add([
                'name' => $name,
                'slug' => $slug,
                'icon' => $icon,
                'count_text' => $countText,
                'description' => $desc,
                'show_in_signup' => $showInSignup
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to create category: ' . ($res['message'] ?? 'Error')];
            }
        } else {
            $alert = ['type' => 'error', 'msg' => 'Category name is required.'];
        }

    } elseif ($action === 'toggle_category_signup') {
        $activeTab = 'categories';
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $res = pov_category_toggle_signup($id);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($res);
                exit;
            }
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to toggle category: ' . ($res['message'] ?? 'Error')];
            }
        }

    } elseif ($action === 'delete_category') {
        $activeTab = 'categories';
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $res = pov_category_delete($id);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to delete category: ' . ($res['message'] ?? 'Error')];
            }
        }

    } elseif ($action === 'add_location') {
        $activeTab = 'locations';
        $name = trim($_POST['name'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $countText = trim($_POST['count_text'] ?? '0+ Listings');

        if (!empty($name)) {
            $res = pov_location_add([
                'name' => $name,
                'state' => $state,
                'slug' => $slug,
                'count_text' => $countText
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to add location: ' . ($res['message'] ?? 'Error')];
            }
        } else {
            $alert = ['type' => 'error', 'msg' => 'Location name is required.'];
        }

    } elseif ($action === 'add_blog') {
        $activeTab = 'blogs';
        $title = trim($_POST['title'] ?? '');
        $tag = trim($_POST['tag'] ?? 'Business');
        $author = trim($_POST['author'] ?? 'POV Editorial');
        $readTime = trim($_POST['read_time'] ?? '5 min read');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $image = trim($_POST['image'] ?? 'hotel.webp');

        if (!empty($title)) {
            $res = pov_blog_add([
                'title' => $title,
                'tag' => $tag,
                'author' => $author,
                'read_time' => $readTime,
                'excerpt' => $excerpt,
                'body' => $body,
                'image' => $image
            ]);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => $res['message']];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to publish blog: ' . ($res['message'] ?? 'Error')];
            }
        } else {
            $alert = ['type' => 'error', 'msg' => 'Article title is required.'];
        }

    } elseif ($action === 'confirm_visit') {
        $activeTab = 'visits';
        $visitId = trim($_POST['visit_id'] ?? '');
        $res = pov_update_visit_status($visitId, 'CONFIRMED', [
            'actor' => 'ADMIN',
            'actor_name' => $user['name'] ?? 'SuperAdmin',
            'owner_message' => 'Confirmed by Administrator'
        ]);
        if (!empty($res['success'])) {
            $alert = ['type' => 'success', 'msg' => 'Visit request #' . $visitId . ' confirmed successfully.'];
        } else {
            $alert = ['type' => 'error', 'msg' => $res['error'] ?? 'Could not confirm visit.'];
        }

    } elseif ($action === 'complete_visit') {
        $activeTab = 'visits';
        $visitId = trim($_POST['visit_id'] ?? '');
        $res = pov_update_visit_status($visitId, 'COMPLETED', [
            'actor' => 'ADMIN',
            'actor_name' => $user['name'] ?? 'SuperAdmin'
        ]);
        if (!empty($res['success'])) {
            $alert = ['type' => 'success', 'msg' => 'Visit #' . $visitId . ' marked as completed.'];
        } else {
            $alert = ['type' => 'error', 'msg' => $res['error'] ?? 'Could not mark visit completed.'];
        }

    } elseif ($action === 'cancel_visit') {
        $activeTab = 'visits';
        $visitId = trim($_POST['visit_id'] ?? '');
        $res = pov_update_visit_status($visitId, 'CANCELLED', [
            'actor' => 'ADMIN',
            'actor_name' => $user['name'] ?? 'SuperAdmin',
            'cancellation_reason' => 'Cancelled by Administrator'
        ]);
        if (!empty($res['success'])) {
            $alert = ['type' => 'success', 'msg' => 'Visit #' . $visitId . ' cancelled.'];
        } else {
            $alert = ['type' => 'error', 'msg' => $res['error'] ?? 'Could not cancel visit.'];
        }
    } elseif ($action === 'update_edu_lead_stage') {
        $activeTab = 'education';
        $leadId = (int)($_POST['lead_id'] ?? 0);
        $stage = trim($_POST['lead_stage'] ?? 'New');
        if ($leadId > 0) {
            pov_admin_http_request('PATCH', "http://127.0.0.1:5000/api/v1/edu/admin/leads/{$leadId}/stage", ['lead_stage' => $stage]);
            $alert = ['type' => 'success', 'msg' => "Admission Lead #{$leadId} status updated to '{$stage}'."];
        }
    } elseif ($action === 'add_edu_institution') {
        $activeTab = 'education';
        $payload = [
            'canonical_name' => trim($_POST['canonical_name'] ?? ''),
            'short_code' => trim($_POST['short_code'] ?? ''),
            'group_name' => trim($_POST['group_name'] ?? ''),
            'institution_type' => trim($_POST['institution_type'] ?? 'University'),
            'legal_recognition' => trim($_POST['legal_recognition'] ?? 'State Private University'),
            'ownership' => trim($_POST['ownership'] ?? 'Private Unaided'),
            'city' => trim($_POST['city'] ?? 'Jaipur'),
            'state' => trim($_POST['state'] ?? 'Rajasthan'),
            'locality' => trim($_POST['locality'] ?? ''),
            'campus_acres' => !empty($_POST['campus_acres']) ? (float)$_POST['campus_acres'] : 25.0,
            'official_website' => trim($_POST['official_website'] ?? 'https://institution.edu.in'),
            'admissions_url' => trim($_POST['admissions_url'] ?? ''),
            'primary_email' => trim($_POST['primary_email'] ?? ''),
            'primary_phone' => trim($_POST['primary_phone'] ?? ''),
            'naac_grade' => trim($_POST['naac_grade'] ?? 'A+'),
            'nirf_band' => trim($_POST['nirf_band'] ?? 'Top 100'),
            'logo_text' => trim($_POST['logo_text'] ?? 'COL'),
            'badge_color' => trim($_POST['badge_color'] ?? '#0d2b39'),
            'provenance_source_name' => trim($_POST['provenance_source_name'] ?? 'Official University Gazette / AISHE'),
            'provenance_source_url' => trim($_POST['provenance_source_url'] ?? 'https://ugc.gov.in'),
            'status' => trim($_POST['status'] ?? 'verified')
        ];
        if (!empty($payload['canonical_name'])) {
            $res = pov_admin_http_request('POST', 'http://127.0.0.1:5000/api/v1/edu/institutions', $payload);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "College '{$payload['canonical_name']}' added to Find the Right College platform!"];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to add institution: ' . ($res['message'] ?? 'Error')];
            }
        }
    } elseif ($action === 'edit_edu_institution') {
        $activeTab = 'education';
        $id = (int)($_POST['id'] ?? 0);
        $payload = [
            'canonical_name' => trim($_POST['canonical_name'] ?? ''),
            'short_code' => trim($_POST['short_code'] ?? ''),
            'group_name' => trim($_POST['group_name'] ?? ''),
            'institution_type' => trim($_POST['institution_type'] ?? 'University'),
            'ownership' => trim($_POST['ownership'] ?? 'Private Unaided'),
            'city' => trim($_POST['city'] ?? 'Jaipur'),
            'state' => trim($_POST['state'] ?? 'Rajasthan'),
            'locality' => trim($_POST['locality'] ?? ''),
            'campus_acres' => !empty($_POST['campus_acres']) ? (float)$_POST['campus_acres'] : 25.0,
            'official_website' => trim($_POST['official_website'] ?? ''),
            'admissions_url' => trim($_POST['admissions_url'] ?? ''),
            'primary_email' => trim($_POST['primary_email'] ?? ''),
            'primary_phone' => trim($_POST['primary_phone'] ?? ''),
            'naac_grade' => trim($_POST['naac_grade'] ?? 'A+'),
            'nirf_band' => trim($_POST['nirf_band'] ?? ''),
            'logo_text' => trim($_POST['logo_text'] ?? ''),
            'badge_color' => trim($_POST['badge_color'] ?? '#0d2b39'),
            'status' => trim($_POST['status'] ?? 'verified')
        ];
        if ($id > 0 && !empty($payload['canonical_name'])) {
            $res = pov_admin_http_request('PUT', "http://127.0.0.1:5000/api/v1/edu/institutions/{$id}", $payload);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Institution #{$id} updated successfully!"];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to update institution: ' . ($res['message'] ?? 'Error')];
            }
        }
    } elseif ($action === 'delete_edu_institution') {
        $activeTab = 'education';
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $res = pov_admin_http_request('DELETE', "http://127.0.0.1:5000/api/v1/edu/institutions/{$id}");
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Institution #{$id} removed from database."];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to delete institution.'];
            }
        }
    } elseif ($action === 'add_edu_offering') {
        $activeTab = 'education';
        $instId = (int)($_POST['institution_id'] ?? 0);
        $payload = [
            'institution_id' => $instId,
            'program_name' => trim($_POST['program_name'] ?? ''),
            'degree_type' => trim($_POST['degree_type'] ?? 'B.Tech'),
            'discipline' => trim($_POST['discipline'] ?? 'Engineering'),
            'academic_level' => trim($_POST['academic_level'] ?? 'Undergraduate'),
            'duration_years' => !empty($_POST['duration_years']) ? (float)$_POST['duration_years'] : 4.0,
            'academic_year' => trim($_POST['academic_year'] ?? '2026-27'),
            'intake_seats' => (int)($_POST['intake_seats'] ?? 60),
            'annual_tuition_fee' => (int)($_POST['annual_tuition_fee'] ?? 150000),
            'eligibility_criteria' => trim($_POST['eligibility_criteria'] ?? '10+2 with PCM (min 50%)'),
            'entrance_exams' => trim($_POST['entrance_exams'] ?? 'JEE Main / Direct')
        ];
        if ($instId > 0 && !empty($payload['program_name'])) {
            $res = pov_admin_http_request('POST', "http://127.0.0.1:5000/api/v1/edu/institutions/{$instId}/offerings", $payload);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Course '{$payload['program_name']}' added to institution!"];
            } else {
                $alert = ['type' => 'error', 'msg' => 'Failed to add course offering.'];
            }
        }
    } elseif ($action === 'delete_edu_offering') {
        $activeTab = 'education';
        $id = (int)($_POST['offering_id'] ?? 0);
        if ($id > 0) {
            $res = pov_admin_http_request('DELETE', "http://127.0.0.1:5000/api/v1/edu/offerings/{$id}");
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Course offering #{$id} deleted."];
            }
        }
    } elseif ($action === 'add_edu_exam') {
        $activeTab = 'education';
        $payload = [
            'exam_name' => trim($_POST['exam_name'] ?? ''),
            'short_code' => trim($_POST['short_code'] ?? ''),
            'category' => trim($_POST['category'] ?? 'Engineering'),
            'conducting_body' => trim($_POST['conducting_body'] ?? 'NTA'),
            'exam_date' => trim($_POST['exam_date'] ?? '2026-05-15'),
            'application_end' => trim($_POST['application_end'] ?? '2026-04-15'),
            'application_fee' => trim($_POST['application_fee'] ?? '₹1,000'),
            'official_url' => trim($_POST['official_url'] ?? 'https://nta.ac.in')
        ];
        if (!empty($payload['exam_name'])) {
            $res = pov_admin_http_request('POST', 'http://127.0.0.1:5000/api/v1/edu/exams', $payload);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Entrance Exam '{$payload['exam_name']}' added to registry!"];
            }
        }
    } elseif ($action === 'delete_edu_exam') {
        $activeTab = 'education';
        $id = (int)($_POST['exam_id'] ?? 0);
        if ($id > 0) {
            $res = pov_admin_http_request('DELETE', "http://127.0.0.1:5000/api/v1/edu/exams/{$id}");
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Exam #{$id} deleted."];
            }
        }
    } elseif ($action === 'add_edu_scholarship') {
        $activeTab = 'education';
        $payload = [
            'title' => trim($_POST['title'] ?? ''),
            'provider' => trim($_POST['provider'] ?? 'Government of Rajasthan'),
            'category' => trim($_POST['category'] ?? 'Merit-Based'),
            'amount_display' => trim($_POST['amount_display'] ?? 'Up to ₹1,00,000 / year'),
            'applicable_course' => trim($_POST['applicable_course'] ?? 'B.Tech / Degree'),
            'eligibility' => trim($_POST['eligibility'] ?? '12th percentage > 75%'),
            'deadline' => trim($_POST['deadline'] ?? '2026-08-31'),
            'official_link' => trim($_POST['official_link'] ?? 'https://scholarships.gov.in')
        ];
        if (!empty($payload['title'])) {
            $res = pov_admin_http_request('POST', 'http://127.0.0.1:5000/api/v1/edu/scholarships', $payload);
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Scholarship '{$payload['title']}' added to registry!"];
            }
        }
    } elseif ($action === 'delete_edu_scholarship') {
        $activeTab = 'education';
        $id = (int)($_POST['scholarship_id'] ?? 0);
        if ($id > 0) {
            $res = pov_admin_http_request('DELETE', "http://127.0.0.1:5000/api/v1/edu/scholarships/{$id}");
            if (!empty($res['success'])) {
                $alert = ['type' => 'success', 'msg' => "Scholarship #{$id} deleted."];
            }
        }
    }
}

// Safe HTTP Request Helper (works without ext-curl)
if (!function_exists('pov_admin_http_request')) {
    function pov_admin_http_request(string $method, string $url, ?array $payload = null): ?array {
    $method = strtoupper($method);
    $headers = ['Accept: application/json'];
    $content = null;

    if ($payload !== null) {
        $headers[] = 'Content-Type: application/json';
        $content = json_encode($payload);
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers
        ]);
        if ($content !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }
        $raw = @curl_exec($ch);
        curl_close($ch);
        return $raw ? json_decode($raw, true) : null;
    }

    $httpOpts = [
        'method' => $method,
        'header' => implode("\r\n", $headers),
        'timeout' => 2,
        'ignore_errors' => true
    ];
    if ($content !== null) {
        $httpOpts['content'] = $content;
    }

    $ctx = stream_context_create(['http' => $httpOpts]);
    $raw = @file_get_contents($url, false, $ctx);
    return ($raw !== false) ? json_decode($raw, true) : null;
}
}

// Fetch live dataset (Node.js MySQL or Folder-based storage fallback)
$categories = node_api_get('categories') ?: pov_get_all_categories();
$locations = node_api_get('locations') ?: pov_get_all_locations();
$listings = node_api_get('listings') ?: pov_get_all_listings('desc');
$rentalListings = node_rentals_search() ?: ($POV_RENTAL_LISTINGS ?? []);
$blogs = node_api_get('blogs') ?: pov_get_all_blogs();
$submissions = node_api_get('forms/all') ?: pov_get_all_submissions();
$users = node_api_get('auth/users') ?: pov_get_all_users();
$visitsList = pov_load_all_visits();
$visitMetrics = pov_get_visit_metrics();

// Fetch Higher Education Data (POVIndian Education Graph)
$eduInstitutions = [];
$eduLeads = [];
$eduStats = [
    'verified_institutions' => 8,
    'offerings_2026_27' => 17,
    'total_leads' => 3,
    'new_leads' => 1,
    'in_counselling' => 1,
    'verified_approvals' => 12
];

$eduInstRes = pov_admin_http_request('GET', 'http://127.0.0.1:5000/api/v1/edu/institutions?state=all');
if (!empty($eduInstRes['data'])) {
    $eduInstitutions = $eduInstRes['data'];
}

$eduLeadsRes = pov_admin_http_request('GET', 'http://127.0.0.1:5000/api/v1/edu/admin/leads?limit=100');
if (!empty($eduLeadsRes['data'])) {
    $eduLeads = $eduLeadsRes['data'];
}

$eduStatsRes = pov_admin_http_request('GET', 'http://127.0.0.1:5000/api/v1/edu/stats');
if (!empty($eduStatsRes['stats'])) {
    $eduStats = array_merge($eduStats, $eduStatsRes['stats']);
}

$eduExamsRes = pov_admin_http_request('GET', 'http://127.0.0.1:5000/api/v1/edu/exams');
$eduExams = !empty($eduExamsRes['data']) ? $eduExamsRes['data'] : [];

$eduScholarRes = pov_admin_http_request('GET', 'http://127.0.0.1:5000/api/v1/edu/scholarships');
$eduScholarships = !empty($eduScholarRes['data']) ? $eduScholarRes['data'] : [];

$totalEduOfferingsCount = 0;
if (!empty($eduInstitutions)) {
    foreach ($eduInstitutions as $inst) {
        if (!empty($inst['offerings'])) {
            $totalEduOfferingsCount += count($inst['offerings']);
        }
    }
}

// Image resolution helpers
if (!function_exists('pov_dash_listing_img')) {
    function pov_dash_listing_img(?string $img): string {
        return pov_img_url($img, 'assets/img/listings/realestate_01.webp');
    }
}

if (!function_exists('pov_dash_listing_avatar')) {
    function pov_dash_listing_avatar(?string $avatar): string {
        return pov_avatar_url($avatar, 'assets/img/avatars/ryan.webp');
    }
}

if (!function_exists('pov_dash_blog_img')) {
    function pov_dash_blog_img(?string $img): string {
        $img = trim((string)$img);
        if (empty($img)) {
            return pov_url('assets/img/blog/hotel.webp');
        }
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return $img;
        }
        if (str_starts_with($img, 'assets/')) {
            return pov_url($img);
        }
        return pov_url('assets/img/blog/' . ltrim($img, '/'));
    }
}

if (!function_exists('pov_dash_cat_icon')) {
    function pov_dash_cat_icon(?string $icon): string {
        $icon = trim((string)$icon);
        if (empty($icon)) {
            return '<i class="fa-solid fa-layer-group"></i>';
        }
        if (preg_match('/\.(svg|png|webp|jpg|jpeg)$/i', $icon)) {
            $src = str_starts_with($icon, 'assets/') ? pov_url($icon) : pov_url('assets/img/cats/' . ltrim($icon, '/'));
            return '<img src="' . htmlspecialchars($src) . '" alt="icon" style="width:22px; height:22px; object-fit:contain;" />';
        }
        return '<i class="' . htmlspecialchars($icon) . '"></i>';
    }
}

// Category lookup map
$categoryMap = [];
foreach ($categories as $c) {
    if (isset($c['id'])) {
        $categoryMap[$c['id']] = $c['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Portal | POV Indian Management Suite</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Hurricane&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
  <link rel="icon" href="<?= htmlspecialchars(pov_url('assets/img/ui/pov-logo-dark.svg')) ?>" />
  <link rel="stylesheet" href="<?= htmlspecialchars(pov_url('css/admin-dashboard.css')) ?>?v=<?= time() ?>" />
</head>
<body>

  <!-- ================= 1. SIDEBAR ================= -->
  <?php include __DIR__ . '/includes/admin/sidebar.php'; ?>

  <!-- ================= 2. MAIN CONTAINER ================= -->
  <main class="admin-main">
    
    <!-- Topbar -->
    <?php include __DIR__ . '/includes/admin/topbar.php'; ?>

    <!-- Content Body -->
    <div class="admin-content">

      <!-- Alert Notification -->
      <?php if (!empty($alert)): ?>
        <div class="alert-banner <?= htmlspecialchars($alert['type']) ?>" id="alertBanner">
          <div style="display:flex; align-items:center; gap:8px;">
            <i class="fa-solid <?= $alert['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($alert['msg']) ?>
          </div>
          <button type="button" class="alert-close" onclick="document.getElementById('alertBanner').remove();">&times;</button>
        </div>
      <?php endif; ?>

      <!-- Modular Tab Sections -->
      <?php include __DIR__ . '/includes/admin/tabs/tab-overview.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-listings.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-categories.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-locations.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-blogs.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-inquiries.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-education.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-visits.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-users.php'; ?>
      <?php include __DIR__ . '/includes/admin/tabs/tab-system.php'; ?>

    </div>
  </main>

  <!-- ================= 3. MODALS ================= -->
  <?php include __DIR__ . '/includes/admin/modals.php'; ?>

  <!-- ================= 4. JAVASCRIPT CONTROLLERS ================= -->
  <script src="<?= htmlspecialchars(pov_url('js/admin-dashboard.js')) ?>?v=<?= time() ?>"></script>

</body>
</html>
