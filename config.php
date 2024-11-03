<?php
// Function to load environment variables from a .env file
function loadEnv($file) {
    if (file_exists($file)) {
        $lines = file($file);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            putenv("$name=$value");
        }
    }
}

// Load environment variables
loadEnv(__DIR__ . '/.env');

// reCAPTCHA configuration
define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY'));

// You can also load database configuration if needed
// define('DB_HOST', getenv('DB_HOST'));
// define('DB_USERNAME', getenv('DB_USERNAME'));
// define('DB_PASSWORD', getenv('DB_PASSWORD'));
// define('DB_NAME', getenv('DB_NAME'));
?>