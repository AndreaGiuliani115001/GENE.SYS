<?php
include 'navbar.php';
include('connection.php');

// Recupera i parametri dalla query string
$macro_id = $_GET['macro_id'];
$progetto_id = $_GET['progetto_id'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera il macro_id dalla query string
$macro_id = $_GET['macro_id'];

// Query per ottenere le attività specifiche del progetto e della macro-categoria
$query = "
    SELECT a.id, a.nome 
    FROM attivita a
    JOIN progetti_attivita pa ON a.id = pa.attivita_id
    WHERE a.macro_categoria_id = ? AND pa.progetto_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $macro_id, $progetto_id);
$stmt->execute();
$result = $stmt->get_result();

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

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 my-2">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <a href="checklist.php?attivita_id=<?= $row['id'] ?>&progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>&tipo=manutenzione" class="btn btn-primary btn-rounded">
                                <i class="fas fa-clipboard-list"></i> Vedi Checklist
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
