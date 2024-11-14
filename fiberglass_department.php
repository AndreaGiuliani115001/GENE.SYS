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

?>

<style>
    /* Codice esistente */
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

    .step-status div {
        text-align: center;
        font-size: 14px;
    }

    .action-buttons {
        margin-top: 20px;
        text-align: center;
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

    .card {
        border: none;
    }

    /* Aggiunta di margine tra le card sui dispositivi mobili */
    @media (max-width: 768px) {
        .col-md-4 {
            margin-bottom: 20px;
        }
    }
</style>


<div class="full-screen-container">
    <div class="container">
        <!-- Blocco Dettagli -->
        <div class="details-block shadow-sm mt-4">
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8') . " #" . htmlspecialchars($progetto['numero_matricola'], ENT_QUOTES, 'UTF-8') ?></h3>
            <!-- Blocco Progresso -->
            <div class="progress-block">
                <div class="progress-bar">
                    <div></div>
                </div>
            </div>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['stato'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['consegna'], ENT_QUOTES, 'UTF-8') ?></p>
            <div class="action-buttons">
                <a href="checklist.php?progetto_id=<?= $progetto_id ?>&componente=Secondari&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-primary btn-rounded"><i class="fas fa-clipboard-check"></i> Verifica materiale</a>
            </div>
        </div>

        <!-- Sezione con le card dei componenti -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <img src="uploads/scafo.png" class="card-img-top" alt="Immagine Scafo">
                    <div class="card-body text-center">
                        <h5 class="card-title">Scafo Completo</h5>
                        <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente=scafo&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                           class="btn btn-outline-primary btn-rounded">Vai alle checklist</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <img src="uploads/coperta.png" class="card-img-top" alt="Immagine Coperta">
                    <div class="card-body text-center">
                        <h5 class="card-title">Coperta Completa</h5>
                        <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente=coperta&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                           class="btn btn-outline-primary btn-rounded">Vai alle checklist</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <img src="uploads/Secondari.png" class="card-img-top" alt="Immagine Secondari">
                    <div class="card-body text-center">
                        <h5 class="card-title">Secondari</h5>
                        <a href="componenti.php?progetto_id=<?= $progetto_id ?>&componente=secondari&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                           class="btn btn-outline-primary btn-rounded">Vai alle checklist</a>
                    </div>
                </div>
            </div>
        </div>

        <a href="produzione_dashboard.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
           class="btn btn-primary mt-2 mb-4 btn-rounded">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white text-black text-center py-3">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>


</body>
</html>
