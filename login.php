<?php
include 'navbar.php';
/** @var mysqli $conn */
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query per trovare l'utente
    $stmt = $conn->prepare("SELECT id, password, ruolo, azienda_id FROM utenti WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();


    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $ruolo, $azienda_id);
        $stmt->fetch();

        // Verifica la password
        if (password_verify($password, $hashed_password)) {
            // Salva l'utente nella sessione
            $_SESSION['user_id'] = $user_id;
            $_SESSION['ruolo'] = $ruolo;
            $_SESSION['azienda_id'] = $azienda_id;
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            // Reindirizza in base al ruolo
            if ($ruolo == 'master') {
                if ($azienda_id === null) {
                    header("Location: master_dashboard.php"); // Admin globale
                } else {
                    header("Location: master_linee_prodotti.php?azienda_id=$azienda_id"); // Admin aziendale
                }
            } else if ($ruolo == 'operatore') {
                header("Location: operatore_dashboard.php");
            }
            exit;


        } else {
            $error = "Credenziali errate.";
        }
    } else {
        $error = "Utente non trovato.";
    }

    $stmt->close();
}
?>

<style>
    body {
        background-color: #f8f9fa; /* Colore simile al sito di esempio */
    }

    .login-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-top: 80px;
    }

    h2 {
        font-weight: bold;
    }

    .btn-primary {
        background-color: #17a2b8; /* Colore azzurro simile al bottone 'Contact us' */
        border-color: #17a2b8;
    }

    .btn-primary:hover {
        background-color: #138496;
        border-color: #117a8b;
    }

    /* Stile dell'alert di errore */
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

<div class="container d-flex justify-content-center">
    <div class="login-container col-md-6">
        <h2 class="text-center">Login</h2>
        <form method="post" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger mt-3">
                <?php echo $error; ?>
            </div>
        <?php } ?>
    </div>

</div>
</body>
</html>

