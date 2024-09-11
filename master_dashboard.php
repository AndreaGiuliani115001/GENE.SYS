<?php
include 'navbar.php';

/** @var mysqli $conn */
include('connection.php');

// Verifica se l'utente è un Master
if ($_SESSION['ruolo'] != 'master') {
    header("Location: login.php");
    exit;
}

// Recupera i campi operativi
$campi_operativi = $conn->query("SELECT * FROM campi_operativi");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['campo_operativo_id'])) {
    $campo_operativo_id = $_POST['campo_operativo_id'];
    $clienti = $conn->query("SELECT * FROM aziende WHERE campo_operativo_id = $campo_operativo_id");
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

    .container {
        flex-grow: 1; /* Permette al contenitore di crescere e riempire lo spazio disponibile */
    }

    /* Stile per il footer per mantenerlo in fondo alla pagina */
    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

</style>

<div class="full-screen-container">
    <!-- Main Content -->
    <div class="container mt-5">
        <h2 class="mb-4">Dashboard Master</h2>
        <form method="post" action="master_dashboard.php">
            <div class="mb-3">
                <label for="campo_operativo_id" class="form-label">Seleziona Campo Operativo:</label>
                <select name="campo_operativo_id" id="campo_operativo_id" class="form-select" required>
                    <?php while ($row = $campi_operativi->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nome'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100 ">Visualizza Clienti</button>
        </form>

        <?php if (isset($clienti)): ?>
            <h3 class="mt-5">Clienti</h3>
            <ul class="list-group">
                <?php while ($row = $clienti->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?>
                        <a href="master_linee_prodotti.php?azienda_id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-outline-primary btn-rounded">Visualizza Linee di Prodotto</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>


