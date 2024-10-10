<?php
include 'navbar.php';
include('connection.php');

// Recupera i parametri dalla query string
$progetto_id = $_GET['progetto_id'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera il campo operativo dell'azienda
$stmt = $conn->prepare("
    SELECT a.campo_operativo_id 
    FROM aziende a 
    JOIN progetti p ON a.id = p.azienda_id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$result = $stmt->get_result();
$campo_operativo = $result->fetch_assoc()['campo_operativo_id'];

// Query per ottenere le macro-categorie specifiche per il campo operativo
$query = "SELECT id, nome FROM macro_categorie WHERE campo_operativo_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $campo_operativo);
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
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 my-2">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                            <a href="storico_interventi.php?macro_id=<?= $row['id'] ?>&progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-outline-primary btn-rounded mx-1">
                                <i class="fas fa-history"></i> Storico Interventi
                            </a>
                            <a href="attivita.php?macro_id=<?= $row['id'] ?>&progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-primary btn-rounded mx-1">
                                <i class="fas fa-tasks"></i> Vedi Attività
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

</body>
</html>
