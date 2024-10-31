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
    SELECT p.numero_matricola, p.cin, p.stato, p.consegna, p.immagine,
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
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['numero_matricola'];
$campo_operativo_id = $progetto['campo_operativo_id'];
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

    .details-block,
    .card {
        border-radius: 8px;
        background-color: white;
        min-height: 300px; /* Imposta la stessa altezza per il details block e le schede */
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        border: none;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .card-body img {
        max-width: 100%;
        max-height: 150px; /* Limita l'altezza dell'immagine per rimanere proporzionale */
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
    }


    .project-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }


    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
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
        <div class="row align-items-center">
            <!-- Dettagli del progetto -->
        </div>

        <!-- Griglia delle card per i dipartimenti -->
        <div class="row mt-4">
            <?php if ($campo_operativo_id == 2): ?>
                <!-- Campo operativo Navale -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-tools fa-3x mb-3"></i>
                            <h5 class="card-title">Fiberglass Department</h5>
                            <a href="fiberglass_department.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-outline-primary btn-rounded">
                                Visualizza
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-couch fa-3x mb-3"></i>
                            <h5 class="card-title">Outfitting Department</h5>
                            <a href="outfitting_department.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-outline-primary btn-rounded">
                                Visualizza
                            </a>
                        </div>
                    </div>
                </div>
            <?php elseif ($campo_operativo_id == 1): ?>
                <!-- Campo operativo Aerospazio -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-cogs fa-3x mb-3"></i>
                            <h5 class="card-title">Assembly Line</h5>
                            <a href="assembly_line.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-outline-primary btn-rounded">
                                Visualizza
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <h5 class="card-title">Quality Control</h5>
                            <a href="quality_control.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-outline-primary btn-rounded">
                                Visualizza
                            </a>
                        </div>
                    </div>
                </div>
            <?php elseif ($campo_operativo_id == 3): ?>
                <!-- Campo operativo Industriale -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-battery-full fa-3x mb-3"></i>
                            <h5 class="card-title">Battery Production</h5>
                            <a href="battery_production.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-outline-primary btn-rounded">
                                Visualizza
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 text-center">
                        <div class="card-body">
                            <i class="fas fa-spinner fa-3x mb-3"></i>
                            <h5 class="card-title">Status</h5>
                            <h6 class="card-title"><i class="fas fa-microchip"></i> IoT device</h6>

                            <a href="status.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-outline-primary btn-rounded">
                                Visualizza
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <a href="dashboard_progetto.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
           class="btn btn-primary btn-rounded">
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
