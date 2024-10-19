<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del componente e del progetto dalla query string
$componente_id = $_GET['componente_id'] ?? null;
$progetto_id = $_GET['progetto_id'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Recupera la checklist in base al componente
$checklist_stmt = $conn->prepare("
    SELECT c.id, c.nome, c.descrizione, ccp.id AS checklist_componente_progetto_id
    FROM checklist c
    JOIN checklist_componente_progetto ccp ON c.id = ccp.checklist_id
    JOIN componente_progetto cp ON ccp.componente_progetto_id = cp.id
    WHERE cp.componente_id = ? AND cp.progetto_id = ?");
$checklist_stmt->bind_param("ii", $componente_id, $progetto_id);
$checklist_stmt->execute();
$checklist = $checklist_stmt->get_result()->fetch_assoc();

if (!$checklist) {
    die("Nessuna checklist trovata per questo componente.");
}

$checklist_componente_progetto_id = $checklist['checklist_componente_progetto_id'];

// Recupera le domande e le risposte per la checklist
$domande_stmt = $conn->prepare("
    SELECT d.id AS domanda_id, d.testo AS domanda_testo, d.tipo_contenuto, d.tipo_risposta, d.valore_media_url,
           r.valore_testo AS risposta_testo, r.valore_data AS risposta_data, r.valore_media_url AS risposta_media_url,
           dccp.id AS domanda_checklist_componente_progetto_id
    FROM domande d
    JOIN domanda_checklist_componente_progetto dccp ON d.id = dccp.domanda_id
    LEFT JOIN risposta_domanda_checklist_componente_progetto rdc ON dccp.id = rdc.domanda_checklist_componente_progetto_id
    LEFT JOIN risposte r ON rdc.risposta_id = r.id
    WHERE dccp.checklist_componente_progetto_id = ?");
$domande_stmt->bind_param("i", $checklist_componente_progetto_id);
$domande_stmt->execute();
$domande = $domande_stmt->get_result();
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

    .container {
        flex-grow: 1;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    .media-display img {
        max-width: 150px;
        margin-top: 10px;
        cursor: pointer;
    }

    .media-display video, .media-display audio {
        max-width: 100%;
        margin-top: 10px;
        cursor: pointer;
    }

    .media-display a {
        color: #007bff;
        text-decoration: none;
    }

    .media-display a:hover {
        text-decoration: underline;
    }

    .img-container {
        margin-top: 10px;
    }

    .img-thumbnail {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .img-thumbnail:hover {
        transform: scale(1.05);
    }
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2><?= htmlspecialchars($checklist['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($checklist['descrizione'], ENT_QUOTES, 'UTF-8') ?></p>

        <!-- Contenitore per le domande e risposte -->
        <form id="checklistForm" method="post" action="salva_risposte.php" enctype="multipart/form-data">
            <input type="hidden" name="progetto_id" value="<?= $progetto_id ?>">
            <input type="hidden" name="componente_id" value="<?= $componente_id ?>">
            <input type="hidden" name="checklist_componente_progetto_id" value="<?= $checklist_componente_progetto_id ?>">
            <input type="hidden" name="azienda_id" value="<?= $azienda_id ?>">
            <input type="hidden" name="linea_prodotto_id" value="<?= $linea_prodotto_id ?>">

            <?php while ($domanda = $domande->fetch_assoc()): ?>
                <div class="question-container mb-4">
                    <h5><?= htmlspecialchars($domanda['domanda_testo'], ENT_QUOTES, 'UTF-8') ?></h5>

                    <!-- Se la domanda ha un valore multimediale -->
                    <?php if (!empty($domanda['valore_media_url'])): ?>
                        <div class="media-display">
                            <?php if (strpos($domanda['valore_media_url'], '.pdf') === false): ?>
                                <!-- Mostra immagine o video -->
                                <?php if (strpos($domanda['valore_media_url'], '.mp4') !== false): ?>
                                    <a href="<?= htmlspecialchars($domanda['valore_media_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                                        <video controls>
                                            <source src="<?= htmlspecialchars($domanda['valore_media_url'], ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
                                            Il tuo browser non supporta la visualizzazione video.
                                        </video>
                                    </a>
                                <?php else: ?>
                                    <div class="img-container">
                                        <img src="<?= htmlspecialchars($domanda['valore_media_url'], ENT_QUOTES, 'UTF-8') ?>" alt="Immagine" class="img-thumbnail img-responsive mb-3" onclick="openModalImage('<?= htmlspecialchars($domanda['valore_media_url'], ENT_QUOTES, 'UTF-8') ?>')" />
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- PDF -->
                                <a href="<?= htmlspecialchars($domanda['valore_media_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank"><i class="fas fa-file-pdf mb-3"></i> Visualizza PDF</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Risposta alla domanda in base al tipo_risposta -->
                    <?php if ($domanda['tipo_risposta'] == 'testo'): ?>
                        <input type="text" name="risposte[<?= $domanda['domanda_id'] ?>]" value="<?= htmlspecialchars($domanda['risposta_testo'], ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    <?php elseif ($domanda['tipo_risposta'] == 'data'): ?>
                        <input type="date" name="risposte[<?= $domanda['domanda_id'] ?>]" value="<?= htmlspecialchars($domanda['risposta_data'], ENT_QUOTES, 'UTF-8') ?>" class="form-control">
                    <?php elseif (in_array($domanda['tipo_risposta'], ['immagine', 'video', 'file'])): ?>
                        <input type="file" name="risposte[<?= $domanda['domanda_id'] ?>]" class="form-control">
                        <?php if (!empty($domanda['risposta_media_url'])): ?>
                            <div class="media-display">
                                <a href="<?= htmlspecialchars($domanda['risposta_media_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($domanda['risposta_media_url'], ENT_QUOTES, 'UTF-8') ?>" class="img-thumbnail mt-2">
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>

            <!-- Pulsante per salvare tutte le risposte -->
            <button type="submit" class="btn btn-primary w-100 mt-4 btn-rounded">Salva Risposte</button>
        </form>

        <!-- Pulsante per tornare alla pagina componenti -->
        <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente_id=<?= $componente_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
           class="btn btn-outline-primary mt-5">
            <i class="fas fa-arrow-left"></i> Torna alla pagina precedente
        </a>
    </div>

    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

<!-- Modal per visualizzare l'immagine ingrandita -->
<div id="imageModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalImage" src="" class="img-fluid" alt="Immagine Ingrandita">
            </div>
        </div>
    </div>
</div>

<script>
    function openModalImage(imageUrl) {
        var modalImage = document.getElementById("modalImage");
        modalImage.src = imageUrl;
        var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
        myModal.show();
    }
</script>
