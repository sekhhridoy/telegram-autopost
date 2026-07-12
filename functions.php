<?php

$config = require __DIR__ . '/config.php';

/**
 * HTTP GET Request
 */
function httpGet(string $url): array
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'Filmi Telegram Auto Post'
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($status != 200) {
        throw new Exception("HTTP Error : ".$status);
    }

    return json_decode($response, true);
}

/**
 * Latest Movie
 */
function latestMovie(): ?array
{
    global $config;

    $movies = httpGet($config['api_url']);

    if (empty($movies)) {
        return null;
    }

    return $movies[0];
}

/**
 * Featured Image URL
 */
function featuredImage(array $movie): ?string
{
    if (!isset($movie['_embedded']['wp:featuredmedia'][0]['source_url'])) {
        return null;
    }

    return $movie['_embedded']['wp:featuredmedia'][0]['source_url'];
}

/**
 * Movie Title
 */
function movieTitle(array $movie): string
{
    return html_entity_decode(
        strip_tags($movie['title']['rendered']),
        ENT_QUOTES | ENT_HTML5
    );
}

/**
 * Movie Link
 */
function movieLink(array $movie): string
{
    return $movie['link'];
}

/**
 * Storage Read
 */
function lastMovieId(): int
{
    global $config;

    if (!file_exists($config['storage'])) {
        return 0;
    }

    $json = json_decode(file_get_contents($config['storage']), true);

    return (int)($json['last_movie'] ?? 0);
}

/**
 * Storage Save
 */
function saveMovieId(int $id): void
{
    global $config;

    file_put_contents(
        $config['storage'],
        json_encode([
            'last_movie' => $id
        ], JSON_PRETTY_PRINT)
    );
}
