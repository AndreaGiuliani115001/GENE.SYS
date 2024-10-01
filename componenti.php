<?php
include 'navbar.php';
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera i parametri dalla query string
$progetto_id = $_GET['progetto_id'];
$componente = $_GET['componente'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Imposta l'id_componente di base in base al componente selezionato
if ($componente == 'scafo') {
    $parent_id = 1; // ID del componente 'Scafo'
} elseif ($componente == 'coperta') {
    $parent_id = 2; // ID del componente 'Coperta'
} elseif ($componente == 'secondari') {
    $parent_id = 3; // ID del componente 'Secondari'
} else {
    die("Componente non valido.");
}

// Recupera i componenti principali e i sottocomponenti filtrati per progetto
$componente_principale_stmt = $conn->prepare("
    SELECT c.id, c.nome, c.descrizione 
    FROM componenti c
    JOIN progetti_componenti pc ON c.id = pc.componente_id
    WHERE pc.progetto_id = ? AND (c.id = ? OR c.parent_id = ?)
");
$componente_principale_stmt->bind_param("iii", $progetto_id, $parent_id, $parent_id);
$componente_principale_stmt->execute();
$componenti = $componente_principale_stmt->get_result();
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

    /* Griglia per i componenti */
    .component-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        grid-gap: 20px;
        margin-top: 20px;
    }

    .component-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
    }

    .component-card h5 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .component-card p {
        font-size: 16px;
        margin-bottom: 15px;
    }

    .component-card .btn {
        margin-top: 10px;
    }

</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2 class="mb-4">Componenti per <?= ucfirst(htmlspecialchars($componente, ENT_QUOTES, 'UTF-8')) ?></h2>

        <!-- Griglia dei componenti e sottocomponenti -->
        <div class="component-list">
            <?php while ($comp = $componenti->fetch_assoc()): ?>
                <div class="component-card">
                    <h5><?= htmlspecialchars($comp['nome'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <p><?= htmlspecialchars($comp['descrizione'], ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="checklist.php?componente_id=<?= $comp['id'] ?>&componente=<?= $componente ?>&progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                       class="btn btn-primary btn-rounded"><i class="fas fa-clipboard-check"></i>  Visualizza checklist</a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pulsante per tornare alla dashboard di produzione -->
        <a href="fiberglass_department.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
           class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-left"></i> Torna alla Dashboard Produzione</a>
    </div>

    <!-- Footer -->
    <footer class="text-center text-black bg-white">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
