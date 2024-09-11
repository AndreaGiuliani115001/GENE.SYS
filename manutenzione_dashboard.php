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
    SELECT p.cin, p.state, p.delivery, 
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$result = $stmt->get_result();

// Se il progetto non viene trovato
if ($result->num_rows === 0) {
    die("Progetto non trovato.");
}

$progetto = $result->fetch_assoc();
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];
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
    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="mb-4">Manutenzione per il progetto <?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h2>

        <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>

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
            <tr>
                <td>Transmission</td>
                <td>ok</td>
                <td>227h</td>
                <td><a href="#" class="btn btn-sm btn-info">Checklist</a></td>
            </tr>
            <tr>
                <td>Engine Room</td>
                <td>ok</td>
                <td>136h</td>
                <td><a href="#" class="btn btn-sm btn-info">Checklist</a></td>
            </tr>
            <tr>
                <td>Hull Antifouling</td>
                <td>not work</td>
                <td>0 d</td>
                <td><a href="#" class="btn btn-sm btn-info">Checklist</a></td>
            </tr>
            <tr>
                <td>Plant Room</td>
                <td>update available</td>
                <td>3 y</td>
                <td><a href="#" class="btn btn-sm btn-info">Checklist</a></td>
            </tr>
            </tbody>
        </table>

        <!-- Pulsante per tornare indietro -->
        <a href="dashboard_progetto.php?progetto_id=<?= $progetto_id ?>" class="btn btn-secondary mt-4">Torna alla
            Dashboard Progetto</a>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>


</body>
</html>
