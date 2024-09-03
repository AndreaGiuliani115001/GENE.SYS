<?php
session_start();

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

            // Reindirizza in base al ruolo
            if ($ruolo == 'master') {
                header("Location: master_dashboard.php");
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

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login - GENE.SYS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Login</h2>
    <form method="post" action="login.php">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <?php if (isset($error)) { ?>
        <div class="alert alert-danger mt-3">
            <?php echo $error; ?>
        </div>
    <?php } ?>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
