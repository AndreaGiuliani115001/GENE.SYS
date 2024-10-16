<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è un admin globale o admin aziendale
if ($_SESSION['ruolo'] != 'master' || (!is_null($_SESSION['azienda_id']) && $_GET['azienda_id'] != $_SESSION['azienda_id'])) {
    header("Location: login.php");
    exit;
}

// Recupera i dati della linea di prodotto
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['linea_prodotto_id']) && isset($_GET['azienda_id'])) {
    $linea_prodotto_id = $_GET['linea_prodotto_id'];
    $azienda_id = $_GET['azienda_id'];

    $stmt = $conn->prepare("SELECT * FROM linee_prodotti WHERE id = ? AND azienda_id = ?");
    $stmt->bind_param("ii", $linea_prodotto_id, $azienda_id);
    $stmt->execute();
    $linea_prodotto = $stmt->get_result()->fetch_assoc();

    if (!$linea_prodotto) {
        echo "<div class='alert alert-danger'>Linea di prodotto non trovata.</div>";
        exit;
    }
}

// Gestione della modifica della linea di prodotto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome_linea']) && isset($_POST['azienda_id'])) {
    $linea_prodotto_id = $_POST['linea_prodotto_id'];
    $nome_linea = $_POST['nome_linea'];
    $azienda_id = $_POST['azienda_id'];

    // Query per aggiornare la linea di prodotto
    $stmt = $conn->prepare("UPDATE linee_prodotti SET nome = ? WHERE id = ? AND azienda_id = ?");
    $stmt->bind_param("sii", $nome_linea, $linea_prodotto_id, $azienda_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Linea di prodotto modificata con successo.";
        header("Location: master_linee_prodotti.php?azienda_id=$azienda_id");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Errore durante la modifica della linea di prodotto.</div>";
    }

}
?>

<div class="container mt-5">
    <h2 class="mb-4">Modifica Linea di Prodotto</h2>

    <form method="POST" action="modifica_linea_prodotto.php">
        <input type="hidden" name="linea_prodotto_id" value="<?= $linea_prodotto['id'] ?>">
        <input type="hidden" name="azienda_id" value="<?= $azienda_id ?>">

        <div class="mb-3">
            <label for="nome_linea" class="form-label">Nome Linea di Prodotto:</label>
            <input type="text" name="nome_linea" id="nome_linea" class="form-control"
                   value="<?= htmlspecialchars($linea_prodotto['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salva Modifiche</button>
        <a href="master_linee_prodotti.php?azienda_id=<?= $azienda_id ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a>
    </form>


</div>
</body>
</html>

