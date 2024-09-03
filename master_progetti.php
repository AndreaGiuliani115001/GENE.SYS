<?php
session_start();

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera gli ID dell'azienda e della linea di prodotto selezionati
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Recupera i progetti associati all'azienda e alla linea di prodotto selezionate
$stmt = $conn->prepare("
    SELECT p.cin, p.state, p.delivery, p.immagine, 
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.azienda_id = ? AND p.linea_prodotto_id = ?");
$stmt->bind_param("ii", $azienda_id, $linea_prodotto_id);
$stmt->execute();
$result = $stmt->get_result();

// Se non ci sono progetti, mostra un messaggio
if ($result->num_rows === 0) {
    die("Nessun progetto trovato per questa linea di prodotto.");
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progetti dell'Azienda</title>
    <!-- Includi Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">GENE.SYS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="master_dashboard.php">Dashboard Master</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <h2>Progetti dell'Azienda</h2>
    <div class="row">
        <?php while ($progetto = $result->fetch_assoc()):
            $nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];
            ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="<?= htmlspecialchars($progetto['immagine'], ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="Progetto">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="card-text"><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="card-text"><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="card-text"><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

<!-- Includi Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
