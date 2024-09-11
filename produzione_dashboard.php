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

        .container {
            flex-grow: 1;
        }

        .project-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .details-block {
            text-align: left;
            border: 2px solid #000;
            padding: 20px;
            border-radius: 8px;
            width: 30%;
        }

        .project-image img {
            width: 100%;
            height: auto;
            max-width: 600px;  /* Limita la larghezza dell'immagine */
        }

        .department-block {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }

        .department-block a {
            text-align: center;
            padding: 20px;
            width: 45%;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 20px;
        }
    </style>

<div class="full-screen-container">
    <div class="container mt-5">
        <div class="project-section">
            <!-- Dettagli del progetto -->
            <div class="details-block">
                <h3><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h3>
                <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>

            <!-- Immagine del progetto -->
            <div class="project-image">
                <img src="<?= htmlspecialchars($progetto['immagine'], ENT_QUOTES, 'UTF-8') ?>" alt="Immagine del progetto">
            </div>
        </div>

        <!-- Sezione Dipartimenti -->
        <div class="department-block">
            <a class="btn btn-outline-primary btn-rounded" href="fiberglass_dashboard.php?progetto_id=<?= $progetto_id ?>">Fiberglass Department</a>
            <a class="btn btn-outline-primary btn-rounded" href="outfitting_dashboard.php?progetto_id=<?= $progetto_id ?>">Outfitting Department</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
