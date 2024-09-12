<?php
session_start();

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del progetto dalla query string
$progetto_id = $_GET['progetto_id'];

// Funzione per eliminare le informazioni collegate
function elimina_dati_correlati($conn, $table, $progetto_id) {
    $stmt = $conn->prepare("DELETE FROM $table WHERE progetto_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $progetto_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Errore: " . $conn->error;
        exit;
    }
}

// Elimina i dati correlati nelle altre tabelle
elimina_dati_correlati($conn, 'produzione_fiberglass', $progetto_id);
elimina_dati_correlati($conn, 'manutenzione_progetti', $progetto_id);
elimina_dati_correlati($conn, 'sostenibilita_progetti', $progetto_id);

// Elimina il progetto dal database
$stmt = $conn->prepare("DELETE FROM progetti WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $progetto_id);
    $stmt->execute();
    $stmt->close();
} else {
    echo "Errore nell'eliminazione del progetto: " . $conn->error;
    exit;
}

// Reindirizza alla pagina precedente dopo l'eliminazione
header("Location: master_progetti.php?azienda_id=" . $_GET['azienda_id'] . "&linea_prodotto_id=" . $_GET['linea_prodotto_id']);
exit;
?>
