
<?php
require_once 'config.php';

// Funzione per pulire l'output prima di mostrarlo
function cleanOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Funzione per validare input
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Funzione per generare token CSRF
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Funzione per verificare token CSRF
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    return true;
}

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Abilita il reporting degli errori
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // Imposta il charset a UTF-8
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }
    
    // Verifica se la connessione SSL è disponibile
    if (!$conn->ssl_set(NULL, NULL, NULL, NULL, NULL)) {
        error_log("SSL connection not available");
    }
    
    // Imposta il timezone corretto per il database
    $conn->query("SET time_zone = '+01:00'");
    
} catch (Exception $e) {
    // Log dell'errore in modo sicuro
    error_log("Database connection error: " . $e->getMessage());
    
    // Mostra un messaggio generico all'utente
    die("Si è verificato un errore di connessione al database. Riprova più tardi.");
}

// Inizia la sessione in modo sicuro se non è già stata avviata
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => 1,
        'cookie_secure' => 1,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => 1,
        'gc_maxlifetime' => 3600,
    ]);
}

// Rigenera l'ID sessione periodicamente per prevenire il session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
?>