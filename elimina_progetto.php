<?php
include 'navbar.php';
/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del progetto dalla query string
$progetto_id = $_GET['progetto_id'];

// Recupera l'ID dell'azienda dalla query string
$azienda_id = $_GET['azienda_id'];

// Recupera l'ID della linea di prodotto dell'azienda dalla query string
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Inizio transazione per garantire che tutte le operazioni siano eseguite correttamente
$conn->begin_transaction();

try {
    // Recupera il percorso dell'immagine associata al progetto
    $stmt = $conn->prepare("SELECT immagine FROM progetti WHERE id = ?");
    $stmt->bind_param("i", $progetto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $progetto = $result->fetch_assoc();

    if ($progetto) {
        $immagine = $progetto['immagine'];

        // Elimina l'immagine dal filesystem, se esiste
        if (file_exists($immagine)) {
            unlink($immagine);
        }
    }

    // 1. Elimina le associazioni nella tabella progetti_componenti per questo progetto
    $stmt = $conn->prepare("DELETE FROM progetti_componenti WHERE progetto_id = ?");
    $stmt->bind_param("i", $progetto_id);
    $stmt->execute();

    // 2. Elimina le checklist associate ai componenti del progetto
    // Prima dobbiamo recuperare le checklist associate a questo progetto
    $checklist_stmt = $conn->prepare("SELECT id FROM checklist WHERE progetto_id = ?");
    $checklist_stmt->bind_param("i", $progetto_id);
    $checklist_stmt->execute();
    $checklists = $checklist_stmt->get_result();

    while ($checklist = $checklists->fetch_assoc()) {
        $checklist_id = $checklist['id'];

        // Elimina le risposte associate alle checklist
        $delete_responses_stmt = $conn->prepare("DELETE FROM risposte WHERE checklist_id = ?");
        $delete_responses_stmt->bind_param("i", $checklist_id);
        $delete_responses_stmt->execute();

        // Elimina le domande associate alle checklist
        $delete_questions_stmt = $conn->prepare("DELETE FROM domande WHERE checklist_id = ?");
        $delete_questions_stmt->bind_param("i", $checklist_id);
        $delete_questions_stmt->execute();

        // Elimina la checklist stessa
        $delete_checklist_stmt = $conn->prepare("DELETE FROM checklist WHERE id = ?");
        $delete_checklist_stmt->bind_param("i", $checklist_id);
        $delete_checklist_stmt->execute();
    }

    // 3. Elimina il progetto dalla tabella progetti
    $stmt = $conn->prepare("DELETE FROM progetti WHERE id = ?");
    $stmt->bind_param("i", $progetto_id);
    $stmt->execute();

    // Commit delle operazioni
    $conn->commit();

    // Reindirizza l'utente alla pagina dei progetti
    header("Location: master_progetti.php?azienda_id=$azienda_id&linea_prodotto_id=$linea_prodotto_id");
    exit;
} catch (Exception $e) {
    // In caso di errore, rollback delle operazioni
    $conn->rollback();
    echo "Errore durante l'eliminazione del progetto: " . $e->getMessage();
}
?>
