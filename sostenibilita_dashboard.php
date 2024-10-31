<?php
include 'navbar.php';

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del progetto dalla query string
$progetto_id = $_GET['progetto_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];
$azienda_id = $_GET['azienda_id'];

// Recupera i dettagli del progetto dal database
$stmt = $conn->prepare("
    SELECT p.numero_matricola, p.cin, p.stato, p.consegna, p.immagine,
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$progetto = $stmt->get_result()->fetch_assoc();
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];

// Recupera i dati di sostenibilità
$sost_stmt = $conn->prepare("
    SELECT * FROM sostenibilita_progetti WHERE progetto_id = ?");
$sost_stmt->bind_param("i", $progetto_id);
$sost_stmt->execute();
$sostenibilita = $sost_stmt->get_result();
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
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: white;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    .card {
        border: none;
    }

</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <!-- Blocco Dettagli -->
        <div class="details-block shadow-sm">
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8'). " #" .htmlspecialchars($progetto['numero_matricola'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['stato'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['consegna'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <!-- Card per il Carbon Footprint -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-leaf fa-3x mb-3 text-success"></i>
                        <h5 class="card-title">Calcolo del Carbon Footprint</h5>
                        <p class="card-text">Scopri l'impatto ambientale del progetto e calcola il carbon footprint
                            associato.</p>
                        <a href="carbon_footprint.php?progetto_id=<?= $progetto_id ?>"
                           class="btn btn-outline-primary btn-rounded mt-3">
                            Vai al calcolo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabella di Sostenibilità -->
            <div class="col-md-8">
                <table class="table table-hover shadow-sm">
                    <thead>
                    <tr>
                        <th>Elemento</th>
                        <th>Descrizione</th>
                        <th>Produttore</th>
                        <th>Peso [kg]</th>
                        <th>% sul totale</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($elemento = $sostenibilita->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($elemento['element'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($elemento['description'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($elemento['productor'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($elemento['weight_kg'], ENT_QUOTES, 'UTF-8') ?> kg</td>
                            <td><?= htmlspecialchars($elemento['percentage_total'], ENT_QUOTES, 'UTF-8') ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pulsante per tornare indietro -->
        <a href="dashboard_progetto.php?progetto_id=<?= $progetto_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>&azienda_id=<?= $azienda_id ?>"
           class="btn btn-primary btn-rounded mt-4"><i class="fas fa-arrow-left"></i></a>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>


</body>
</html>
