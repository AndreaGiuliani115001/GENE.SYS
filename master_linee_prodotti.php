<?php
session_start();

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

$azienda_id = $_GET['azienda_id'];

// Recupera il cliente e le sue linee di prodotto
$cliente = $conn->query("SELECT * FROM aziende WHERE id = $azienda_id")->fetch_assoc();
$linee_prodotti = $conn->query("SELECT * FROM linee_prodotti WHERE azienda_id = $azienda_id");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linee di Prodotto - <?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?></title>
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

<!-- Main Content -->
<div class="container mt-5">
    <h2 class="mb-4">Linee di Prodotto per <?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
    <ul class="list-group">
        <?php while ($row = $linee_prodotti->fetch_assoc()): ?>
            <li class="list-group-item">
                <?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>
                <a href="master_progetti.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm float-end">Visualizza Progetti</a>
            </li>
        <?php endwhile; ?>
    </ul>
    <a href="master_dashboard.php" class="btn btn-secondary mt-4">Torna alla Dashboard Master</a>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

<!-- Includi Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
