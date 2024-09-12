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
$result = $stmt->get_result();

// Se il progetto non viene trovato
if ($result->num_rows === 0) {
    die("Progetto non trovato.");
}

$progetto = $result->fetch_assoc();
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];
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
        text-align: left;
        border: 2px solid #000;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
    }

    .project-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .department-block {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .department-block a {
        padding: 15px 20px;
        width: 100%;
        max-width: 250px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        border-radius: 50px;
        text-decoration: none;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    /* Ottimizzazione per schermi piccoli */
    @media (max-width: 768px) {
        .details-block {
            text-align: center;
        }

        .department-block a {
            max-width: 100%;
        }
    }
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <div class="row align-items-center">
            <!-- Dettagli del progetto -->
            <div class="col-md-6 mb-4">
                <div class="details-block">
                    <h3><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>

            <!-- Immagine del progetto -->
            <div class="col-md-6 mb-4">
                <div class="project-image">
                    <img src="<?= htmlspecialchars($progetto['immagine'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="Immagine del progetto">
                </div>
            </div>
        </div>

        <!-- Sezione Dipartimenti -->
        <div class="department-block">
            <a class="btn btn-outline-primary btn-rounded"
               href="fiberglass_department.php?progetto_id=<?= $progetto_id ?>">Fiberglass Department</a>
            <a class="btn btn-outline-primary btn-rounded"
               href="outfitting_department.php?progetto_id=<?= $progetto_id ?>">Outfitting Department</a>
        </div>
    </div>
</div>


<!-- Footer -->
<footer class="bg-white text-black text-center py-3">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

</body>
</html>
