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
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

</style>


<?php include 'navbar.php'; ?>


<!-- Main Content -->
<div class="full-screen-container">
    <div class="container my-auto">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4">Benvenuto in GENE.SYS</h1>
                <p class="lead">Il sistema di gestione di processi industriali e manutentivi</p>
                <p>
                    Gene.SYS è un software per la digitalizzazione della raccolta dati di processi industriali e
                    manutentivi
                    che caratterizzano tutta la vita di un manufatto.
                </p>
                <a href="login.php" class="btn btn-primary btn-rounded">Accedi al Sistema</a>
            </div>
        </div>

        <!-- Sezione Card -->
        <div class="row justify-content-center mt-5">
            <!-- Card Produzione -->
            <div class="col-md-4">
                <div class="card text-center mb-4" style="border-radius: 10px;">
                    <div class="card-body">
                        <i class="fas fa-cogs fa-3x mb-3 text-primary"></i> <!-- Icona Produzione -->
                        <h5 class="card-title">Produzione</h5>
                    </div>
                </div>
            </div>

            <!-- Card Manutenzione -->
            <div class="col-md-4">
                <div class="card text-center mb-4" style="border-radius: 10px;">
                    <div class="card-body">
                        <i class="fas fa-wrench fa-3x mb-3 text-primary"></i> <!-- Icona Manutenzione -->
                        <h5 class="card-title">Manutenzione</h5>
                    </div>
                </div>
            </div>

            <!-- Card Sostenibilità -->
            <div class="col-md-4">
                <div class="card text-center mb-4" style="border-radius: 10px;">
                    <div class="card-body">
                        <i class="fas fa-leaf fa-3x mb-3 text-primary"></i> <!-- Icona Sostenibilità -->
                        <h5 class="card-title">Sostenibilità</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-black text-center">
        &copy; 2024 GENE.SYS. Tutti i diritti riservati.
    </footer>
</div>
</body>
</html>
