<?php
// Avvia la sessione
session_start();

// Distrugge tutte le variabili di sessione
$_SESSION = array();

// Se desideri distruggere anche il cookie di sessione (opzionale)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugge la sessione
session_destroy();

// Reindirizza l'utente alla pagina principale (index.php)
header("Location: index.php");
exit;
