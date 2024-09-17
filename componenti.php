<?php
include 'navbar.php';

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è loggato
if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

// Recupera l'ID del progetto dalla query string
$progetto_id = $_GET['progetto_id'];

// Recupera il componente dalla query string
$componente = $_GET['componente'];

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
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2>Componenti per <?php echo ucfirst($componente); ?></h2><br>

        <!-- Lista dei componenti e sottocomponenti -->
        <ul class="list-group">
            <?php while ($comp = $componenti->fetch_assoc()): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($comp['nome'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <p><?= htmlspecialchars($comp['descrizione'], ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="checklist.php?componente_id=<?= $comp['id'] ?>&progetto_id=<?= $progetto_id ?>"
                       class="btn  btn-primary btn-rounded"><i class="fas fa-clipboard-check"></i>
                     Visualizza checklist</a>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- Pulsante per tornare indietro -->
        <a href="fiberglass_department.php?progetto_id=<?= $progetto_id ?>" class="btn btn-outline-primary mt-4 btn-rounded">Torna al Fiberglass Department</a>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
