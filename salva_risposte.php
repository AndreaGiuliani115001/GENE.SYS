<?php
include 'connection.php'; // Connessione al database
session_start();

// Controlla se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera i dati dal form
$progetto_id = $_POST['progetto_id'];
$componente_id = $_POST['componente_id'];
$nome_componente = $_POST['nome_componente'];
$checklist_id = $_POST['checklist_id'];
$checklist_componente_progetto_id = $_POST['checklist_componente_progetto_id'];
$risposte = $_POST['risposte'] ?? []; // Risposte di tipo testo o data
$risposte_media = $_FILES['risposte'] ?? []; // Risposte di tipo file
$domanda_checklist_componente_progetto_ids = $_POST['domanda_checklist_componente_progetto_ids'] ?? []; // ID domande relazionate alla checklist
$azienda_id = $_POST['azienda_id'];
$linea_prodotto_id = $_POST['linea_prodotto_id'];
$componente_nome = $_POST['componente_nome']; // Nome del componente (per il reindirizzamento)

// Debug iniziale
echo "<pre>";
echo "Progetto ID: $progetto_id\n";
echo "Componente ID: $componente_id\n";
echo "Checklist Componente Progetto ID: $checklist_componente_progetto_id\n";
echo "Risposte Testuali/Data:\n";
print_r($risposte);
echo "Domanda Checklist Componente Progetto IDs:\n";
print_r($domanda_checklist_componente_progetto_ids);
echo "File Uploads:\n";
print_r($risposte_media);
echo "</pre>";

// Avvia la transazione
$conn->begin_transaction();

try {
    // Salva risposte
    foreach ($risposte as $domanda_id => $risposta_valore) {
        if (!empty($risposta_valore)) {
            $domanda_checklist_componente_progetto_id = $domanda_checklist_componente_progetto_ids[$domanda_id];

            // Controlla se esiste già una risposta associata a questa domanda
            $stmt_check = $conn->prepare("
                SELECT r.id, r.testo, r.valore_data, r.valore_media_url 
                FROM risposta_domanda_checklist_componente_progetto rdc
                JOIN risposte r ON rdc.risposta_id = r.id
                WHERE rdc.domanda_checklist_componente_progetto_id = ?
            ");
            $stmt_check->bind_param("i", $domanda_checklist_componente_progetto_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $existing_response = $result->fetch_assoc();

            if ($existing_response) {
                // Aggiorna solo i campi vuoti
                $response_id = $existing_response['id'];
                if (empty($existing_response['testo']) && !empty($risposta_valore)) {
                    $stmt_update = $conn->prepare("UPDATE risposte SET testo = ? WHERE id = ?");
                    $stmt_update->bind_param("si", $risposta_valore, $response_id);
                    $stmt_update->execute();
                }
            } else {
                // Inserisci una nuova risposta
                $stmt_risposta = $conn->prepare("
                    INSERT INTO risposte (tipo_contenuto, testo) VALUES ('testo', ?)
                ");
                $stmt_risposta->bind_param("s", $risposta_valore);
                $stmt_risposta->execute();
                $new_risposta_id = $stmt_risposta->insert_id;

                // Associa la risposta alla domanda
                $stmt_associazione = $conn->prepare("
                    INSERT INTO risposta_domanda_checklist_componente_progetto (domanda_checklist_componente_progetto_id, risposta_id) 
                    VALUES (?, ?)
                ");
                $stmt_associazione->bind_param("ii", $domanda_checklist_componente_progetto_id, $new_risposta_id);
                $stmt_associazione->execute();
            }
        }
    }

    // Salva risposte di tipo file
    foreach ($_FILES['risposte']['name'] as $domanda_id => $file_name) {
        if (!empty($file_name)) {
            $domanda_checklist_componente_progetto_id = $domanda_checklist_componente_progetto_ids[$domanda_id];
            $file_tmp = $_FILES['risposte']['tmp_name'][$domanda_id];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($file_name);

            // Carica il file
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Controlla se esiste già una risposta associata a questa domanda
                $stmt_check = $conn->prepare("
                    SELECT r.id, r.testo, r.valore_data, r.valore_media_url 
                    FROM risposta_domanda_checklist_componente_progetto rdc
                    JOIN risposte r ON rdc.risposta_id = r.id
                    WHERE rdc.domanda_checklist_componente_progetto_id = ?
                ");
                $stmt_check->bind_param("i", $domanda_checklist_componente_progetto_id);
                $stmt_check->execute();
                $result = $stmt_check->get_result();
                $existing_response = $result->fetch_assoc();

                if ($existing_response) {
                    // Aggiorna solo i campi vuoti
                    $response_id = $existing_response['id'];
                    if (empty($existing_response['valore_media_url'])) {
                        $stmt_update = $conn->prepare("UPDATE risposte SET valore_media_url = ? WHERE id = ?");
                        $stmt_update->bind_param("si", $target_file, $response_id);
                        $stmt_update->execute();
                    }
                } else {
                    // Inserisci una nuova risposta
                    $stmt_risposta = $conn->prepare("
                        INSERT INTO risposte (tipo_contenuto, valore_media_url) VALUES ('file', ?)
                    ");
                    $stmt_risposta->bind_param("s", $target_file);
                    $stmt_risposta->execute();
                    $new_risposta_id = $stmt_risposta->insert_id;

                    // Associa la risposta alla domanda
                    $stmt_associazione = $conn->prepare("
                        INSERT INTO risposta_domanda_checklist_componente_progetto (domanda_checklist_componente_progetto_id, risposta_id) 
                        VALUES (?, ?)
                    ");
                    $stmt_associazione->bind_param("ii", $domanda_checklist_componente_progetto_id, $new_risposta_id);
                    $stmt_associazione->execute();
                }
            }
        }
    }

    // Commit della transazione
    $conn->commit();

    // Reindirizzamento corretto
    header("Location: checklist.php?checklist_id=$checklist_id&componente_id=$componente_id&componente=$nome_componente&progetto_id=$progetto_id&azienda_id=$azienda_id&linea_prodotto_id=$linea_prodotto_id");
    exit;

} catch (Exception $e) {
    // Rollback in caso di errore
    $conn->rollback();
    die("Errore durante il salvataggio delle risposte: " . $e->getMessage());
}
?>
