<?php

/**
 * Load Config
 */
$config = require __DIR__ . '/config.php';

/**
 * Get latest movie from WordPress API
 */
function getLatestMovie($apiUrl)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Telegram Auto Post Bot',
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!is_array($data) || empty($data)) {
        return null;
    }

    return $data[0];
}

/**
 * Read last sent movie ID
 */
function getLastMovieId($storage)
{
    if (!file_exists($storage)) {
        return 0;
    }

    $json = json_decode(file_get_contents($storage), true);

    return $json['last_movie_id'] ?? 0;
}

/**
 * Save last sent movie ID
 */
function saveLastMovieId($storage, $id)
{
    file_put_contents(
        $storage,
        json_encode([
            'last_movie_id' => $id
        ], JSON_PRETTY_PRINT)
    );
}

/**
 * Get Featured Image URL
 */
function getFeaturedImage($mediaId)
{
    global $config;

    $url = "https://filmi.unaux.com/wp-json/wp/v2/media/" . $mediaId;

    $json = @file_get_contents($url);

    if (!$json) {
        return null;
    }

    $media = json_decode($json, true);

    return $media['source_url'] ?? null;
}
