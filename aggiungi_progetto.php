<?php
include 'navbar.php'; // Include la barra di navigazione
include 'connection.php'; // Connessione al database

// Recupera i valori azienda_id e linea_prodotto_id dal GET
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Variabile per attivare il modal dopo la creazione del progetto
$showModal = false;

// Se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente = $_POST['cliente'];
    $cin = $_POST['cin'];
    $delivery = $_POST['delivery'];
    $immagine = $_FILES['immagine']['name'];
    $stato = $_POST['stato']; // Stato selezionato dall'utente

    // Salva l'immagine
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($immagine);
    move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file);

    // Inserisci il progetto
    $sql_progetto = "INSERT INTO progetti (nome_cliente, azienda_id, linea_prodotto_id, stato, cin, consegna, immagine) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_progetto);
    $stmt->bind_param("siissss", $cliente, $azienda_id, $linea_prodotto_id, $stato, $cin, $delivery, $target_file);
    $stmt->execute();

    $progetto_id = $conn->insert_id; // Ottieni l'ID del progetto appena creato
    $showModal = true;

    // Popola i componenti predefiniti
    $sql_componenti = "SELECT id FROM componenti WHERE `default` = 1";
    $result_componenti = $conn->query($sql_componenti);

    while ($componente = $result_componenti->fetch_assoc()) {
        $componente_id = $componente['id'];

        // Inserisci il componente associato al progetto
        $sql_componente = "INSERT INTO componente_progetto (componente_id, progetto_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_componente);
        $stmt->bind_param("ii", $componente_id, $progetto_id);
        $stmt->execute();
        $componente_progetto_id = $conn->insert_id;

        // Se l'utente ha selezionato delle checklist per questo componente, inserisci le relazioni
        if (isset($_POST['checklist'][$componente_id])) {
            foreach ($_POST['checklist'][$componente_id] as $checklist_predefinita_id) {
                // Copia la checklist predefinita nella tabella checklist
                $sql_copia_checklist = "INSERT INTO checklist (nome, descrizione, fase_id, campo_operativo_id)
                                        SELECT nome, descrizione, fase_id, campo_operativo_id 
                                        FROM checklist_predefinite 
                                        WHERE id = ?";
                $stmt = $conn->prepare($sql_copia_checklist);
                $stmt->bind_param("i", $checklist_predefinita_id);
                $stmt->execute();
                $checklist_id = $conn->insert_id; // Ottieni l'ID della checklist copiata

                // Inserisci il collegamento nella tabella checklist_componente_progetto
                $sql_checklist = "INSERT INTO checklist_componente_progetto (checklist_id, componente_progetto_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql_checklist);
                $stmt->bind_param("ii", $checklist_id, $componente_progetto_id);
                $stmt->execute();
                $checklist_componente_progetto_id = $conn->insert_id;

                // Duplica le domande per la checklist selezionata
                $sql_domande_predefinite = "SELECT id, testo, tipo_contenuto, valore_media_url, tipo_risposta FROM domande_predefinite WHERE checklist_predefinita_id = ?";
                $stmt = $conn->prepare($sql_domande_predefinite);
                $stmt->bind_param("i", $checklist_predefinita_id);
                $stmt->execute();
                $result_domande_predefinite = $stmt->get_result();

                while ($domanda_predefinita = $result_domande_predefinite->fetch_assoc()) {
                    // Duplica la domanda nella tabella domande, includendo tipo_risposta
                    $sql_duplica_domanda = "INSERT INTO domande (testo, tipo_contenuto, valore_media_url, tipo_risposta) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql_duplica_domanda);
                    $stmt->bind_param("ssss", $domanda_predefinita['testo'], $domanda_predefinita['tipo_contenuto'], $domanda_predefinita['valore_media_url'], $domanda_predefinita['tipo_risposta']);
                    $stmt->execute();

                    $domanda_id = $conn->insert_id; // Ottieni l'ID della domanda appena duplicata

                    // Collega la domanda duplicata alla checklist_componente_progetto
                    $sql_domanda_checklist = "INSERT INTO domanda_checklist_componente_progetto (domanda_id, checklist_componente_progetto_id)
                              VALUES (?, ?)";
                    $stmt = $conn->prepare($sql_domanda_checklist);
                    $stmt->bind_param("ii", $domanda_id, $checklist_componente_progetto_id);
                    $stmt->execute();
                }


            }
        }
    }

}
?>

<!-- Frontend -->
<style>
    .card {
        border: none;
        flex-direction: column;
        margin-bottom: 20px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        height: 100%; /* Altezza uguale per tutti */
    }

    .card-header {
        background-color: #27bcbc; /* Cambia il colore per maggiore contrasto */
        color: white;
        font-weight: bold;
    }

    .card-body {
        padding: 10px 15px;
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Distribuisce il contenuto */
    }

    .checklist-ul {
        padding-left: 20px;
    }

    .checklist-ul li {
        margin-bottom: 5px;
    }

    .form-check-label {
        font-weight: 600;
    }

    .col-md-6 {
        margin-bottom: 20px; /* Spazio tra i blocchi */
    }
</style>


