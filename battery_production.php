<?php
include 'navbar.php';
include('connection.php');

if (!isset($_SESSION['ruolo'])) {
    header("Location: login.php");
    exit;
}

$progetto_id = $_GET['progetto_id'];
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Recupera i dettagli del progetto e il campo operativo dal database
$stmt = $conn->prepare("
    SELECT p.cin, p.stato, p.consegna, p.immagine,
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           a.campo_operativo_id, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Progetto non trovato.");
}

$progetto = $result->fetch_assoc();
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];
$campo_operativo_id = $progetto['campo_operativo_id'];
?>

<style>
    .details-block{
        border-radius: 8px;
        background-color: white;
        min-height: 300px; /* Imposta la stessa altezza per il details block e le schede */
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        border: none;
    }
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <div class="details-block shadow-sm">
            <h3><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['stato'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['consegna'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <img src="uploads/ProcessoBatterie.png" alt="Production Image" class="img-fluid mt-3 mb-4" style="width: 100%; height: auto;">

        <!-- Tabella per le checklist di produzione -->
        <table class="table table-bordered text-center">
            <tbody>
            <!-- Electrode Production -->
            <tr>
                <th class="bg-light" colspan="2">Electrode Production</th>
            </tr>
            <tr>
                <td>Mixing</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Coating</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Drying</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Calendering</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Slitting</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Vacuum Drying</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>

            <!-- Cell Assembly -->
            <tr>
                <th class="bg-light" colspan="2">Cell Assembly</th>
            </tr>
            <tr>
                <td>Cutting</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Stacking/Winding</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Contacting</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Packaging</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Electrolyte Filling</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>

            <!-- Cell Finishing -->
            <tr>
                <th class="bg-light" colspan="2">Cell Finishing</th>
            </tr>
            <tr>
                <td>Pre-treatment</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Formation</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Degassing</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            <tr>
                <td>Aging</td>
                <td><a href="#" class="btn btn-outline-primary btn-sm"><i class="fas fa-tasks"></i></a></td>
            </tr>
            </tbody>
        </table>
        <a href="produzione_dashboard.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-primary btn-rounded">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

</div>
<!-- Footer -->
<footer class="bg-white text-black text-center py-3 mt-4">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>
</body>
</html>

