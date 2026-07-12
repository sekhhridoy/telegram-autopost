<?php
// Security check to prevent direct access
if (!defined('ALLOWED_ACCESS')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not allowed.');
}

// Telegram Configuration
define('TELEGRAM_BOT_TOKEN', '8518044652:AAFn9FRIgOoHs_WPwVCZwmD7mhitfrZdV5I'); // Replace via Env or direct input
define('TELEGRAM_CHANNEL_ID', '-1002677113544');

// WordPress Configuration
define('WP_API_URL', 'https://filmi.unaux.com/wp-json/wp/v2/movies?per_page=1&_embed');

// System Paths
define('STORAGE_FILE', __DIR__ . '/storage.json');

// Telegram Caption Template
define('CAPTION_TEMPLATE', "❇️📽️ {title}\n" .
                           "❇️☢️--Visit Website👇\n" .
                           "https://filmi.unaux.com\n" .
                           "❇️🧲--DOWNLOAD LINK : 👇👇\n" .
                           "⚡ {link}\n\n" .
                           "❇️📢 সহজে ডাউনলোড করতে লিংক কপি করে যে কোন ব্রাউজারে পেস্ট করে ঢুকবেন। আমাদের সাইট সবার সাথে শেয়ার করবেন।");
