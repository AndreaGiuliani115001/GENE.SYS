<?php
include 'navbar.php';

/** @var mysqli $conn */
include 'connection.php';

if ($_SESSION['ruolo'] != 'master' || !is_null($_SESSION['azienda_id'])) {
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nome_campo_operativo'])) {
        // Inserimento di un nuovo campo operativo
        $nome_campo_operativo = $_POST['nome_campo_operativo'];
        $stmt = $conn->prepare("INSERT INTO campi_operativi (nome) VALUES (?)");
        $stmt->bind_param("s", $nome_campo_operativo);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Campo Operativo aggiunto con successo!</div>";
        } else {
            echo "<div class='alert alert-danger'>Errore durante l'inserimento del campo operativo.</div>";
        }
        $stmt->close();
    }

    if (isset($_POST['nome_azienda']) && isset($_POST['campo_operativo_id'])) {
        // Inserimento di una nuova azienda
        $nome_azienda = $_POST['nome_azienda'];
        $campo_operativo_id = $_POST['campo_operativo_id'];
        $stmt = $conn->prepare("INSERT INTO aziende (nome, campo_operativo_id) VALUES (?, ?)");
        $stmt->bind_param("si", $nome_azienda, $campo_operativo_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Azienda aggiunta con successo!</div>";
        } else {
            echo "<div class='alert alert-danger'>Errore durante l'inserimento dell'azienda.</div>";
        }
        $stmt->close();
    }
}

?>

<style>


    .logo-image {
        width: 100px;  /* Imposta la larghezza fissa */
        height: 100px; /* Imposta l'altezza fissa */
        object-fit: contain; /* Adatta l'immagine senza distorcerla */
        margin-bottom: 10px;
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

    function toggleForm(formId) {
        var form = document.getElementById(formId);
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
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

            <div class="d-flex">
                <button type="submit" class="btn btn-primary btn-rounded">
                    <i class="fas fa-user"></i> Visualizza Clienti
                </button>
                <div class="ms-auto">
                    <a href="modifica_campi_operativi.php" class="btn btn-warning btn-rounded">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="elimina_campi_operativi.php" class="btn btn-danger btn-rounded"">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            </div>
        </form>

        <?php if (isset($clienti) && $clienti->num_rows == 0): ?>
            <div class="alert alert-info mt-3">Nessuna azienda trovata per questo campo operativo.</div>
        <?php endif; ?>

        <?php if (isset($clienti)): ?>

            <div class="stat-box mt-5 shadow-sm">
                <i class="fas fa-building"></i>
                <h4><?= $aziende_count ?> Aziende</h4>
            </div>

            <div class="company-list">
                <?php while ($row = $clienti->fetch_assoc()): ?>
                    <div class="company-card">
                        <img src="<?= htmlspecialchars($row['logo'], ENT_QUOTES, 'UTF-8') ?>" class="logo-image mb-4" alt="Progetto">
                        <h5><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                        <a href="master_linee_prodotti.php?azienda_id=<?= $row['id'] ?>"
                           class="btn btn-primary btn-rounded">
                            <i class="fas fa-stream"></i> Linee di Prodotto
                        </a><br>
                        <a href="modifica_azienda.php?azienda_id=<?= $row['id'] ?>" class="btn btn-warning btn-rounded">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="elimina_azienda.php?azienda_id=<?= $row['id'] ?>" class="btn btn-danger btn-rounded"
                           onclick="return confirm('Sei sicuro di voler eliminare questa azienda?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-end mb-2 mt-4">
            <button class="btn btn-primary me-2 btn-rounded" onclick="toggleForm('campoOperativoForm')">
                <i class="fas fa-plus"></i> <i class="fas fa-stream"></i>
            </button>
            <button class="btn btn-primary  btn-rounded" onclick="toggleForm('aziendaForm')">
                <i class="fas fa-plus"></i> <i class="fas fa-building"></i>
            </button>
        </div>

        <div id="campoOperativoForm" style="display: none;">
            <h4>Inserisci un Nuovo Campo Operativo</h4>
            <form method="post" action="master_dashboard.php">
                <div class="mb-3">
                    <label for="nome_campo_operativo" class="form-label">Nome Campo Operativo:</label>
                    <input type="text" name="nome_campo_operativo" id="nome_campo_operativo" class="form-control"
                           required>
                </div>
                <button type="submit" class="btn btn-primary btn-rounded">Aggiungi</button>
            </form>
        </div>
        <div id="aziendaForm" style="display: none;">
            <h4>Inserisci una Nuova Azienda</h4>
            <form method="post" action="master_dashboard.php">
                <div class="mb-3">
                    <label for="nome_azienda" class="form-label">Nome Azienda:</label>
                    <input type="text" name="nome_azienda" id="nome_azienda" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="campo_operativo_id" class="form-label">Campo Operativo:</label>
                    <select name="campo_operativo_id" id="campo_operativo_id" class="form-select" required>
                        <?php // Recupera i campi operativi all'inizio per il primo select
                        $campi_operativi = $conn->query("SELECT * FROM campi_operativi");
                        while ($row = $campi_operativi->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Aggiungi</button>
            </form>
        </div>


    </div>


    <footer class="text-center text-black bg-white mt-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>
