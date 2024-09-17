<?php
include 'navbar.php';

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è loggato e ha il ruolo di master
if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera l'azienda e la linea di prodotto dalla query string
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Verifica se l'azienda è nel settore navale
$settore_stmt = $conn->prepare("
    SELECT co.nome as campo_operativo 
    FROM aziende a 
    JOIN campi_operativi co ON a.campo_operativo_id = co.id 
    WHERE a.id = ?");
$settore_stmt->bind_param("i", $azienda_id);
$settore_stmt->execute();
$settore = $settore_stmt->get_result()->fetch_assoc();

if ($settore['campo_operativo'] != 'Navale') {
    die("L'azienda selezionata non opera nel settore navale.");
}

// Gestione della creazione del nuovo progetto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cin = $_POST['cin'];
    $state = $_POST['state'];
    $delivery = $_POST['delivery'];
    $immagine = $_FILES['immagine']['name'];

    // Carica l'immagine del progetto
    if (!empty($immagine)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($immagine);
        move_uploaded_file($_FILES['immagine']['tmp_name'], $target_file);
    }

    // Inserisci il progetto nella tabella progetti
    $stmt = $conn->prepare("
        INSERT INTO progetti (azienda_id, linea_prodotto_id, cin, state, delivery, immagine) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $azienda_id, $linea_prodotto_id, $cin, $state, $delivery, $target_file);
    $stmt->execute();

    $progetto_id = $stmt->insert_id;

    // Collega i componenti predefiniti (scafo, ragno, coperta, celini, secondari) al progetto
    $componenti = [1, 2, 3, 4, 5]; // Id dei componenti (scafo, ragno, coperta, celini, secondari)
    foreach ($componenti as $componente_id) {
        $componente_stmt = $conn->prepare("
            INSERT INTO progetti_componenti (progetto_id, componente_id) 
            VALUES (?, ?)");
        $componente_stmt->bind_param("ii", $progetto_id, $componente_id);
        $componente_stmt->execute();
    }

    // Collega le checklist predefinite ai componenti
    foreach ($componenti as $componente_id) {
        $checklist_stmt = $conn->prepare("
            INSERT INTO checklist (nome, descrizione, componente_id, progetto_id) 
            VALUES (?, ?, ?, ?)");
        $nome_checklist = "Checklist " . $componente_id;
        $descrizione_checklist = "Checklist per il componente " . $componente_id;
        $checklist_stmt->bind_param("ssii", $nome_checklist, $descrizione_checklist, $componente_id, $progetto_id);
        $checklist_stmt->execute();
    }

    // Reindirizza alla pagina di visualizzazione dei progetti
    header("Location: master_progetti.php?azienda_id=$azienda_id&linea_prodotto_id=$linea_prodotto_id");
    exit;
}
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

    .container {
        flex-grow: 1;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    .btn-rounded {
        border-radius: 50px;
    }
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2>Crea un nuovo progetto per l'azienda</h2>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="cin" class="form-label">CIN</label>
                <input type="text" class="form-control" id="cin" name="cin" required>
            </div>
            <div class="mb-3">
                <label for="state" class="form-label">Stato</label>
                <input type="text" class="form-control" id="state" name="state" required>
            </div>
            <div class="mb-3">
                <label for="delivery" class="form-label">Data di consegna</label>
                <input type="date" class="form-control" id="delivery" name="delivery" required>
            </div>
            <div class="mb-3">
                <label for="immagine" class="form-label">Immagine del progetto</label>
                <input type="file" class="form-control" id="immagine" name="immagine">
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-rounded">Crea Progetto</button>
        </form>
    </div>

    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>
