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

// Aggiorna i campi operativi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campi_operativi'])) {
    foreach ($_POST['campi_operativi'] as $id => $nome) {
        $stmt = $conn->prepare("UPDATE campi_operativi SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $id);
        $stmt->execute();
    }
    $_SESSION['success'] = "Campi operativi aggiornati con successo.";
    header("Location: master_dashboard.php");
    exit;
}
?>

<div class="container mt-5">
    <h2>Modifica Campi Operativi</h2>
    <form method="post" action="modifica_campi_operativi.php">
        <?php while ($row = $campi_operativi->fetch_assoc()): ?>
            <div class="mb-3">
                <label for="campo_operativo_<?= $row['id'] ?>" class="form-label">Nome Campo Operativo:</label>
                <input type="text" name="campi_operativi[<?= $row['id'] ?>]" id="campo_operativo_<?= $row['id'] ?>"
                       class="form-control" value="<?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
        <?php endwhile; ?>
        <button type="submit" class="btn btn-primary btn-rounded"><i class="fas fa-save"></i> Salva modifiche</button>
        <a href="master_dashboard.php" class="btn btn-secondary btn-rounded"><i class="fas fa-times"></i></a>
    </form>
</div>
