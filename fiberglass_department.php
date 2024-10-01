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

// Recupera l'ID dell'azienda dalla query string
$azienda_id = $_GET['azienda_id'];

// Recupera l'ID della linea di prodotto dalla query string
$linea_prodotto_id = $_GET['linea_prodotto_id'];

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
        padding: 20px;
    }

    .details-block {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: white;
    }

    .progress-block {
        margin: 30px 0;
    }

    .progress-bar {
        width: 100%;
        background-color: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
    }

    .progress-bar div {
        background-color: #27bcbc;
        height: 30px;
    }

    .step-status {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        font-weight: bold;
    }

    .step-status div {
        text-align: center;
        font-size: 14px;
    }

    .action-buttons {
        margin-top: 20px;
        text-align: center;
    }

    .action-buttons a {
        margin: 5px;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 16px;
    }

    .project-image {
        text-align: center;
        margin-top: 20px;
    }

    .project-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .step-status {
            flex-direction: column;
        }

        .details-block {
            text-align: center;
        }

        .project-image img {
            max-width: 100%;
        }
    }
</style>

<div class="full-screen-container">
    <div class="container">
        <!-- Blocco Dettagli -->
        <div class="details-block shadow-sm">
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8') . " #" . $progetto_id ?></h3>
            <!-- Blocco Progresso -->
            <div class="progress-block">
                <div class="progress-bar">
                    <div></div>
                </div>
            </div>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>
            <div class="action-buttons">
                <a href="checklist.php?progetto_id=<?= $progetto_id ?>&componente=Secondari&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-primary btn-rounded"><i class="fas fa-clipboard-check"></i> Verifica materiale</a>
                <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente=scafo&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-outline-primary"><i class="fas fa-ship"></i> Scafo completo</a>
                <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente=coperta&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-outline-primary"><i class="fas fa-layer-group"></i> Coperta completa</a>
                <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente=secondari&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-outline-primary"><i class="fas fa-cogs"></i> Secondari</a>
            </div>
        </div>
        <a href="produzione_dashboard.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-outline-primary mt-4">
            <i class="fas fa-arrow-left"></i> Torna Alla Dashboard Produzione
        </a>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white text-black text-center py-3">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

</body>
</html>
