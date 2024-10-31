<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è un Master (admin globale o admin aziendale)
if ($_SESSION['ruolo'] != 'master' || (!is_null($_SESSION['azienda_id']) && $_GET['azienda_id'] != $_SESSION['azienda_id'])) {
    header("Location: login.php");
    exit;
}


$azienda_id = $_GET['azienda_id'];

// Recupera il cliente e le sue linee di prodotto
$cliente = $conn->query("SELECT * FROM aziende WHERE id = $azienda_id")->fetch_assoc();
$linee_prodotti = $conn->query("SELECT * FROM linee_prodotti WHERE azienda_id = $azienda_id ORDER BY CAST(SUBSTRING_INDEX(nome, ' ', -1) AS DECIMAL(10, 2)) ASC");
$count_linee = $conn->query("SELECT COUNT(*) as totale_linee FROM linee_prodotti WHERE azienda_id = $azienda_id")->fetch_assoc()['totale_linee'];



//inserimento di una nuova linea di prodotto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome_linea'])) {
    $nome_linea = $_POST['nome_linea'];
    $stmt = $conn->prepare("INSERT INTO linee_prodotti (nome, azienda_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nome_linea, $azienda_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Linea di prodotto aggiunta con successo!</div>";
    } else {
        echo "<div class='alert alert-danger'>Errore nell'aggiunta della linea di prodotto.</div>";
    }
}


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

    .stat-box {
        background-color: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        text-align: center;
    }

    .stat-box i {
        font-size: 36px;
        color: #27bcbc;
        margin-bottom: 10px;
    }

    .stat-box h4 {
        font-size: 24px;
        margin-bottom: 0;
    }

</style>

<div class="full-screen-container">
    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="mb-4">Linee di Prodotto per <?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?></h2>

        <?php if (isset($linee_prodotti) && $linee_prodotti->num_rows == 0): ?>
            <div class="alert alert-info mt-3">Nessuna linea di prodotto trovata per questa azienda.</div>
        <?php endif; ?>
        <!-- Blocco per il conteggio totale delle linee di prodotto -->
        <div class="stat-box mt-5 shadow-sm">
            <i class="fas fa-stream"></i>
            <h4><?= $count_linee ?> Linee di prodotto</h4>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <!-- Lista delle linee di prodotto -->
        <div class="product-list">

            <?php while ($row = $linee_prodotti->fetch_assoc()): ?>
                <div class="product-card">
                    <h5><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <a href="master_progetti.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $row['id'] ?>"
                       class="btn btn-outline-primary btn-rounded"><i class="fas fa-folder"></i> Progetti</a><br>
                    <a href="modifica_linea_prodotto.php?linea_prodotto_id=<?= $row['id'] ?>&azienda_id=<?= $azienda_id ?>"
                       class="btn btn-warning btn-rounded">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="elimina_linea_prodotto.php?linea_prodotto_id=<?= $row['id'] ?>&azienda_id=<?= $azienda_id ?>"
                       class="btn btn-danger btn-rounded"
                       onclick="return confirm('Sei sicuro di voler eliminare questa linea di prodotto?')">
                        <i class="fas fa-trash-alt"></i>
                    </a>

                </div>
            <?php endwhile; ?>
        </div>

        <div class="d-flex justify-content-between mt-3 mb-4">

            <?php if (is_null($_SESSION['azienda_id'])): ?>
                <!-- Pulsante per tornare alla dashboard visibile solo per admin globale (pieni permessi) -->
                <a href="master_dashboard.php" class="btn btn-primary btn-rounded ">
                    <i class="fas fa-arrow-left"></i>
                </a>
            <?php endif; ?>
            <!-- Bottone per visualizzare il form -->
            <button id="toggleFormButton" class="btn btn-primary btn-rounded">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <!-- Form per inserire una nuova linea di prodotto, nascosto di default -->
        <form id="newLineForm" method="POST" action="master_linee_prodotti.php?azienda_id=<?= $azienda_id ?>" style="display: none;">
            <div class="mb-3">
                <label for="nome_linea" class="form-label">Nuova Linea di Prodotto:</label>
                <input type="text" name="nome_linea" id="nome_linea" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-rounded btn-primary">Aggiungi Linea di Prodotto</button>
        </form>


    </div>

    <!-- Footer -->
    <footer class="text-center text-black bg-white mt-4">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

<script>
    document.getElementById('toggleFormButton').addEventListener('click', function() {
        var form = document.getElementById('newLineForm');
        if (form.style.display === 'none') {
            form.style.display = 'block'; // Mostra il form
        } else {
            form.style.display = 'none'; // Nascondi il form
        }
    });
</script>

