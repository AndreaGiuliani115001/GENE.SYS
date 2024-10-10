<?php
include 'navbar.php';
include 'connection.php';

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master' || !is_null($_SESSION['azienda_id'])) {
    header("Location: login.php");
    exit;
}

// Recupera i campi operativi
$campi_operativi = $conn->query("SELECT * FROM campi_operativi");

// Elimina i campi operativi selezionati
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campi_operativi'])) {
    $ids = implode(',', array_map('intval', $_POST['campi_operativi']));
    $stmt = $conn->prepare("DELETE FROM campi_operativi WHERE id IN ($ids)");
    if ($stmt->execute()) {
        $_SESSION['success'] = "Campi operativi eliminati con successo.";
        header("Location: master_dashboard.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Errore durante l'eliminazione dei campi operativi.</div>";
    }
}
?>

<div class="container mt-5">
    <h2>Elimina Campi Operativi</h2>
    <form method="post" action="elimina_campi_operativi.php">
        <?php while ($row = $campi_operativi->fetch_assoc()): ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="campi_operativi[]" value="<?= $row['id'] ?>" id="campo_operativo_<?= $row['id'] ?>">
                <label class="form-check-label" for="campo_operativo_<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>
                </label>
            </div>
        <?php endwhile; ?>
        <button type="submit" class="btn btn-danger mt-3">Elimina Selezionati</button>
        <a href="master_dashboard.php" class="btn btn-secondary mt-3">Annulla</a>
    </form>
</div>

