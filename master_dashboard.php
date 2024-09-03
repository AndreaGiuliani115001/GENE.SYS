<?php
session_start();

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera i campi operativi
$campi_operativi = $conn->query("SELECT * FROM campi_operativi");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campo_operativo_id'])) {
    $campo_operativo_id = $_POST['campo_operativo_id'];
    $clienti = $conn->query("SELECT * FROM aziende WHERE campo_operativo_id = $campo_operativo_id");
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Master</title>
    <!-- Includi Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">GENE.SYS - Dashboard Master</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <h2 class="mb-4">Dashboard Master</h2>
    <form method="post" action="master_dashboard.php">
        <div class="mb-3">
            <label for="campo_operativo_id" class="form-label">Seleziona Campo Operativo:</label>
            <select name="campo_operativo_id" id="campo_operativo_id" class="form-select" required>
                <?php while ($row = $campi_operativi->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nome'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Visualizza Clienti</button>
    </form>

    <?php if (isset($clienti)): ?>
        <h3 class="mt-5">Clienti</h3>
        <ul class="list-group">
            <?php while ($row = $clienti->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= $row['nome'] ?>
                    <a href="master_linee_prodotti.php?azienda_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Visualizza Linee di Prodotto</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

<!-- Includi Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

