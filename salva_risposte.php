<?php
session_start();
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera i dati inviati dal form
$progetto_id = $_POST['progetto_id'];
$componente_id = $_POST['componente_id'];
$checklist_componente_progetto_id = $_POST['checklist_componente_progetto_id'];
$azienda_id = $_POST['azienda_id'];
$linea_prodotto_id = $_POST['linea_prodotto_id'];
$risposte = $_POST['risposte'];

// Directory per il caricamento dei file multimediali
$upload_dir = 'uploads/';

// Controllo della cartella uploads
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Ciclo attraverso le risposte
foreach ($risposte as $domanda_id => $risposta) {
    // Recupera il tipo di contenuto della domanda
    $stmt = $conn->prepare("SELECT tipo_risposta FROM domande WHERE id = ?");
    $stmt->bind_param("i", $domanda_id);
    $stmt->execute();
    $tipo_risposta = $stmt->get_result()->fetch_assoc()['tipo_risposta'];

    $valore_testo = null;
    $valore_data = null;
    $valore_media_url = null;

    // Gestione del salvataggio in base al tipo di risposta
    if ($tipo_risposta == 'testo') {
        $valore_testo = $risposta;
    } elseif ($tipo_risposta == 'data') {
        $valore_data = $risposta;
    } elseif (in_array($tipo_risposta, ['immagine', 'video', 'file'])) {
        // Gestione dei file caricati
        if (isset($_FILES['risposte']['tmp_name'][$domanda_id]) && $_FILES['risposte']['error'][$domanda_id] == UPLOAD_ERR_OK) {
            $file_name = uniqid() . '_' . basename($_FILES['risposte']['name'][$domanda_id]);
            $upload_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['risposte']['tmp_name'][$domanda_id], $upload_file)) {
                $valore_media_url = $upload_file;
            }
        }
    }

    // Verifica se esiste già una risposta per questa domanda e checklist_componente_progetto_id
    $stmt = $conn->prepare("
    SELECT r.id FROM risposte r
    JOIN risposta_domanda_checklist_componente_progetto rdc 
    ON r.id = rdc.risposta_id
    WHERE rdc.domanda_checklist_componente_progetto_id = ?");
    $stmt->bind_param("i", $checklist_componente_progetto_id);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows > 0) {
        // Se esiste già una risposta, aggiornala
        $risposta_row = $result->fetch_assoc();
        $risposta_id = $risposta_row['id'];

        $update_stmt = $conn->prepare("
            UPDATE risposte 
            SET valore_testo = ?, valore_data = ?, valore_media_url = ? 
            WHERE id = ?");
        $update_stmt->bind_param("sssi", $valore_testo, $valore_data, $valore_media_url, $risposta_id);
        $update_stmt->execute();
    } else {
        // Se non esiste una risposta, inseriscine una nuova
        $insert_stmt = $conn->prepare("
            INSERT INTO risposte (valore_testo, valore_data, valore_media_url, tipo_contenuto) 
            VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $valore_testo, $valore_data, $valore_media_url, $tipo_risposta);
        $insert_stmt->execute();
        $risposta_id = $conn->insert_id;

        // Recupera l'ID della relazione domanda_checklist_componente_progetto
        $select_rel_stmt = $conn->prepare("
    SELECT id 
    FROM domanda_checklist_componente_progetto 
    WHERE domanda_id = ? AND checklist_componente_progetto_id = ?");
        $select_rel_stmt->bind_param("ii", $domanda_id, $checklist_componente_progetto_id);
        $select_rel_stmt->execute();
        $rel_result = $select_rel_stmt->get_result();

// Controlla se la relazione esiste
        if ($rel_result->num_rows > 0) {
            $rel_row = $rel_result->fetch_assoc();
            $domanda_checklist_componente_progetto_id = $rel_row['id']; // Ottieni l'ID corretto

            // Ora puoi inserire la risposta collegata all'ID corretto
            $insert_rel_stmt = $conn->prepare("
        INSERT INTO risposta_domanda_checklist_componente_progetto (risposta_id, domanda_checklist_componente_progetto_id) 
        VALUES (?, ?)");
            $insert_rel_stmt->bind_param("ii", $risposta_id, $domanda_checklist_componente_progetto_id);
            $insert_rel_stmt->execute();
        } else {
            echo "Errore: Relazione domanda-checklist-componente non trovata.";
            exit;
        }

    }
}

// Reindirizza alla pagina della checklist
header("Location: checklist.php?componente_id=" . $componente_id . "&progetto_id=" . $progetto_id . "&azienda_id=" . $azienda_id . "&linea_prodotto_id=" . $linea_prodotto_id);
exit;
?>
