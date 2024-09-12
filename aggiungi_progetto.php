<?php
include 'navbar.php';
/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera gli ID dell'azienda e della linea di prodotto dalla query string
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Gestisce l'invio del form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cin = $_POST['cin'];
    $state = $_POST['state'];
    $delivery = $_POST['delivery'];

    // Gestisce l'upload dell'immagine
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["immagine"]["name"]);
    move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file);

    // Inserisce il nuovo progetto nel database
    $stmt = $conn->prepare("
        INSERT INTO progetti (azienda_id, linea_prodotto_id, cin, state, delivery, immagine)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $azienda_id, $linea_prodotto_id, $cin, $state, $delivery, $target_file);
    $stmt->execute();

    // Reindirizza alla pagina dei progetti
    header("Location: master_progetti.php?azienda_id=$azienda_id&linea_prodotto_id=$linea_prodotto_id");
    exit;
}
?>

<div class="container mt-5">
    <h2>Aggiungi Progetto</h2>
    <form action="aggiungi_progetto.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="cin" class="form-label">CIN:</label>
            <input type="text" class="form-control" id="cin" name="cin" required>
        </div>
        <div class="mb-3">
            <label for="state" class="form-label">STATE:</label>
            <input type="text" class="form-control" id="state" name="state" required>
        </div>
        <div class="mb-3">
            <label for="delivery" class="form-label">DELIVERY:</label>
            <input type="date" class="form-control" id="delivery" name="delivery" required>
        </div>
        <div class="mb-3">
            <label for="immagine" class="form-label">Immagine del Progetto:</label>
            <input type="file" class="form-control" id="immagine" name="immagine" required>
        </div>
        <button type="submit" class="btn btn-primary">Aggiungi Progetto</button>
    </form>
    <a href="master_progetti.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-secondary mt-3">Torna ai Progetti</a>
</div>

</body>
</html>
