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
        <h2 class="mb-4">Sostenibilità per il progetto <?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h2>

        <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>

        <!-- Tabella di Sostenibilità -->
        <table class="table table-bordered table-hover mt-4">
            <thead>
            <tr>
                <th>Element</th>
                <th>Description</th>
                <th>Productor</th>
                <th>Weight [kg]</th>
                <th>% in the total</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Gelcoat</td>
                <td>Surface finishing resin</td>
                <td>Euroresin - NEOGEL NPG 8373-W-9910</td>
                <td>260kg</td>
                <td>6,7%</td>
            </tr>
            <tr>
                <td>Fiberglass</td>
                <td>Reinforcement fiber</td>
                <td>VELOX</td>
                <td>1430kg</td>
                <td>36,9%</td>
            </tr>
            <tr>
                <td>Core</td>
                <td>Expanded PVC 80-100-130kg/m^3</td>
                <td>Polycell</td>
                <td>85 kg</td>
                <td>2,2%</td>
            </tr>
            <tr>
                <td>Marine plywood</td>
                <td>Anti-compression reinforcements</td>
                <td>Rossi legnami</td>
                <td>20 kg</td>
                <td>0,05%</td>
            </tr>
            <tr>
                <td>Brass - steel inserts</td>
                <td>Bolted coupling reinforcements</td>
                <td>Dugheria - Stilferro</td>
                <td>81 kg</td>
                <td>2,1%</td>
            </tr>
            <tr>
                <td>Vinylester resin</td>
                <td>Vinylester resin matrix</td>
                <td>Euroresin</td>
                <td>2000kg</td>
                <td>51,6%</td>
            </tr>
            </tbody>
        </table>

        <!-- Pulsante per tornare indietro -->
        <a href="dashboard_progetto.php?progetto_id=<?= $progetto_id ?>" class="btn btn-secondary mt-4">Torna alla Dashboard Progetto</a>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>

</body>
</html>
