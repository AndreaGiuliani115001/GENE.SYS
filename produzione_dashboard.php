
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
    SELECT p.cin, p.state, p.delivery, p.immagine,
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
        text-align: left;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: white;
    }

    .project-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .department-block {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .department-block a {
        padding: 15px 20px;
        width: 100%;
        max-width: 250px;
        text-align: center;
        font-size: 18px;
        border-radius: 50px;
        text-decoration: none;
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    /* Ottimizzazione per schermi piccoli */
    @media (max-width: 768px) {
        .details-block {
            text-align: center;
        }

        .department-block a {
            max-width: 100%;
        }
    }
</style>

<div class="full-screen-container">
    <div class="container mt-5">
        <div class="row align-items-center">

            <!-- Immagine del progetto -->
            <div class="col-md-6 mb-4">
                <div class="project-image">
                    <img src="<?= htmlspecialchars($progetto['immagine'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="Immagine del progetto">
                </div>
            </div>

            <!-- Dettagli del progetto -->
            <div class="col-md-6">
                <div class="details-block shadow-sm">
                    <h3><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="department-block">
                        <?php if ($campo_operativo_id == 2): ?>
                            <!-- Campo operativo Navale -->
                            <a class="btn btn-primary btn-rounded"
                               href="fiberglass_department.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"><i class="fas fa-tools"></i>
                                Fiberglass Department</a>
                            <a class="btn btn-primary btn-rounded"
                               href="outfitting_department.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"><i class="fas fa-couch"></i>
                                Outfitting Department</a>
                        <?php elseif ($campo_operativo_id == 1): ?>
                            <!-- Campo operativo Aerospazio -->
                            <a class="btn btn-primary btn-rounded"
                               href="assembly_line.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"><i class="fas fa-cogs"></i>
                                Assembly Line</a>
                            <a class="btn btn-primary btn-rounded"
                               href="quality_control.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"><i class="fas fa-check-circle"></i>
                                Quality Control</a>
                        <?php elseif ($campo_operativo_id == 3): ?>
                            <!-- Campo operativo Industriale -->
                            <a class="btn btn-primary btn-rounded"
                               href="battery_production.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"><i class="fas fa-battery-full"></i>
                                Battery Production</a>
                            <a class="btn btn-primary btn-rounded"
                               href="assembly_line_industrial.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"><i class="fas fa-industry"></i>
                                Assembly Line</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
        <a href="dashboard_progetto.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Torna alle fasi
        </a>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white text-black text-center py-3 mt-4">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

</body>
</html>
