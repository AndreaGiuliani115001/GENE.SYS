<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del componente e del progetto dalla query string
$componente_id = $_GET['componente_id'];
$componente = $_GET['componente'];
$progetto_id = $_GET['progetto_id'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Recupera i dettagli della checklist con il componente_id e il progetto_id
$checklist_stmt = $conn->prepare("
    SELECT id, nome, descrizione 
    FROM checklist 
    WHERE componente_id = ? AND progetto_id = ?");
$checklist_stmt->bind_param("ii", $componente_id, $progetto_id);
$checklist_stmt->execute();
$checklist = $checklist_stmt->get_result()->fetch_assoc();

// Verifica se la checklist esiste
if (!$checklist) {
    die("Nessuna checklist trovata per questo componente e progetto.");
}

// Recupera le domande e le risposte (se esistenti)
$domande_stmt = $conn->prepare("
    SELECT d.id AS domanda_id, d.testo AS domanda_testo, d.tipo_contenuto, 
           r.valore_testo AS risposta_testo, r.valore_data AS risposta_data, r.valore_media_url AS risposta_media_url 
    FROM domande d
    LEFT JOIN risposte r ON d.id = r.domanda_id AND r.checklist_id = ?
    WHERE d.checklist_id = ?");
$checklist_id = $checklist['id'];
$domande_stmt->bind_param("ii", $checklist_id, $checklist_id);
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

    /* Stili per nascondere le domande */
    .question-container {
        display: none;
    }

    .question-container.active {
        display: block;
    }

    .nav-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2><?= htmlspecialchars($checklist['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($checklist['descrizione'], ENT_QUOTES, 'UTF-8') ?></p>

        <!-- Contenitore per le domande -->
        <form id="checklistForm" method="post" action="salva_risposte.php" enctype="multipart/form-data">
            <?php $questionIndex = 0; ?>
            <?php while ($domanda = $domande->fetch_assoc()): ?>
                <div class="question-container" id="question-<?= $questionIndex ?>" data-question-id="<?= $domanda['domanda_id'] ?>">
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
                <?php $questionIndex++; ?>
            <?php endwhile; ?>

            <!-- Pulsante per salvare tutte le risposte -->
            <button type="submit" class="btn btn-primary w-100 mt-4 btn-rounded">Salva Risposte</button>

            <input type="hidden" name="componente_id" value="<?= $componente_id ?>">
            <input type="hidden" name="progetto_id" value="<?= $progetto_id ?>">
            <input type="hidden" name="checklist_id" value="<?= $checklist_id ?>">

            <!-- Pulsanti per navigare tra le domande -->
            <div class="nav-buttons">
                <button type="button" id="prevButton" class="btn btn-secondary btn-rounded" disabled><i class="fas fa-arrow-left"></i> Indietro</button>
                <button type="button" id="nextButton" class="btn btn-primary btn-rounded"><i class="fas fa-arrow-right"></i> Avanti</button>
            </div>
        </form>

        <!-- Pulsante per tornare alla pagina componenti.php -->
        <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente_id=<?= $componente_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>&componente=<?= $componente ?>"
           class="btn btn-outline-primary mt-5">
            <i class="fas fa-arrow-left"></i> Torna ai Componenti
        </a>

    </div>



    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

<script>
    let currentQuestionIndex = 0;
    const totalQuestions = <?= $questionIndex ?>;

    // Mostra la prima domanda non risolta
    function showQuestion(index) {
        // Nasconde tutte le domande
        document.querySelectorAll('.question-container').forEach(container => container.classList.remove('active'));

        // Mostra la domanda selezionata
        document.getElementById('question-' + index).classList.add('active');

        // Disabilita i pulsanti in base all'indice
        document.getElementById('prevButton').disabled = (index === 0);
        document.getElementById('nextButton').disabled = (index === totalQuestions - 1);
    }

    document.getElementById('prevButton').addEventListener('click', function() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
        }
    });

    document.getElementById('nextButton').addEventListener('click', function() {
        if (currentQuestionIndex < totalQuestions - 1) {
            currentQuestionIndex++;
            showQuestion(currentQuestionIndex);
        }
    });

    // Mostra la prima domanda all'avvio
    showQuestion(currentQuestionIndex);
</script>

</body>
</html>
