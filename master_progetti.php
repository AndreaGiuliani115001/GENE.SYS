<?php

include 'navbar.php';

/** @var mysqli $conn */
include('connection.php');


// Verifica se l'utente è un Master (admin globale o admin aziendale)
if ($_SESSION['ruolo'] != 'master' || (!is_null($_SESSION['azienda_id']) && $_GET['azienda_id'] != $_SESSION['azienda_id'])) {
    header("Location: login.php");
    exit;
}

// Recupera gli ID dell'azienda e della linea di prodotto selezionati
$azienda_id = $_GET['azienda_id'];
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Conta i progetti totali per l'azienda e la linea di prodotto selezionate
$count_progetti = $conn->query("SELECT COUNT(*) as totale_progetti FROM progetti WHERE azienda_id = $azienda_id AND linea_prodotto_id = $linea_prodotto_id")->fetch_assoc()['totale_progetti'];


// Recupera i progetti associati all'azienda e alla linea di prodotto selezionate
$stmt = $conn->prepare("
    SELECT p.numero_matricola, p.nome_cliente, p.cin, p.stato, p.consegna, p.immagine, 
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
        height: 100%; /* Imposta l'altezza della card per riempire tutto lo spazio */
        display: flex;
        flex-direction: column; /* Per garantire che il contenuto sia distribuito verticalmente */
    }

    .card-img-top {
        width: 100%;
        height: 200px; /* Imposta un'altezza fissa per le immagini */
        object-fit: cover; /* Mantiene le proporzioni e ritaglia l'immagine se necessario */
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .card-body {
        flex-grow: 1; /* Permette al corpo della card di occupare tutto lo spazio rimanente */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Progress block (opzionale, se vuoi uniformare anche questo elemento in altezza) */
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

    /* Contenitore dei bottoni con Flexbox */
    .card-body .button-group {
        display: flex;
        justify-content: space-between; /* Spazia i bottoni */
        gap: 10px; /* Aggiunge spazio tra i bottoni */
    }

    .card-body .btn {
        flex-grow: 1; /* Permette ai bottoni di crescere in modo uniforme */
        width: auto; /* Imposta la larghezza automatica per evitare che si estendano per tutta la card */
        white-space: nowrap; /* Evita che il testo vada a capo */
    }

    .stat-box {
        background-color: #fff;
        padding: 20px;
        margin-bottom: 10px;
        border-radius: 10px;
        text-align: center;
    }

    .stat-box i {
        font-size: 36px;
        color: #27bcbc;
        margin-bottom: 10px;
    }

    .stat-box h4 {
        font-size: 24px;
        margin-bottom: 0;
    }

</style>


<div class="full-screen-container">
    <div class="container mt-5 my-auto">
        <h2>Progetti dell'Azienda</h2>

        <?php if (isset($result) && $result->num_rows == 0): ?>
            <div class="alert alert-info mt-3">Nessuna progetto trovato per questa azienda.</div>
        <?php endif; ?>

        <!-- Blocco per il conteggio totale delle linee di prodotto -->
        <div class="stat-box mt-5 shadow-sm">
            <i class="fas fa-folder"></i>
            <h4><?= $count_progetti ?> Progetti</h4>
        </div>

        <div class="row">
            <?php while ($progetto = $result->fetch_assoc()):
                $nome_progetto = $progetto['azienda'] . " " . $progetto['linea_prodotto'] . " #" . $progetto['numero_matricola'];
                ?>
                <div class="col-md-4 mb-4 mt-3">
                    <div class="card">
                        <img src="<?= htmlspecialchars($progetto['immagine'], ENT_QUOTES, 'UTF-8') ?>"
                             class="card-img-top" alt="Progetto">
                        <div class="card-body">
                            <h5 class="card-title mb-4"><?= htmlspecialchars($nome_progetto, ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="card-text">
                                <strong>Cliente:</strong> <?= htmlspecialchars($progetto['nome_cliente'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="card-text">
                                <strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="card-text">
                                <strong>Stato:</strong> <?= htmlspecialchars($progetto['stato'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="card-text">
                                <strong>Consegna:</strong> <?= htmlspecialchars($progetto['consegna'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <!-- Blocco Progresso -->
                            <div class="progress-block">
                                <div class="progress-bar">
                                    <div></div>
                                </div>
                            </div>

                            <!-- Contenitore dei bottoni -->
                            <div class="btn-group">
                                <a href="dashboard_progetto.php?progetto_id=<?= $progetto['id_progetto'] ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                                   class="btn btn-primary btn-rounded"><i class="fas fa-eye"></i></a>
                                <a href="modifica_progetto.php?progetto_id=<?= $progetto['id_progetto'] ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                                   class="btn btn-warning btn-rounded"><i class="fas fa-edit"></i></a>
                                <a href="elimina_progetto.php?progetto_id=<?= $progetto['id_progetto'] ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
                                   class="btn btn-danger btn-rounded"
                                   onclick="return confirm('Sei sicuro di voler eliminare questo progetto?');"><i
                                            class="fas fa-trash"></i></a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="d-flex justify-content-between">
            <a href="master_linee_prodotti.php?azienda_id=<?= $azienda_id ?>" class="btn btn-primary btn-rounded">
                <i class="fas fa-arrow-left"></i>
            </a>
            <a href="aggiungi_progetto.php?azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
               class="btn btn-primary btn-rounded "><i class="fas fa-plus"></i></a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center mt-4">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>

