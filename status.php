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

<!-- CDN di Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkOG0qi7kh0x0pmE_rC3hSZJwrYNs6KpM"></script>

<style>
    .details-block {
        border-radius: 8px;
        background-color: white;
        padding: 20px;
        text-align: center;
        margin-bottom: 30px;
    }

    .status-grid {
        display: flex;
        flex-wrap: wrap; /* Consente ai blocchi di andare a capo su schermi piccoli */
        gap: 20px;
        justify-content: space-between;
        margin-top: 20px;
        margin-bottom: 30px;
    }

    .card {
        flex: 1;
        min-width: 250px; /* Imposta una larghezza minima per le card */
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        height: 250px; /* Altezza uniforme alle card */
    }

    .chart-container {
        position: relative;
        height: 150px;
        width: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #map {
        height: 250px;
        background-color: #ccc;
        margin-top: 20px;
    }
</style>


<div class="full-screen-container">
    <div class="container mt-5">
        <div class="details-block">
            <h3><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['stato'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['consegna'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <h2>Status</h2>
        <!-- Griglia per le card -->
        <div class="status-grid">
            <!-- Tensione -->
            <div class="card">
                <h5>Tensione [V]</h5>
                <div class="chart-container">
                    <canvas id="tensioneChart"></canvas>
                </div>
            </div>

            <!-- Temperatura -->
            <div class="card">
                <h5>Temperatura [°C]</h5>
                <div class="chart-container">
                    <canvas id="temperaturaChart"></canvas>
                </div>
            </div>

            <!-- Stato Salute -->
            <div class="card">
                <h5>Salute [%]</h5>
                <div class="chart-container">
                    <canvas id="saluteChart"></canvas>
                </div>
            </div>
        </div>
        <h2>Tracking</h2>
        <div id="map" style="height: 300px; background-color: #ccc; margin-top: 20px;">
            <p class="text-center mt-3">Mappa corrente (simulata)</p>
        </div>

        <a href="produzione_dashboard.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
           class="btn btn-primary btn-rounded mt-4">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
</div>

<footer class="bg-white text-black text-center py-3 mt-4">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>
</body>
</html>

<script>
    // JSON di stile per la mappa
    const mapStyle = [
        // Inserisci qui tutto il JSON che hai fornito
    ];

    // Inizializza la mappa con lo stile JSON
    function initMap() {
        const mapOptions = {
            zoom: 4,
            center: { lat: 41.9028, lng: 12.4964 }, // Centra su Roma (lat, lng) o il luogo che desideri
            styles: mapStyle
        };

        const map = new google.maps.Map(document.getElementById("map"), mapOptions);
    }

    // Avvia la mappa quando la pagina è completamente caricata
    window.onload = initMap;
</script>
<script>
    // Tensione - Configurazione del grafico a batteria
    const tensioneCtx = document.getElementById('tensioneChart').getContext('2d');
    const tensioneChart = new Chart(tensioneCtx, {
        type: 'doughnut',
        data: {
            labels: ['Carica', 'Vuoto'],
            datasets: [{
                data: [36, 12],
                backgroundColor: ['#28a745', '#ddd'],
                hoverBackgroundColor: ['#28a745', '#ddd']
            }]
        },
        options: {
            responsive: true,
            cutout: '80%',
            plugins: {
                tooltip: {enabled: true},
                legend: {display: false},
            }
        }
    });

    // Temperatura - Configurazione del grafico a gauge
    const temperaturaCtx = document.getElementById('temperaturaChart').getContext('2d');
    const temperaturaChart = new Chart(temperaturaCtx, {
        type: 'doughnut',
        data: {
            labels: ['Temperatura Attuale', ''],
            datasets: [{
                data: [25, 55],
                backgroundColor: ['#f39c12', '#ddd'],
                hoverBackgroundColor: ['#f39c12', '#ddd']
            }]
        },
        options: {
            responsive: true,
            cutout: '80%',
            plugins: {
                tooltip: {enabled: true},
                legend: {display: false},
            }
        }
    });

    // Stato Salute - Configurazione del grafico circolare
    const saluteCtx = document.getElementById('saluteChart').getContext('2d');
    const saluteChart = new Chart(saluteCtx, {
        type: 'doughnut',
        data: {
            labels: ['Salute del Pacco', ''],
            datasets: [{
                data: [90, 10],
                backgroundColor: ['#17a2b8', '#ddd'],
                hoverBackgroundColor: ['#17a2b8', '#ddd']
            }]
        },
        options: {
            responsive: true,
            cutout: '80%',
            plugins: {
                tooltip: {enabled: true},
                legend: {display: false},
            }
        }
    });
</script>
</body>
</html>
