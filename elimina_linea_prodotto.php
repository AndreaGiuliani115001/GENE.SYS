<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è un admin globale o admin aziendale
if ($_SESSION['ruolo'] != 'master' || (!is_null($_SESSION['azienda_id']) && $_GET['azienda_id'] != $_SESSION['azienda_id'])) {
    header("Location: login.php");
    exit;
}

// Verifica che l'ID della linea di prodotto sia stato passato correttamente
if (isset($_GET['linea_prodotto_id']) && isset($_GET['azienda_id'])) {
    $linea_prodotto_id = $_GET['linea_prodotto_id'];
    $azienda_id = $_GET['azienda_id'];

    // Prepara la query per eliminare la linea di prodotto
    $stmt = $conn->prepare("DELETE FROM linee_prodotti WHERE id = ? AND azienda_id = ?");
    $stmt->bind_param("ii", $linea_prodotto_id, $azienda_id);

    if ($stmt->execute()) {
        // Se l'eliminazione ha successo, reindirizza alla pagina delle linee di prodotto
        $_SESSION['success'] = "Linea di prodotto eliminata con successo.";
        header("Location: master_linee_prodotti.php?azienda_id=$azienda_id");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Errore durante l'eliminazione della linea di prodotto.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>ID della linea di prodotto non valido.</div>";
}
?>

