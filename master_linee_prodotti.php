<?php
include 'navbar.php';

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

<style>
    /* Imposta altezza e larghezza del 100% su html e body */
    html, body {
        height: 100%;
        margin: 0;
    }

    /* Imposta il contenitore principale per occupare tutto lo schermo */
    .full-screen-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Distribuisce il contenuto tra header e footer */
        min-height: 100vh; /* Occupazione dell'intero viewport */
    }

    .container {
        flex-grow: 1; /* Permette al contenitore di crescere e riempire lo spazio disponibile */
    }

    /* Stile per il footer per mantenerlo in fondo alla pagina */
    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }
</style>

<div class="full-screen-container">
    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="mb-4">Linee di Prodotto per <?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
        <ul class="list-group">
            <?php while ($row = $linee_prodotti->fetch_assoc()): ?>
                <li class="list-group-item">
                    <?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>
                    <a href="master_progetti.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm float-end btn-rounded"><i class="fas fa-folder"></i>
                         Progetti</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
