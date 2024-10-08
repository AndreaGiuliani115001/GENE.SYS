<?php
session_start();
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}



// Recupera le risposte dal form
$checklist_id = $_POST['checklist_id'];
$risposte = $_POST['risposte'];
$componente_id = $_POST['componente_id'] ?? null;
$attivita_id = $_POST['attivita_id'] ?? null;
$progetto_id = $_POST['progetto_id'];
$azienda_id = $_POST['azienda_id'];
$linea_prodotto_id = $_POST['linea_prodotto_id'];
$tipo = $_POST['tipo'] ?? 'produzione';


// Ciclo attraverso le risposte
foreach ($risposte as $domanda_id => $risposta) {

    // Prepara i valori in base al tipo di contenuto
    $tipo_contenuto_stmt = $conn->prepare("SELECT tipo_contenuto FROM domande WHERE id = ?");
    $tipo_contenuto_stmt->bind_param("i", $domanda_id);
    $tipo_contenuto_stmt->execute();
    $tipo_contenuto_result = $tipo_contenuto_stmt->get_result()->fetch_assoc();
    $tipo_contenuto = $tipo_contenuto_result['tipo_contenuto'];

    $valore_testo = null;
    $valore_data = null;
    $valore_media_url = null;

    if ($tipo_contenuto == 'testo') {
        $valore_testo = $risposta;
    } elseif ($tipo_contenuto == 'data') {
        $valore_data = $risposta;
    } elseif ($tipo_contenuto == 'immagine') {
        // Caricamento dell'immagine
        if (isset($_FILES['risposte']['tmp_name'][$domanda_id]) && $_FILES['risposte']['error'][$domanda_id] == UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $file_name = basename($_FILES['risposte']['name'][$domanda_id]);
            $upload_file = $upload_dir . $file_name;

            // Sposta il file caricato
            if (move_uploaded_file($_FILES['risposte']['tmp_name'][$domanda_id], $upload_file)) {
                $valore_media_url = $upload_file;
            }
        }
    }

    // Verifica se esiste già una risposta per questa domanda
    $stmt = $conn->prepare("SELECT r.id FROM risposte r
                            JOIN domanda_risposte dr ON r.id = dr.risposta_id
                            WHERE dr.domanda_id = ?");
    $stmt->bind_param("i", $domanda_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Se esiste una risposta, aggiornala
        $risposta_row = $result->fetch_assoc();
        $risposta_id = $risposta_row['id'];

        $update_stmt = $conn->prepare("
            UPDATE risposte 
            SET valore_testo = ?, valore_data = ?, valore_media_url = ? 
            WHERE id = ?");
        $update_stmt->bind_param("sssi", $valore_testo, $valore_data, $valore_media_url, $risposta_id);
        $update_stmt->execute();
    } else {
        // Se non esiste una risposta, inserisci una nuova risposta
        $insert_stmt = $conn->prepare("
            INSERT INTO risposte (tipo_contenuto, valore_testo, valore_data, valore_media_url) 
            VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $tipo_contenuto, $valore_testo, $valore_data, $valore_media_url);
        $insert_stmt->execute();
        $risposta_id = $conn->insert_id;

        // Collega la nuova risposta alla domanda nella tabella intermedia
        $insert_rel_stmt = $conn->prepare("
            INSERT INTO domanda_risposte (domanda_id, risposta_id) 
            VALUES (?, ?)");
        $insert_rel_stmt->bind_param("ii", $domanda_id, $risposta_id);
        $insert_rel_stmt->execute();
    }
}

// Dopo il salvataggio, reindirizza alla pagina della checklist
header("Location: checklist.php?componente_id=" . $componente_id . "&attivita_id=" . $attivita_id . "&progetto_id=" . $progetto_id . "&azienda_id=" . $azienda_id . "&linea_prodotto_id=" . $linea_prodotto_id . "&tipo=" . $tipo);
exit;
