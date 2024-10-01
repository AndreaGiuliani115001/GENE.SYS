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

    /* Stile personalizzato per le card */
    .card {
        border: none; /* Rimuove il bordo */
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Aggiunge un'ombra personalizzata */
    }

    .card-img-top {
        border-top-left-radius: 8px; /* Mantiene gli angoli arrotondati */
        border-top-right-radius: 8px;
    }

    .progress-block {
        margin: 30px 0;
    }

    .progress-bar {
        width: 100%;
        background-color: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
    }

    .progress-bar div {
        background-color: #27bcbc;
        height: 30px;
    }

</style>


<div class="full-screen-container">
    <div class="container mt-5 my-auto">
        <h2>Progetti dell'Azienda</h2>

        <!-- Mostra il pulsante "Aggiungi Progetto" solo se l'utente è master -->
        <?php if ($_SESSION['ruolo'] == 'master'): ?>
            <div class="text-end mb-4">
                <a href="aggiungi_progetto.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                   class="btn btn-primary btn-rounded "><i class="fas fa-plus"></i></a>
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
                            <h5 class="card-title mb-4"><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="card-text">
                                <strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="card-text">
                                <strong>Stato:</strong> <?= htmlspecialchars($progetto['state'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="card-text">
                                <strong>Consegna:</strong> <?= htmlspecialchars($progetto['delivery'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <!-- Blocco Progresso -->
                            <div class="progress-block">
                                <div class="progress-bar">
                                    <div></div>
                                </div>
                            </div>
                            <a href="dashboard_progetto.php?progetto_id=<?= $progetto['id_progetto'] ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-primary btn-rounded"><i class="fas fa-eye"></i>
                            </a>
                            <a href="elimina_progetto.php?progetto_id=<?= $progetto['id_progetto'] ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                               class="btn btn-danger btn-rounded"
                               onclick="return confirm('Sei sicuro di voler eliminare questo progetto?');">
                                <i class="fas fa-trash"></i>
                            </a>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <a href="master_linee_prodotti.php?azienda_id=<?= $azienda_id ?>" class="btn btn-outline-primary mt-3">
            <i class="fas fa-arrow-left"></i> Torna alle Linee di Prodotto
        </a>

    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center mt-4">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>

