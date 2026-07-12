<?php
if (!defined('ALLOWED_ACCESS')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not allowed.');
}

function make_http_request(string $url, array $options = []): string|bool {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    // একদম রিয়েল ক্রোম ব্রাউজারের মতো ফুল হেডার সেট করা হলো যেন ফ্রি হোস্টিং ব্লক না করে
    $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if (!empty($options)) {
        curl_setopt_array($ch, $options);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch) || $httpCode >= 400) {
        error_log("HTTP Request Error. Code: $httpCode");
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    return $response;
}

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

function get_movie_poster(array $movie): string {
    if (!empty($movie['_embedded']['wp:featuredmedia'][0]['source_url'])) {
        return $movie['_embedded']['wp:featuredmedia'][0]['source_url'];
    }
    return 'https://via.placeholder.com/800x1200.png?text=No+Poster+Available';
}

function read_storage(): array {
    if (!file_exists(STORAGE_FILE)) {
        return ['last_movie' => 0];
    }
    $data = file_get_contents(STORAGE_FILE);
    $decoded = json_decode($data, true);
    return is_array($decoded) ? $decoded : ['last_movie' => 0];
}

function save_storage(int $id): bool {
    $data = json_encode(['last_movie' => $id], JSON_PRETTY_PRINT);
    return (bool) file_put_contents(STORAGE_FILE, $data, LOCK_EX);
}
