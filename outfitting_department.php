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
        border: 2px solid #007bff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
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

    .action-buttons {
        margin-top: 20px;
        text-align: center;
    }

    .action-buttons a {
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

    @media (max-width: 768px) {
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
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8') ?></h3>
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
            <a href="outdoor_setup.php?progetto_id=<?= $progetto_id ?>" class="btn btn-outline-primary">Outdoor
                Setup</a>
            <a href="indoor_setup.php?progetto_id=<?= $progetto_id ?>" class="btn btn-outline-primary">Indoor Setup</a>
        </div>
    </div>
</div>


<!-- Footer -->
<footer class="bg-white text-black text-center py-3">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

</body>
</html>

