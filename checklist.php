<?php
include 'navbar.php';
/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del componente e del progetto dalla query string
$componente_id = $_GET['componente_id'];
$progetto_id = $_GET['progetto_id'];

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

// Recupera le domande e le risposte (se esistenti), evitando duplicazioni
$domande_stmt = $conn->prepare("
    SELECT d.id AS domanda_id, d.testo AS domanda_testo, d.tipo_contenuto, 
           MAX(r.valore_testo) AS risposta_testo, MAX(r.valore_data) AS risposta_data, MAX(r.valore_media_url) AS risposta_media_url 
    FROM domande d
    LEFT JOIN risposte r ON d.id = r.domanda_id AND r.checklist_id = ?
    WHERE d.checklist_id = ?
    GROUP BY d.id");
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
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2><?= htmlspecialchars($checklist['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($checklist['descrizione'], ENT_QUOTES, 'UTF-8') ?></p>

        <!-- Tabella delle domande e risposte -->
        <form method="post" action="salva_risposte.php" enctype="multipart/form-data">
            <table class="table table-bordered table-hover mt-4">
                <thead>
                <tr>
                    <th>Domande</th>
                    <th>Risposte</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($domanda = $domande->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($domanda['domanda_testo'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
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
                                    <img src="<?= htmlspecialchars($domanda['risposta_media_url'], ENT_QUOTES, 'UTF-8') ?>"
                                         class="img-thumbnail mt-2" style="max-width: 150px;">
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <input type="hidden" name="componente_id" value="<?= $componente_id ?>">
            <input type="hidden" name="progetto_id" value="<?= $progetto_id ?>">
            <input type="hidden" name="checklist_id" value="<?= $checklist_id ?>">
            <button type="submit" class="btn btn-primary w-100">Salva Risposte</button>
        </form>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
