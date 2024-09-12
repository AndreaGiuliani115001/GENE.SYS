<?php

include 'navbar.php';

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera gli ID dell'azienda e della linea di prodotto selezionati
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Recupera i progetti associati all'azienda e alla linea di prodotto selezionate
$stmt = $conn->prepare("
    SELECT p.cin, p.state, p.delivery, p.immagine, 
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.azienda_id = ? AND p.linea_prodotto_id = ?");
$stmt->bind_param("ii", $azienda_id, $linea_prodotto_id);
$stmt->execute();
$result = $stmt->get_result();

// Se non ci sono progetti, mostra un messaggio
if ($result->num_rows === 0) {
    die("Nessun progetto trovato per questa linea di prodotto.");
}
?>
<style>
    /* Imposta altezza e larghezza del 100% su html e body */
    html, body {
        height: 100%;
        margin: 0;
    }

    /* Imposta il contenitore principale per occupare tutto lo schermo */
    .full-screen-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Distribuisce il contenuto tra header e footer */
        min-height: 100vh; /* Occupazione dell'intero viewport */
    }

    /* Stile per il footer per mantenerlo in fondo alla pagina */
    footer {
        padding: 20px;
    }
</style>


<div class="full-screen-container">
    <div class="container mt-5 my-auto">
        <h2>Progetti dell'Azienda</h2>

        <!-- Mostra il pulsante "Aggiungi Progetto" solo se l'utente è master -->
        <?php if ($_SESSION['ruolo'] == 'master'): ?>
            <div class="text-end mb-4">
                <a href="aggiungi_progetto.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-primary ">Aggiungi Progetto</a>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php while ($progetto = $result->fetch_assoc()):
                $nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['id_progetto'];
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($progetto['immagine'], ENT_QUOTES, 'UTF-8') ?>"
                             class="card-img-top" alt="Progetto">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="card-text">
                                <strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="card-text">
                                <strong>STATE:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="card-text">
                                <strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <a href="dashboard_progetto.php?progetto_id=<?= $progetto['id_progetto'] ?>"
                               class="btn btn-outline-primary btn-rounded">Visualizza Progetto</a>
                            <a href="elimina_progetto.php?progetto_id=<?= $progetto['id_progetto'] ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-danger btn-rounded"
                               onclick="return confirm('Sei sicuro di voler eliminare questo progetto?');">
                                Elimina Progetto
                            </a>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>

