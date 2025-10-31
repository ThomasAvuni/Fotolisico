<?php
// Configurazione del database
define('DB_HOST', 'localhost');
define('DB_USER', 'giuliano');
define('DB_PASS', 'prepuzio');
define('DB_NAME', 'dbBarche');
define('DB_PORT', 3306);

// Configurazione generale del sito
define('SITE_URL', 'https://tuodominio.it'); // Cambia con il tuo dominio
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 10485760); // 10MB in bytes

// Chiavi di sicurezza
define('SECURE_KEY', hash('sha256', 'cambia_questa_chiave_con_una_random')); // Cambia questa chiave!

// Configurazione sessione
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600); // 1 ora
ini_set('session.use_strict_mode', 1);

// Impostazioni PHP
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Headers di sicurezza
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net; img-src 'self' data: blob:; font-src 'self'; connect-src 'self';");
?>