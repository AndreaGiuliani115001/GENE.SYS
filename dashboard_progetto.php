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
$result = $stmt->get_result();

// Se il progetto non viene trovato
if ($result->num_rows === 0) {
    die("Progetto non trovato.");
}

$progetto = $result->fetch_assoc();
$nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];
?>

    <style>
        .dashboard-block {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f5f5f5;
            transition: background-color 0.3s;
            margin-bottom: 20px;
        }

        .dashboard-block:hover {
            background-color: #e9ecef;
        }

        .dashboard-block a {
            text-decoration: none;
        }

        .dashboard-icon {
            font-size: 50px;
            color: #0275d8;
            margin-bottom: 10px;
        }
    </style>


<div class="container mt-5">
    <h2 class="mb-4"><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h2>

    <!-- Blocco Produzione -->
    <div class="dashboard-block">
        <a href="produzione_dashboard.php?progetto_id=<?= $progetto_id ?>">
            <div class="dashboard-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <h3>Produzione</h3>
            <p>Visualizza i dettagli di produzione del progetto</p>
        </a>
    </div>

    <!-- Blocco Manutenzione -->
    <div class="dashboard-block">
        <a href="manutenzione_dashboard.php?progetto_id=<?= $progetto_id ?>">
            <div class="dashboard-icon">
                <i class="fas fa-wrench"></i>
            </div>
            <h3>Manutenzione</h3>
            <p>Gestisci la manutenzione del progetto</p>
        </a>
    </div>

    <!-- Blocco Sostenibilità -->
    <div class="dashboard-block">
        <a href="sostenibilita_dashboard.php?progetto_id=<?= $progetto_id ?>">
            <div class="dashboard-icon">
                <i class="fas fa-leaf"></i>
            </div>
            <h3>Sostenibilità</h3>
            <p>Visualizza i dettagli di sostenibilità del progetto</p>
        </a>
    </div>
</div>

<!-- Footer -->
<footer class="bg-white text-black text-center py-3 mt-5">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

</body>
</html>