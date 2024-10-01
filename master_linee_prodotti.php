<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

$azienda_id = $_GET['azienda_id'];

// Recupera il cliente e le sue linee di prodotto
$cliente = $conn->query("SELECT * FROM aziende WHERE id = $azienda_id")->fetch_assoc();
$linee_prodotti = $conn->query("SELECT * FROM linee_prodotti WHERE azienda_id = $azienda_id ORDER BY nome ASC");

?>

<style>
    /* Stile per il contenitore principale a schermo intero */
    .full-screen-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100vh;
    }

    .container {
        flex-grow: 1;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    /* Lista delle linee di prodotto */
    .product-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        grid-gap: 20px;
        margin-top: 20px;
    }

    .product-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
    }

    .product-card h5 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .product-card .btn {
        margin-top: 10px;
    }

</style>

<div class="full-screen-container">
    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="mb-4">Linee di Prodotto per <?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?></h2>

        <!-- Lista delle linee di prodotto -->
        <div class="product-list">
            <?php while ($row = $linee_prodotti->fetch_assoc()): ?>
                <div class="product-card">
                    <h5><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <a href="master_progetti.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $row['id'] ?>"
                       class="btn btn-primary btn-rounded"><i class="fas fa-folder"></i> Progetti</a>
                </div>
            <?php endwhile; ?>
        </div>
        <!-- Pulsante per tornare alla dashboard -->
        <a href="master_dashboard.php" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-left"></i> Torna alla Dashboard</a>

    </div>

    <!-- Footer -->
    <footer class="text-center text-black bg-white mt-4">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
