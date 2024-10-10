<?php
include 'navbar.php';
include('connection.php');


// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del componente/intervento e del progetto dalla query string
$componente_id = $_GET['componente_id'] ?? null;
$attivita_id = $_GET['attivita_id'] ?? null;  // Se si tratta di una attività di manutenzione
$progetto_id = $_GET['progetto_id'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];
$tipo = $_GET['tipo'] ?? 'produzione';  // Specifica se è produzione o manutenzione (default 'produzione')

// Recupera la checklist in base al componente o attività
if ($tipo === 'produzione') {
    // Query per la produzione (componenti)
    $checklist_stmt = $conn->prepare("
        SELECT c.id, c.nome, c.descrizione 
        FROM checklist c
        JOIN checklist_componenti cc ON c.id = cc.checklist_id
        WHERE cc.componente_id = ? AND cc.progetto_id = ?");
    $checklist_stmt->bind_param("ii", $componente_id, $progetto_id);
} else {
    // Query per la manutenzione (attività/interventi)
    $checklist_stmt = $conn->prepare("
        SELECT c.id, c.nome, c.descrizione 
        FROM checklist c
        JOIN checklist_attivita ca ON c.id = ca.checklist_id
        WHERE ca.attivita_id = ? AND ca.progetto_id = ?");
    $checklist_stmt->bind_param("ii", $attivita_id, $progetto_id);

}

$checklist_stmt->execute();
$checklist = $checklist_stmt->get_result()->fetch_assoc();

// Verifica se la checklist esiste
if (!$checklist) {
    die("Nessuna checklist trovata per questo elemento.");
}

$checklist_id = $checklist['id'];

// Recupera le domande e le risposte per la checklist
$domande_stmt = $conn->prepare("
    SELECT d.id AS domanda_id, d.testo AS domanda_testo, d.tipo_contenuto,
           r.valore_testo AS risposta_testo, r.valore_data AS risposta_data, r.valore_media_url AS risposta_media_url
    FROM domande d
    JOIN checklist_domande cd ON d.id = cd.domanda_id
    LEFT JOIN domanda_risposte dr ON d.id = dr.domanda_id
    LEFT JOIN risposte r ON dr.risposta_id = r.id
    WHERE cd.checklist_id = ?");
$domande_stmt->bind_param("i", $checklist_id);
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

</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2><?= htmlspecialchars($checklist['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($checklist['descrizione'], ENT_QUOTES, 'UTF-8') ?></p>

        <!-- Contenitore per le domande e risposte -->
        <form id="checklistForm" method="post" action="salva_risposte.php" enctype="multipart/form-data">
            <?php while ($domanda = $domande->fetch_assoc()): ?>
                <div class="question-container mb-4">
                    <h5><?= htmlspecialchars($domanda['domanda_testo'], ENT_QUOTES, 'UTF-8') ?></h5>

                    <!-- Risposta alla domanda -->
                    <?php if ($domanda['tipo_contenuto'] == 'testo'): ?>
                        <input type="text" name="risposte[<?= $domanda['domanda_id'] ?>]"
                               value="<?= htmlspecialchars($domanda['risposta_testo'], ENT_QUOTES, 'UTF-8') ?>"
                               class="form-control">
                    <?php elseif ($domanda['tipo_contenuto'] == 'data'): ?>
                        <input type="date" name="risposte[<?= $domanda['domanda_id'] ?>]"
                               value="<?= htmlspecialchars($domanda['risposta_data'], ENT_QUOTES, 'UTF-8') ?>"
                               class="form-control">
                    <?php elseif ($domanda['tipo_contenuto'] == 'immagine'): ?>
                        <input type="file" name="risposte[<?= $domanda['domanda_id'] ?>]" class="form-control">
                        <?php if (!empty($domanda['risposta_media_url'])): ?>
                            <img src="<?= htmlspecialchars($domanda['risposta_media_url'], ENT_QUOTES, 'UTF-8') ?>" class="img-thumbnail mt-2" style="max-width: 150px;">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>

            <!-- Pulsante per salvare tutte le risposte -->
            <button type="submit" class="btn btn-primary w-100 mt-4 btn-rounded">Salva Risposte</button>

            <input type="hidden" name="progetto_id" value="<?= $progetto_id ?>">
            <input type="hidden" name="azienda_id" value="<?= $azienda_id ?>">
            <input type="hidden" name="linea_prodotto_id" value="<?= $linea_prodotto_id ?>">
            <input type="hidden" name="componente_id" value="<?= $componente_id ?>">
            <input type="hidden" name="attivita_id" value="<?= $attivita_id ?>">
            <input type="hidden" name="tipo" value="<?= $tipo ?>">
            <input type="hidden" name="checklist_id" value="<?= $checklist_id ?>">
        </form>

        <!-- Pulsante per tornare alla pagina componenti/attività -->
        <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente_id=<?= $componente_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>&tipo=<?= $tipo ?>"
           class="btn btn-outline-primary mt-5">
            <i class="fas fa-arrow-left"></i> Torna alla pagina precedente
        </a>
    </div>

    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>
