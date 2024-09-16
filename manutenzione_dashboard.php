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

// Recupera i dettagli del progetto dal database
$stmt = $conn->prepare("
    SELECT p.cin, p.state, p.delivery, p.immagine,
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$progetto = $stmt->get_result()->fetch_assoc();
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];

// Recupera i dati di manutenzione
$man_stmt = $conn->prepare("
    SELECT * FROM manutenzione_progetti WHERE progetto_id = ?");
$man_stmt->bind_param("i", $progetto_id);
$man_stmt->execute();
$installazioni = $man_stmt->get_result();
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

    .details-block {
        border: 2px solid #27bcbc;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: white;
    }

    .table-btn {
        width: 100%;
        display: block;
        text-align: center;
    }

    .support-buttons {
        margin-top: 20px;
        text-align: center;
    }

    .support-buttons a {
        margin: 10px;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 16px;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <!-- Blocco Dettagli -->
        <div class="details-block">
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <!-- Tabella delle Installations -->
        <table class="table table-bordered table-hover mt-4">
            <thead>
            <tr>
                <th>Installations</th>
                <th>Status</th>
                <th>Next</th>
                <th>Register</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($installazione = $installazioni->fetch_assoc()): ?>
                <tr>
                    <td><a href="#"
                           class="btn btn-outline-primary table-btn"><?= htmlspecialchars($installazione['installation_name'], ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td><?= htmlspecialchars($installazione['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($installazione['next_check'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a href="<?= htmlspecialchars($installazione['checklist_url'], ENT_QUOTES, 'UTF-8') ?>"
                           class="btn btn-sm btn-primary">Checklist</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Sezione Supporto -->
        <div class="support-buttons">
            <a href="#" class="btn btn-outline-primary">Technical Drawing</a>
            <a href="#" class="btn btn-outline-primary">Print Status Checklist</a>
        </div>

        <!-- Pulsante Historical -->
        <a href="#" class="btn btn-secondary mt-4">Historical</a>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
