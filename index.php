<?php
// Set execution environment constraints
ini_set('display_errors', 0);
error_log("Movie Auto-Poster Execution Started: " . date('Y-m-d H:i:s'));

define('ALLOWED_ACCESS', true);

// Load Project Files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/telegram.php';

// Check if Config is default
if (TELEGRAM_BOT_TOKEN === 'YOUR_BOT_TOKEN_HERE') {
    die("Error: Please set your actual Telegram Bot Token in config.php\n");
}

// 1. Fetch Latest Movie from WordPress
$movie = fetch_latest_movie();
if (!$movie) {
    die("Failed to fetch data from WordPress API.\n");
}

$current_movie_id = (int) ($movie['id'] ?? 0);
if ($current_movie_id === 0) {
    die("Invalid movie layout data received.\n");
}

// 2. Read Local Storage State
$storage = read_storage();
$last_posted_id = (int) $storage['last_movie'];

// 3. Check for Duplication
if ($current_movie_id <= $last_posted_id) {
    echo "No new movie found. Last posted ID: {$last_posted_id}. Current ID: {$current_movie_id}.\n";
    exit;
}

// 4. Prepare Post Data
$movie_title = htmlspecialchars($movie['title']['rendered'] ?? 'New Movie', ENT_QUOTES, 'UTF-8');
$movie_link  = esc_url($movie['link'] ?? '');
$poster_url  = get_movie_poster($movie);

// Clean URL Helper Function (Simple implementation inline for security)
function esc_url($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

// Build Caption from Template
$caption = str_replace(
    ['{title}', '{link}'],
    [$movie_title, $movie_link],
    CAPTION_TEMPLATE
);

// 5. Dispatch to Telegram Channel
$send_status = send_telegram_poster($poster_url, $caption);

if ($send_status) {
    // 6. Update Storage on success to prevent duplicates
    save_storage($current_movie_id);
    echo "Successfully posted new movie ID: {$current_movie_id} to Telegram.\n";
} else {
    echo "Failed to send post to Telegram. Check PHP error logs.\n";
}
