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

// Recupera il componente principale e i suoi sottocomponenti per il progetto specifico
$componente_stmt = $conn->prepare("
    SELECT c.id AS componente_id, c.nome AS componente_nome, c.descrizione AS componente_descrizione
    FROM componenti c
    JOIN componente_progetto cp ON c.id = cp.componente_id
    WHERE cp.progetto_id = ? AND (c.id = ? OR c.parent_id = ?)
");
$componente_stmt->bind_param("iii", $progetto_id, $parent_id, $parent_id);
$componente_stmt->execute();
$componenti = $componente_stmt->get_result();
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

    .component-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .checklist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }

    .checklist-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 10px;
        text-align: center;
        margin: 15px;

    }

    .checklist-card h6 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .component-header {
        background-color: #27bcbc;
        color: white;
        padding: 15px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        margin: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .plus-icon {
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
    }


    .alert-info {
        padding: 10px;
    }

    .dashboard-icon {
        font-size: 30px;
        color: #27bcbc;
        margin-bottom: 5px;
    }
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <h2 class="mb-4">Checklist per <?= ucfirst(htmlspecialchars($componente, ENT_QUOTES, 'UTF-8')) ?></h2>

        <?php while ($comp = $componenti->fetch_assoc()): ?>
            <div class="component-card">
                <h4 class="component-header"><?= htmlspecialchars($comp['componente_nome'], ENT_QUOTES, 'UTF-8') ?>
                    <a href="aggiungi_checklist.php?componente_id=<?= $componente_id ?>&componente=<?= $comp['componente_nome'] ?>&progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                       class="btn btn-primary btn-rounded"><i class="fas fa-plus"></i></a>
                </h4>

                <?php
                // Recupera le checklist specifiche per questo progetto e componente
                $componente_id = $comp['componente_id'];
                $checklist_stmt = $conn->prepare("
                    SELECT cl.id AS checklist_id, cl.nome AS checklist_nome, cl.descrizione AS checklist_descrizione
                    FROM checklist cl
                    JOIN checklist_componente_progetto ccp ON cl.id = ccp.checklist_id
                    JOIN componente_progetto cp ON ccp.componente_progetto_id = cp.id
                    WHERE cp.progetto_id = ? AND cp.componente_id = ?
                ");
                $checklist_stmt->bind_param("ii", $progetto_id, $componente_id);
                $checklist_stmt->execute();
                $checklists = $checklist_stmt->get_result();
                ?>

                <?php if ($checklists->num_rows > 0): ?>
                    <div class="checklist-grid">
                        <?php while ($checklist = $checklists->fetch_assoc()): ?>
                            <div class="checklist-card">
                                <div class="dashboard-icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <h6><?= htmlspecialchars($checklist['checklist_nome'], ENT_QUOTES, 'UTF-8') ?></h6>
                                <div class="btn-group">
                                    <a href="checklist.php?checklist_id=<?= $checklist['checklist_id'] ?>&componente_id=<?= $componente_id ?>&componente=<?= $comp['componente_nome'] ?>&progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                                       class="btn btn-primary btn-sm btn-rounded"><i class="fas fa-eye"></i></a>
                                    <a href="modifica_macro.php?id=<?= $macro['id'] ?>"
                                       class="btn btn-warning btn-sm btn-rounded" title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="elimina_macro.php?id=<?= $macro['id'] ?>"
                                       class="btn btn-danger btn-sm btn-rounded" title="Elimina"
                                       onclick="return confirm('Sei sicuro di voler eliminare questa macro-categoria?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="alert-info text-center">Nessuna checklist disponibile per questo componente.</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <a href="fiberglass_department.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
           class="btn btn-primary btn-rounded"><i class="fas fa-arrow-left"></i></a>
    </div>

    <!-- Footer -->
    <footer class="text-center text-black bg-white py-4">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>

