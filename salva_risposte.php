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

// Directory per il caricamento delle immagini
$upload_dir = 'uploads/';

// Controllo di debug
echo "Dati ricevuti dal form: <br>";
echo "Checklist ID: " . $checklist_id . "<br>";
echo "Progetto ID: " . $progetto_id . "<br>";
echo "Componente ID: " . $componente_id . "<br>";
echo "Attività ID: " . $attivita_id . "<br>";
echo "Tipo: " . $tipo . "<br>";
echo "Dati delle risposte: <br>";
print_r($risposte);
echo "<br>";

// Contenuto di $_FILES per verificare la presenza delle immagini
echo "Contenuto di \$_FILES: <br>";
echo "<pre>";
print_r($_FILES);
echo "</pre>";

// Controllo della cartella uploads
if (!is_dir($upload_dir)) {
    echo "La cartella 'uploads' non esiste. Creazione in corso...<br>";
    if (mkdir($upload_dir, 0777, true)) {
        echo "Cartella 'uploads' creata con successo.<br>";
    } else {
        echo "Errore nella creazione della cartella 'uploads'.<br>";
        exit;
    }
}

// Ciclo attraverso le risposte di testo e data
foreach ($risposte as $domanda_id => $risposta) {
    // Prepara i valori in base al tipo di contenuto
    $tipo_contenuto_stmt = $conn->prepare("SELECT tipo_contenuto FROM domande WHERE id = ?");
    $tipo_contenuto_stmt->bind_param("i", $domanda_id);
    $tipo_contenuto_stmt->execute();
    $tipo_contenuto_result = $tipo_contenuto_stmt->get_result()->fetch_assoc();
    $tipo_contenuto = $tipo_contenuto_result['tipo_contenuto'];

    echo "Processando domanda ID: " . $domanda_id . " con tipo contenuto: " . $tipo_contenuto . "<br>";

    $valore_testo = null;
    $valore_data = null;
    $valore_media_url = null;

    if ($tipo_contenuto == 'testo') {
        $valore_testo = $risposta;
    } elseif ($tipo_contenuto == 'data') {
        $valore_data = $risposta;
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
        echo "Risposta aggiornata per domanda ID: " . $domanda_id . "<br>";
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
        echo "Nuova risposta inserita per domanda ID: " . $domanda_id . "<br>";
    }
}

// Gestione delle immagini (al di fuori del ciclo precedente)
foreach ($_FILES['risposte']['tmp_name'] as $domanda_id => $tmp_name) {
    if ($_FILES['risposte']['error'][$domanda_id] == UPLOAD_ERR_OK) {
        // Genera un nome univoco per evitare sovrascritture
        $file_name = uniqid() . '_' . basename($_FILES['risposte']['name'][$domanda_id]);
        $upload_file = $upload_dir . $file_name;

        echo "Tentativo di spostare il file caricato in: " . $upload_file . "<br>";

        // Sposta il file caricato nella cartella uploads
        if (move_uploaded_file($tmp_name, $upload_file)) {
            echo "Immagine caricata con successo in: " . $upload_file . "<br>";

            // Verifica se esiste già una risposta per questa domanda
            $stmt = $conn->prepare("SELECT r.id FROM risposte r
                                    JOIN domanda_risposte dr ON r.id = dr.risposta_id
                                    WHERE dr.domanda_id = ?");
            $stmt->bind_param("i", $domanda_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $valore_media_url = $upload_file;

            if ($result->num_rows > 0) {
                // Se esiste una risposta, aggiornala
                $risposta_row = $result->fetch_assoc();
                $risposta_id = $risposta_row['id'];

                $update_stmt = $conn->prepare("
                    UPDATE risposte 
                    SET valore_media_url = ? 
                    WHERE id = ?");
                $update_stmt->bind_param("si", $valore_media_url, $risposta_id);
                $update_stmt->execute();
                echo "Risposta aggiornata con immagine per domanda ID: " . $domanda_id . "<br>";
            } else {
                // Se non esiste una risposta, inserisci una nuova risposta
                $insert_stmt = $conn->prepare("
                    INSERT INTO risposte (tipo_contenuto, valore_media_url) 
                    VALUES ('immagine', ?)");
                $insert_stmt->bind_param("s", $valore_media_url);
                $insert_stmt->execute();
                $risposta_id = $conn->insert_id;

                // Collega la nuova risposta alla domanda nella tabella intermedia
                $insert_rel_stmt = $conn->prepare("
                    INSERT INTO domanda_risposte (domanda_id, risposta_id) 
                    VALUES (?, ?)");
                $insert_rel_stmt->bind_param("ii", $domanda_id, $risposta_id);
                $insert_rel_stmt->execute();
                echo "Nuova risposta inserita con immagine per domanda ID: " . $domanda_id . "<br>";
            }
        } else {
            echo "Errore nel salvataggio dell'immagine. Verifica i permessi della cartella.<br>";
            exit;
        }
    }
}

header("Location: checklist.php?componente_id=" . $componente_id . "&attivita_id=" . $attivita_id . "&progetto_id=" . $progetto_id . "&azienda_id=" . $azienda_id . "&linea_prodotto_id=" . $linea_prodotto_id . "&tipo=" . $tipo);
echo "Processo di salvataggio completato. Puoi ora controllare i messaggi di debug sopra.";
exit;
