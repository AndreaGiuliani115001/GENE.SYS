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

    /* Stile delle card */
    .card {
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
        <div class="row">
            <?php while ($macro = $macro_result->fetch_assoc()): ?>
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($macro['nome'], ENT_QUOTES, 'UTF-8') ?></h5>

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
                            <ul class="activity-list list-group">
                                <?php while ($attivita = $attivita_result->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <?= htmlspecialchars($attivita['nome'], ENT_QUOTES, 'UTF-8') ?>

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
                                                        <i class="fas fa-clipboard-check"></i> checklist
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
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
