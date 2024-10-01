<?php
include 'navbar.php';
/** @var mysqli $conn */
include 'connection.php';

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
    $aziende_count = $conn->query("SELECT COUNT(*) as totale_aziende FROM aziende WHERE campo_operativo_id = $campo_operativo_id")->fetch_assoc()['totale_aziende'];
}



?>

<style>
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

    /* Griglia Responsive */
    .company-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        grid-gap: 20px;
        margin-top: 20px;
    }

    .company-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
    }

    .company-card h5 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .company-card .btn {
        margin-top: 10px;
    }

</style>

<script>
    // Effetto Navbar scrolled
    window.addEventListener('scroll', function () {
        var navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>

<div class="full-screen-container">
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
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-user"></i> Visualizza Clienti</button>
        </form>

        <?php if (isset($clienti)): ?>

            <div class="stat-box mt-5 shadow-sm">
                <i class="fas fa-building"></i>
                <h4><?= $aziende_count ?> Aziende</h4>
            </div>

            <div class="company-list">
                <?php while ($row = $clienti->fetch_assoc()): ?>
                    <div class="company-card">
                        <h5><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                        <a href="master_linee_prodotti.php?azienda_id=<?= $row['id'] ?>" class="btn btn-primary btn-rounded">
                            <i class="fas fa-stream"></i> Linee di Prodotto
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="text-center text-black bg-white mt-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
