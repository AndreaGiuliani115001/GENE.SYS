<?php
include 'navbar.php';
include 'connection.php';

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master' || !is_null($_SESSION['azienda_id'])) {
    header("Location: login.php");
    exit;
}

// Recupera i dettagli dell'azienda
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['azienda_id'])) {
    $azienda_id = $_GET['azienda_id'];
    $stmt = $conn->prepare("SELECT * FROM aziende WHERE id = ?");
    $stmt->bind_param("i", $azienda_id);
    $stmt->execute();
    $azienda = $stmt->get_result()->fetch_assoc();

    // Verifica se l'azienda esiste
    if (!$azienda) {
        echo "<div class='alert alert-danger'>Azienda non trovata.</div>";
        exit;
    }
}

// Aggiorna i dettagli dell'azienda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['azienda_id'])) {
    $azienda_id = $_POST['azienda_id'];
    $nome_azienda = $_POST['nome_azienda'];
    $campo_operativo_id = $_POST['campo_operativo_id'];

    $stmt = $conn->prepare("UPDATE aziende SET nome = ?, campo_operativo_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $nome_azienda, $campo_operativo_id, $azienda_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Azienda aggiornata con successo!</div>";
        header("Location: master_dashboard.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Errore durante l'aggiornamento.</div>";
    }
}
?>

<style>
    .full-screen-container {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .content-container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-grow: 1;
    }

    .form-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 500px;
        width: 100%;
    }

    h3 {
        font-weight: bold;
        color: #000 !important;
    }

    .btn-primary, .btn-success, .btn-secondary {
        border-radius: 20px;
    }

    .btn-primary:hover, .btn-success:hover, .btn-secondary:hover {
        opacity: 0.8;
    }

    .form-label {
        color: #000 !important;
    }

    .alert {
        margin-top: 20px;
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
    <div class="content-container">
        <div class="form-container">
            <h3 class="text-center">Modifica Azienda</h3>
            <form method="post" action="modifica_azienda.php">
                <input type="hidden" name="azienda_id"
                       value="<?= htmlspecialchars($azienda['id'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="mb-3">
                    <label for="nome_azienda" class="form-label">Nome Azienda:</label>
                    <input type="text" name="nome_azienda" id="nome_azienda" class="form-control"
                           value="<?= htmlspecialchars($azienda['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="campo_operativo_id" class="form-label">Campo Operativo:</label>
                    <select name="campo_operativo_id" id="campo_operativo_id" class="form-select" required>
                        <?php
                        // Recupera i campi operativi per la select
                        $campi_operativi = $conn->query("SELECT * FROM campi_operativi");
                        while ($row = $campi_operativi->fetch_assoc()):
                            ?>
                            <option value="<?= $row['id'] ?>"
                                <?= $row['id'] == $azienda['campo_operativo_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="master_dashboard.php" class="btn btn-secondary me-2">
                        <i class="fas fa-times"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</body>
</html>
