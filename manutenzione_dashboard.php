<?php
include 'navbar.php';
include('connection.php');

// Recupera i parametri dalla query string
$progetto_id = $_GET['progetto_id'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera il campo operativo dell'azienda
$stmt = $conn->prepare("
    SELECT a.campo_operativo_id 
    FROM aziende a 
    JOIN progetti p ON a.id = p.azienda_id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$result = $stmt->get_result();
$campo_operativo = $result->fetch_assoc()['campo_operativo_id'];

// Query per ottenere le macro-categorie specifiche per il campo operativo
$query = "SELECT id, nome FROM macro_categorie WHERE campo_operativo_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $campo_operativo);
$stmt->execute();
$macro_result = $stmt->get_result();


// Recupera i dettagli del progetto e il campo operativo dal database
$stmt = $conn->prepare("
    SELECT p.numero_matricola, p.cin, p.stato, p.consegna, p.immagine,
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           a.campo_operativo_id, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Progetto non trovato.");
}

$progetto = $result->fetch_assoc();
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['numero_matricola'];
$campo_operativo_id = $progetto['campo_operativo_id'];

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

    .details-block {
        border-radius: 8px;
        background-color: white;
        min-height: 300px; /* Imposta la stessa altezza per il details block e le schede */
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        border: none;
    }

    /* Stile delle card */
    .card {
        border: none;
        margin-bottom: 30px;
        border-radius: 10px;
    }

    /* Limita l'altezza dell'elenco delle attività per evitare che allarghi le card */
    .activity-list, .checklist-list {
        max-height: 150px; /* Limita l'altezza massima dell'elenco */
        overflow-y: auto; /* Aggiunge lo scroll verticale se necessario */
    }

    /* Migliora i link */
    .checklist-list a {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 10px;
        background-color: #27bcbc;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <div class="details-block shadow-sm mb-4">
            <h3><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['stato'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['consegna'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="row">
            <?php while ($macro = $macro_result->fetch_assoc()): ?>
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <!-- Intestazione della card con titolo e bottoni -->
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><?= htmlspecialchars($macro['nome'], ENT_QUOTES, 'UTF-8') ?></h5>

                                <!-- Bottoni allineati a destra -->
                                <div class="btn-group">
                                    <a href="crea_macro.php?id=<?= $macro['id'] ?>"
                                       class="btn btn-primary btn-sm btn-rounded" title="Crea">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    <a href="modifica_macro.php?id=<?= $macro['id'] ?>"
                                       class="btn btn-warning btn-sm btn-rounded" title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="elimina_macro.php?id=<?= $macro['id'] ?>"
                                       class="btn btn-danger btn-sm btn-rounded" title="Elimina"
                                       onclick="return confirm('Sei sicuro di voler eliminare questa macro-categoria?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Query per ottenere le attività correlate a questa macro-categoria -->
                            <?php
                            $attivita_stmt = $conn->prepare("
                                SELECT a.id, a.nome 
                                FROM attivita a
                                JOIN progetti_attivita pa ON a.id = pa.attivita_id
                                WHERE a.macro_categoria_id = ? AND pa.progetto_id = ?");
                            $attivita_stmt->bind_param("ii", $macro['id'], $progetto_id);
                            $attivita_stmt->execute();
                            $attivita_result = $attivita_stmt->get_result();
                            ?>

                            <!-- Elenco delle attività sotto la macro-categoria -->
                            <ul class="activity-list list-group mt-3">
                                <?php while ($attivita = $attivita_result->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <?= htmlspecialchars($attivita['nome'], ENT_QUOTES, 'UTF-8') ?>

                                            <!-- Bottoni allineati a destra -->
                                            <div class="btn-group">
                                                <a href="modifica_macro.php?id=<?= $macro['id'] ?>"
                                                   class="btn btn-outline-warning btn-sm btn-rounded" title="Modifica">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="elimina_macro.php?id=<?= $macro['id'] ?>"
                                                   class="btn btn-outline-danger btn-sm btn-rounded" title="Elimina"
                                                   onclick="return confirm('Sei sicuro di voler eliminare questa macro-categoria?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Query per ottenere le checklist correlate a questa attività -->
                                        <?php
                                        $checklist_stmt = $conn->prepare("
                                            SELECT c.id, c.nome 
                                            FROM checklist c
                                            JOIN checklist_attivita ca ON c.id = ca.checklist_id
                                            WHERE ca.attivita_id = ?");
                                        $checklist_stmt->bind_param("i", $attivita['id']);
                                        $checklist_stmt->execute();
                                        $checklist_result = $checklist_stmt->get_result();
                                        ?>

                                        <!-- Elenco delle checklist per ogni attività -->
                                        <ul class="checklist-list list-unstyled">
                                            <?php while ($checklist = $checklist_result->fetch_assoc()): ?>
                                                <li>
                                                    <a href="checklist.php?attivita_id=<?= $attivita['id'] ?>&checklist_id=<?= $checklist['id'] ?>&progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>&tipo=manutenzione">
                                                        <i class="fas fa-clipboard-check"></i> <?= htmlspecialchars($checklist['nome'], ENT_QUOTES, 'UTF-8') ?>
                                                    </a>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="d-flex justify-content-between mb-4">
            <a href="dashboard_progetto.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>&progetto_id=<?= $progetto_id ?>"
               class="btn btn-primary btn-rounded">
                <i class="fas fa-arrow-left"></i> <!-- Icona per tornare indietro -->
            </a>
            <button type="submit" class="btn btn-primary btn-rounded">Nuova macro-categoria</button>
        </div>
    </div>


    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>


</body>
</html>
