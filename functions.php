<?php
if (!defined('ALLOWED_ACCESS')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not allowed.');
}

// 1. Updated HTTP Request Function (With User-Agent for Free Hosting)
function make_http_request(string $url, array $options = []): string|bool {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    // User-Agent added to bypass anti-bot protection of free hosting
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    
    if (!empty($options)) {
        curl_setopt_array($ch, $options);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch) || $httpCode >= 400) {
        error_log("HTTP Request Error to URL: $url. Code: $httpCode. Error: " . curl_error($ch));
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    return $response;
}

// 2. Fetch the latest movie from WP REST API
function fetch_latest_movie(): array|bool {
    $json_data = make_http_request(WP_API_URL);
    if (!$json_data) {
        return false;
    }
    
    $movies = json_decode($json_data, true);
    if (empty($movies) || !is_array($movies)) {
        return false;
    }
    
    return $movies[0]; 
}

// 3. Extract featured image from Embedded WP API response
function get_movie_poster(array $movie): string {
    if (!empty($movie['_embedded']['wp:featuredmedia'][0]['source_url'])) {
        return $movie['_embedded']['wp:featuredmedia'][0]['source_url'];
    }
    return 'https://via.placeholder.com/800x1200.png?text=No+Poster+Available';
}

// 4. Read Storage JSON File safely
function read_storage(): array {
    if (!file_exists(STORAGE_FILE)) {
        return ['last_movie' => 0];
    }
    
    $data = file_get_contents(STORAGE_FILE);
    $decoded = json_decode($data, true);
    
    return is_array($decoded) ? $decoded : ['last_movie' => 0];
}

// 5. Save Movie ID to Storage JSON File
function save_storage(int $id): bool {
    $data = json_encode(['last_movie' => $id], JSON_PRETTY_PRINT);
    return (bool) file_put_contents(STORAGE_FILE, $data, LOCK_EX);
}
