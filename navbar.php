<?php
session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GENE.SYS</title>
    <!-- Includi Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Includi FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            background-color: #f5f6fc;
        }

        .navbar {
            background-color: transparent;
        }

        .navbar:hover {
            background-color: #fff !important;
            transition: background-color 0.3s ease;
        }

        .navbar a.nav-link:hover {
            color: #000 !important;
        }

        .navbar-brand img {
            margin-right: 10px;
        }

        .btn-rounded {
            border-radius: 50px; /* Arrotondamento completo */
            padding-left: 15px;
            padding-right: 15px;
        }

        .btn-outline-primary {
            color: #27bcbc;
            border-color: #27bcbc;
        }

        .btn-outline-primary:hover {
            background-color: #27bcbc;
            color: white;
        }

        .btn-primary {
            background-color: #27bcbc;
            border-color: #27bcbc;
        }

        .btn-primary:hover {
            background-color: #138496;
            border-color: #117a8b;
        }

        .card .fas, .card .fa {
            color: #27bcbc !important;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="uploads/logoGenesis.png" alt="Logo" style="width: 40px; height: 40px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="https://www.p2msrl.it">P2M</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.p2msrl.it/about_us">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://www.p2msrl.it/gene-sys">GENE.SYS</a>
                </li>

                <!-- Verifica se l'utente è loggato -->
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary btn-rounded" href="master_dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary btn-rounded" href="logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link">Benvenuto, <?= htmlspecialchars($_SESSION['username']); ?>!</span>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary btn-rounded" href="login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Includi Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>