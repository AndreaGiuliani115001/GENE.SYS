<?php
include 'navbar.php';
include 'connection.php';

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master' || !is_null($_SESSION['azienda_id'])) {
    header("Location: login.php");
    exit;
}

// Verifica se l'azienda_id è presente nella richiesta
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['azienda_id'])) {
    $azienda_id = $_GET['azienda_id'];

    // Prepara la query per eliminare l'azienda
    $stmt = $conn->prepare("DELETE FROM aziende WHERE id = ?");
    $stmt->bind_param("i", $azienda_id);

    if ($stmt->execute()) {
        // Se l'eliminazione ha successo, reindirizza alla dashboard con un messaggio di conferma
        $_SESSION['success'] = "Azienda eliminata con successo.";
        header("Location: master_dashboard.php");
        exit;
    } else {
        // Se c'è un errore, mostra un messaggio di errore
        echo "<div class='alert alert-danger'>Errore durante l'eliminazione dell'azienda.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID azienda non valido.</div>";
}
?>
