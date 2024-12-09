<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera ID azienda e precompila il campo operativo
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];
$query = $conn->prepare("SELECT campo_operativo_id FROM aziende WHERE id = ?");
$query->bind_param("i", $azienda_id);
$query->execute();
$result = $query->get_result();
$azienda = $result->fetch_assoc();

// Recupera componenti e stati
$componenti = $conn->query("SELECT id, nome FROM componenti")->fetch_all(MYSQLI_ASSOC);
$stati = $conn->query("SELECT id, nome FROM stati")->fetch_all(MYSQLI_ASSOC);
?>
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
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="full-screen-container">
    <div class="container mt-5">
        <h2 class="mb-4">Creazione Nuova Checklist</h2>
        <form action="salva_checklist.php" method="POST" id="checklistForm">
            <input type="hidden" name="azienda_id" value="<?= $azienda_id ?>">
            <input type="hidden" name="campo_operativo_id" value="<?= $campo_operativo_id ?>">

            <!-- Selezione tipo checklist -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tipo di Checklist</h5>
                    <select class="form-select" name="tipo_checklist" id="tipoChecklist" required>
                        <option value="predefinita">Checklist Predefinita</option>
                        <option value="progetto">Checklist Specifica per Progetto</option>
                    </select>
                </div>
            </div>

            <!-- Nome e descrizione -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Dettagli della Checklist</h5>
                    <div class="mb-3">
                        <label for="nomeChecklist" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nomeChecklist" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="descrizioneChecklist" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizioneChecklist" name="descrizione" rows="3"
                                  required></textarea>
                    </div>
                </div>
            </div>

            <!-- Campi per checklist predefinita -->
            <div class="card mb-4 d-none" id="predefinitaFields">
                <div class="card-body">
                    <h5 class="card-title">Configurazione Predefinita</h5>
                    <div class="mb-3">
                        <label for="componenteChecklist" class="form-label">Componente</label>
                        <select class="form-select" id="componenteChecklist" name="componente_id">
                            <?php foreach ($componenti as $componente): ?>
                                <option value="<?= $componente['id'] ?>"><?= htmlspecialchars($componente['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="statoChecklist" class="form-label">Stato</label>
                        <select class="form-select" id="statoChecklist" name="stato_id">
                            <?php foreach ($stati as $stato): ?>
                                <option value="<?= $stato['id'] ?>"><?= htmlspecialchars($stato['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Campi per checklist specifica per progetto -->
            <div class="card mb-4 d-none" id="progettoFields">
                <div class="card-body">
                    <h5 class="card-title">Configurazione Progetto</h5>
                    <div class="mb-3">
                        <label for="faseChecklist" class="form-label">Fase</label>
                        <input type="text" class="form-control" id="faseChecklist" name="fase_id" required>
                    </div>
                </div>
            </div>

            <!-- Aggiungi domande -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Domande</h5>
                    <div id="domandeContainer"></div>
                    <button type="button" class="btn btn-secondary btn-rounded mt-3" onclick="addQuestionBlock()"><i
                                class="fas fa-plus"></i></button>
                </div>
            </div>

            <!-- Pulsante per salvare -->
            <button type="submit" class="btn btn-primary btn-rounded mb-4">Salva Checklist</button>
        </form>
    </div>

    <script>
        let questionIndex = 0;

        function addQuestionBlock() {
            const container = document.getElementById('domandeContainer');
            const block = `
    <div class="card mb-3" id="domanda_${questionIndex}">
        <div class="card-body">
            <h6>Domanda ${questionIndex + 1}</h6>
            <div class="mb-3">
                <label>Tipo di Contenuto:</label>
                <select name="domande[${questionIndex}][tipo_contenuto]" class="form-select" required>
                    <option value="testo">Testo</option>
                    <option value="immagine">Immagine</option>
                    <option value="video">Video</option>
                    <option value="file">File</option>
                    <option value="multiplo">Multiplo</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Valore Effettivo:</label>
                <input type="text" name="domande[${questionIndex}][valore]" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Tipo di Risposta:</label>
                <select name="domande[${questionIndex}][tipo_risposta]" class="form-select" required>
                    <option value="testo">Testo</option>
                    <option value="immagine">Immagine</option>
                    <option value="video">Video</option>
                    <option value="file">File</option>
                    <option value="data">Data</option>
                </select>
            </div>
            <button type="button" class="btn btn-danger btn-rounded" onclick="removeQuestionBlock(${questionIndex})"><i class="fas fa-trash"></i></button>
        </div>
    </div>`;
            container.insertAdjacentHTML('beforeend', block);
            questionIndex++;
        }

        function removeQuestionBlock(index) {
            document.getElementById(`domanda_${index}`).remove();
        }

        document.getElementById('tipoChecklist').addEventListener('change', function () {
            const tipo = this.value;
            document.getElementById('predefinitaFields').classList.toggle('d-none', tipo !== 'predefinita');
            document.getElementById('progettoFields').classList.toggle('d-none', tipo !== 'progetto');
        });
    </script>
    <!-- Footer -->
    <footer class="text-center text-black bg-white py-4">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>