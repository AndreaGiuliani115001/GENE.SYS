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
    $numero_matricola = $_POST['numero_matricola'];
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
    $sql_progetto = "INSERT INTO progetti (numero_matricola,nome_cliente, azienda_id, linea_prodotto_id, stato, cin, consegna, immagine) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_progetto);
    $stmt->bind_param("isiissss",$numero_matricola, $cliente, $azienda_id, $linea_prodotto_id, $stato, $cin, $delivery, $target_file);
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
    html, body {
        height: 100%;
        margin: 0;
    }

    .full-screen-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100vh;
    }

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

<div class="full-screen-container">
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h2>Crea un nuovo progetto</h2>

            <!-- Bottone per aggiungere checklist predefinite -->
            <a href="aggiungi_checklist.php.?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-primary btn-rounded mb-4">
                <i class="fas fa-plus"></i> checklist predefinite <!-- Icona per aggiungere -->
            </a>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="numero_matricola" class="form-label">Numero matricola:</label>
                <input type="number" class="form-control" name="numero_matricola">
            </div>

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
                // Filtra i componenti e le loro checklist predefinite
                $sql_componenti = "SELECT id, nome FROM componenti WHERE `default` = 1 AND campo_operativo_id = (SELECT campo_operativo_id FROM aziende WHERE id = ?)";
                $stmt = $conn->prepare($sql_componenti);
                $stmt->bind_param("i", $azienda_id);
                $stmt->execute();
                $result_componenti = $stmt->get_result();

                echo"<h2>Componenti</h2>";
                while ($componente = $result_componenti->fetch_assoc()) {

                    echo "<div class='col-md-6'>";
                    echo "<div class='card'>";
                    echo "<div class='card-header d-flex justify-content-between align-items-center'>";
                    echo "<span>" . $componente['nome'] . "</span>";
                    echo "</div>";
                    echo "<div class='card-body'>";
                    $componente_id = $componente['id'];

                    // Filtra le checklist predefinite per azienda e componente
                    $sql_checklist = "SELECT id, nome FROM checklist_predefinite WHERE componente_id = ? AND azienda_id = ?";
                    $stmt_checklist = $conn->prepare($sql_checklist);
                    $stmt_checklist->bind_param("ii", $componente_id, $azienda_id);
                    $stmt_checklist->execute();
                    $result_checklist = $stmt_checklist->get_result();

                    while ($checklist = $result_checklist->fetch_assoc()) {
                        echo "<div class='d-flex justify-content-between align-items-center'>";
                        echo "<div>";
                        echo "<input class='form-check-input' type='checkbox' name='checklist[$componente_id][]' value='" . $checklist['id'] . "'> ";
                        echo "<label class='form-check-label'>" . $checklist['nome'] . "</label>";
                        echo "</div>";

                        // Bottoni di modifica ed eliminazione
                        echo "<div class='btn-group'>";
                        echo "<a href='modifica_checklist_predefinita.php?id=" . $checklist['id'] . "' class='btn btn-sm btn-rounded btn-warning'>
                 <i class='fas fa-edit'></i>
              </a>";

                        echo "<a href='elimina_checklist_predefinita.php?id=" . $checklist['id'] . "' class='btn btn-sm btn-rounded btn-danger' onclick='return confirm(\"Sei sicuro di voler eliminare questa checklist?\")'>
                 <i class='fas fa-trash-alt'></i>
              </a>";
                        echo "</div>";
                        echo "</div>";
                    }

                    echo "</div>"; // Fine della card-body
                    echo "</div>"; // Fine della card
                    echo "</div>"; // Fine della colonna
                }
                ?>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <a href="master_progetti.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-primary btn-rounded">
                    <i class="fas fa-arrow-left"></i> <!-- Icona per tornare indietro -->
                </a>
                <button type="submit" class="btn btn-primary btn-rounded">Salva Progetto</button>
            </div>


        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3 mt-4">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
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
                    <input type="text" class="form-control" id="projectLink"
                           value="http://localhost/gene-sys/dashboard_progetto.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                           readonly>
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