<div class="container mt-5">
    <div class="d-flex justify-content-between mb-4">
        <!-- Bottone per tornare ai progetti -->
        <a href="master_progetti.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-primary btn-rounded">
            <i class="fas fa-arrow-left"></i> <!-- Icona per tornare indietro -->
        </a>

        <!-- Bottone per aggiungere checklist predefinite -->
        <a href="aggiungi_checklist_predefinite.php" class="btn btn-primary btn-rounded">
            <i class="fas fa-plus"></i> checklist predefinite <!-- Icona per aggiungere -->
        </a>
    </div>


    <h2>Crea un nuovo progetto</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="cliente" class="form-label">Cliente:</label>
            <input type="text" class="form-control" name="cliente" required>
        </div>

        <div class="mb-3">
            <label for="cin" class="form-label">CIN (Codice Identificativo):</label>
            <input type="text" class="form-control" name="cin" required>
        </div>

        <div class="mb-3">
            <label for="delivery" class="form-label">Data di Consegna:</label>
            <input type="date" class="form-control" name="delivery" required>
        </div>

        <div class="mb-3">
            <label for="stato" class="form-label">Stato del Progetto:</label>
            <select class="form-select" name="stato" required>
                <option value="da iniziare">Da Iniziare</option>
                <option value="in lavorazione">In Lavorazione</option>
                <option value="completo">Completo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="immagine" class="form-label">Immagine:</label>
            <input type="file" class="form-control" name="immagine" required>
        </div>

        <div class="row">
            <?php
            // Popola i componenti e le loro checklist predefinite
            $sql_componenti = "SELECT id, nome FROM componenti WHERE `default` = 1";
            $result_componenti = $conn->query($sql_componenti);
            while ($componente = $result_componenti->fetch_assoc()) {
                echo "<div class='col-md-6'>";
                echo "<div class='card'>";
                echo "<div class='card-header d-flex justify-content-between align-items-center'>";
                echo "<span>" . $componente['nome'] . "</span>";
                echo "</div>";
                echo "<div class='card-body'>";
                $componente_id = $componente['id'];

                // Trova le checklist predefinite per questo componente
                $sql_checklist = "SELECT id, nome FROM checklist_predefinite WHERE componente_id = $componente_id";
                $result_checklist = $conn->query($sql_checklist);
                while ($checklist = $result_checklist->fetch_assoc()) {
                    echo "<div class='d-flex justify-content-between align-items-center'>";
                    echo "<div>";
                    echo "<input class='form-check-input' type='checkbox' name='checklist[$componente_id][]' value='" . $checklist['id'] . "'> ";
                    echo "<label class='form-check-label'>" . $checklist['nome'] . "</label>";
                    echo "</div>";

                    // Bottoni di modifica ed eliminazione
                    echo "<div>";
                    echo "<a href='modifica_checklist_predefinita.php?id=" . $checklist['id'] . "' class='btn btn-sm btn-rounded btn-warning me-2'>
                     <i class='fas fa-edit'></i>
                  </a>";
                    echo "<a href='elimina_checklist_predefinita.php?id=" . $checklist['id'] . "' class='btn btn-sm btn-rounded btn-danger' onclick='return confirm(\"Sei sicuro di voler eliminare questa checklist?\")'>
                     <i class='fas fa-trash-alt'></i>
                  </a>";
                    echo "</div>";
                    echo "</div>";

                    // Mostra le domande predefinite collegate a questa checklist
                    $sql_domande = "SELECT d.testo 
                            FROM domande_predefinite d
                            WHERE d.checklist_predefinita_id = " . $checklist['id'];
                    $result_domande = $conn->query($sql_domande);

                    echo "<ul class='checklist-ul'>";
                    while ($domanda = $result_domande->fetch_assoc()) {
                        echo "<li>" . $domanda['testo'] . "</li>";
                    }
                    echo "</ul>";
                }

                echo "</div>"; // Fine della card-body
                echo "</div>"; // Fine della card
                echo "</div>"; // Fine della colonna
            }
            ?>
        </div>


        <button type="submit" class="btn btn-primary mt-2 btn-rounded">Salva Progetto</button>
    </form>
</div>

<!-- Footer -->
<footer class="bg-white text-black text-center py-3 mt-4">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

<!-- Modal per visualizzare il link del progetto -->
<div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Progetto Creato con Successo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Il progetto è stato creato con successo! Puoi accedere al progetto utilizzando il seguente link:</p>
                <div class="input-group">
                    <input type="text" class="form-control" id="projectLink" value="http://localhost/gene-sys/dashboard_progetto.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" readonly>
                    <button class="btn btn-outline-primary" type="button" id="copyButton">Copia Link</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Funzione per copiare il link negli appunti
    document.getElementById('copyButton').addEventListener('click', function () {
        var copyText = document.getElementById("projectLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Per i dispositivi mobili
        document.execCommand("copy");
        alert("Link copiato: " + copyText.value);
    });

    // Mostra il modal se il progetto è stato creato
    <?php if ($showModal): ?>
    var myModal = new bootstrap.Modal(document.getElementById('projectModal'));
    myModal.show();
    <?php endif; ?>
</script>


</body>
</html>
