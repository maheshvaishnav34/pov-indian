<?php
/**
 * api-client.php
 * PHP Bridge Client for connecting to the Node.js + MySQL REST API.
 * Uses native PHP streams (stream_context_create / file_get_contents)
 * with cURL fallback, so it works even if ext-curl is not enabled in php.ini.
 */

if (!defined('NODE_API_URL')) {
    define('NODE_API_URL', 'http://127.0.0.1:5000/api/v1');
}

if (!defined('NODE_API_KEY')) {
    define('NODE_API_KEY', 'pov_internal_secure_token_9918273645');
}

/**
 * Fetch data from Node.js API with fast timeout
 */
function node_api_get(string $endpoint, array $queryParams = []): ?array {
    $url = rtrim(NODE_API_URL, '/') . '/' . ltrim($endpoint, '/');
    if (!empty($queryParams)) {
        $url .= '?' . http_build_query($queryParams);
    }

    $headers = [
        'Accept: application/json',
        'X-Internal-API-Key: ' . NODE_API_KEY,
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 2,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $response = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $json = json_decode($response, true);
            if (!empty($json['success']) && isset($json['data'])) {
                return $json['data'];
            }
        }
        return null;
    }

    // Native PHP Stream Context (works without ext-curl)
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 1.5,
            'ignore_errors' => true,
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false) {
        $json = json_decode($response, true);
        if (!empty($json['success']) && isset($json['data'])) {
            return $json['data'];
        }
    }

    return null;
}

/**
 * Post data to Node.js API (e.g. form submissions)
 */
function node_api_post(string $endpoint, array $postData): array {
    $url = rtrim(NODE_API_URL, '/') . '/' . ltrim($endpoint, '/');
    $payload = json_encode($postData);

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Internal-API-Key: ' . NODE_API_KEY,
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $response = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (($httpCode === 200 || $httpCode === 201) && $response) {
            return json_decode($response, true) ?? ['success' => true];
        }
        return ['success' => false, 'code' => $httpCode];
    }

    // Native PHP Stream Context fallback
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $payload,
            'timeout' => 2.0,
            'ignore_errors' => true,
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false) {
        $json = json_decode($response, true);
        if (!empty($json['success'])) {
            return $json;
        }
    }

    return ['success' => false];
}

/**
 * Delete data via Node.js API
 */
function node_api_delete(string $endpoint): array {
    $url = rtrim(NODE_API_URL, '/') . '/' . ltrim($endpoint, '/');

    $headers = [
        'Accept: application/json',
        'X-Internal-API-Key: ' . NODE_API_KEY,
    ];

    $opts = [
        'http' => [
            'method' => 'DELETE',
            'header' => implode("\r\n", $headers),
            'timeout' => 2.0,
            'ignore_errors' => true,
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false) {
        $json = json_decode($response, true);
        if (!empty($json['success'])) {
            return $json;
        }
    }

    return ['success' => false];
}

/**
 * Patch data via Node.js API
 */
function node_api_patch(string $endpoint, array $patchData): array {
    $url = rtrim(NODE_API_URL, '/') . '/' . ltrim($endpoint, '/');
    $payload = json_encode($patchData);

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Internal-API-Key: ' . NODE_API_KEY,
    ];

    $opts = [
        'http' => [
            'method' => 'PATCH',
            'header' => implode("\r\n", $headers),
            'content' => $payload,
            'timeout' => 2.5,
            'ignore_errors' => true,
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false) {
        $json = json_decode($response, true);
        if (!empty($json['success'])) {
            return $json;
        }
    }

    return ['success' => false];
}

/**
 * PUT data via Node.js API
 */
function node_api_put(string $endpoint, array $putData): array {
    $url = rtrim(NODE_API_URL, '/') . '/' . ltrim($endpoint, '/');
    $payload = json_encode($putData);

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Internal-API-Key: ' . NODE_API_KEY,
    ];

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $response = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (($httpCode === 200 || $httpCode === 201) && $response) {
            return json_decode($response, true) ?? ['success' => true];
        }
        return ['success' => false, 'code' => $httpCode];
    }

    $opts = [
        'http' => [
            'method' => 'PUT',
            'header' => implode("\r\n", $headers),
            'content' => $payload,
            'timeout' => 2.5,
            'ignore_errors' => true,
        ]
    ];
    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false) {
        $json = json_decode($response, true);
        if (!empty($json['success'])) {
            return $json;
        }
    }

    return ['success' => false];
}

// ====================================================================
// Rentals & Stays Specific Node.js API Functions ("Find a place that fits your life")
// ====================================================================

function node_rentals_search(array $filters = []): ?array {
    return node_api_get('rentals', $filters);
}

function node_rentals_get($id, $lat = null, $lng = null): ?array {
    $params = [];
    if ($lat !== null && $lng !== null) {
        $params['lat'] = $lat;
        $params['lng'] = $lng;
    }
    return node_api_get('rentals/' . urlencode($id), $params);
}

function node_rentals_create(array $propData): array {
    return node_api_post('rentals/listings', $propData);
}

function node_rentals_update($id, array $propData): array {
    return node_api_put('rentals/listings/' . urlencode($id), $propData);
}

function node_rentals_delete($id): array {
    return node_api_delete('rentals/listings/' . urlencode($id));
}

function node_rentals_curated(): ?array {
    return node_api_get('rentals/curated');
}

function node_rentals_schedule_visit(array $visitData): array {
    return node_api_post('rentals/visits', $visitData);
}

function node_rentals_post_requirement(array $reqData): array {
    return node_api_post('rentals/requirements', $reqData);
}

function node_rentals_assisted_onboard(array $ownerData): array {
    return node_api_post('rentals/assisted-onboarding', $ownerData);
}



