<?php
if (!defined('ALLOWED_ACCESS')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not allowed.');
}

function send_telegram_poster(string $photo_url, string $caption): bool {
    $api_url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendPhoto";
    
    $post_fields = [
        'chat_id'    => TELEGRAM_CHANNEL_ID,
        'photo'      => $photo_url,
        'caption'    => $caption,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        CURLOPT_POST       => true,
        CURLOPT_POSTFIELDS => http_build_query($post_fields)
    ];
    
    $response = make_http_request($api_url, $options);
    
    if (!$response) {
        return false;
    }
    
    $result = json_decode($response, true);
    if (isset($result['ok']) && $result['ok'] === true) {
        return true;
    }
    
    error_log("Telegram API Error: " . ($result['description'] ?? 'Unknown Error'));
    return false;
}
