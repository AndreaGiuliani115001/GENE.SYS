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

// Recupera i dettagli del progetto dal database
$stmt = $conn->prepare("
    SELECT p.cin, p.state, p.delivery, p.immagine,
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
            border: 2px solid #27bcbc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: white;
        }

        .support-buttons {
            margin-top: 20px;
            text-align: center;
        }

        .support-buttons a {
            margin: 10px;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 16px;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
        }

    </style>

<div class="full-screen-container">
    <div class="container mt-5">
        <!-- Blocco Dettagli -->
        <div class="details-block">
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <!-- Tabella di Sostenibilità -->
        <table class="table table-bordered table-hover mt-4">
            <thead>
            <tr>
                <th>Element</th>
                <th>Description</th>
                <th>Productor</th>
                <th>Weight [kg]</th>
                <th>% in the total</th>
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

        <!-- Pulsante per tornare indietro -->
        <a href="dashboard_progetto.php?progetto_id=<?= $progetto_id ?>" class="btn btn-secondary mt-4">Torna alla Dashboard Progetto</a>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
