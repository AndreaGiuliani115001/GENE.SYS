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

// Recupera i dettagli di avanzamento nel Fiberglass Department
$prod_stmt = $conn->prepare("
    SELECT * FROM produzione_fiberglass WHERE progetto_id = ?");
$prod_stmt->bind_param("i", $progetto_id);
$prod_stmt->execute();
$produzione = $prod_stmt->get_result()->fetch_assoc();

// Imposta le date di inizio e fine, e gli step
$start_date = $produzione['start_date'];
$end_date = $produzione['end_date'];
$step1 = $produzione['step1_fiberglass'];
$step2 = $produzione['step2_paint'];
$step3 = $produzione['step3_mounting'];
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
        border: 2px solid #27bcbc;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
    }

    .progress-block {
        text-align: center;
        margin: 30px 0;
    }

    .progress-bar {
        width: 100%;
        background-color: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
    }

    .progress-bar div {
        width: <?= ($step1 + $step2 + $step3) * 33.33 ?>%;
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
        <div class="details-block">
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8'). " #" . $progetto_id  ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <!-- Blocco Immagine -->
        <div class="project-image">
            <img src="<?= htmlspecialchars($progetto['immagine'], ENT_QUOTES, 'UTF-8') ?>" alt="Immagine Progetto">
        </div>

        <!-- Blocco Azioni -->
        <div class="action-buttons">
            <a href="#" class="btn btn-outline-primary">Technical Project</a>
            <a href="#" class="btn btn-outline-primary">Boat Configuration</a>
            <a href="#" class="btn btn-outline-primary">Print Status Checklist</a>
        </div>

        <!-- Blocco Progresso -->
        <div class="progress-block">
            <h4>Avanzamento</h4>
            <div class="progress-bar">
                <div></div>
            </div>
            <div class="step-status">
                <div><strong>Step 1:</strong> Fiberglass <?= $step1 ? '✔' : '❌' ?><br><br><p><strong>Start Date:</strong> <?= htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8') ?></p></div>
                <div><strong>Step 2:</strong> Paint <?= $step2 ? '✔' : '❌' ?></div>
                <div><strong>Step 3:</strong> Mounting <?= $step3 ? '✔' : '❌' ?><br><br><p><strong>End Date:</strong> <?= htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8') ?></p></div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white text-black text-center py-3">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

</body>
</html>
