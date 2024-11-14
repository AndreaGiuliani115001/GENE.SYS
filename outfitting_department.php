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
// Recupera l'ID del progetto dalla query string
$azienda_id = $_GET['azienda_id'];
// Recupera l'ID del progetto dalla query string
$linea_prodotto_id = $_GET['linea_prodotto_id'];

// Recupera i dettagli del progetto dal database
$stmt = $conn->prepare("
    SELECT p.numero_matricola, p.cin, p.stato, p.consegna, p.immagine,
           a.nome AS azienda, 
           lp.nome AS linea_prodotto, 
           p.id AS id_progetto
    FROM progetti p
    JOIN aziende a ON p.azienda_id = a.id
    JOIN linee_prodotti lp ON p.linea_prodotto_id = lp.id
    WHERE p.id = ?");
$stmt->bind_param("i", $progetto_id);
$stmt->execute();
$progetto = $stmt->get_result()->fetch_assoc();
?>

<style>
    html, body {
        height: 100%;
        margin: 0;
    }

    .full-screen-container {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100vh;
        padding: 20px;
    }

    .details-block {
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: white;
    }


    .project-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .details-block {
            text-align: center;
        }

        .project-image img {
            max-width: 100%;
        }
    }

    .card {
        border: none;
        border-radius: 8px;
    }

    .card-img-top {
        max-height: 200px; /* Imposta l'altezza fissa per mantenere consistenza */
        object-fit: cover; /* Ritaglia l'immagine mantenendo il centro */
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    #3d-viewer {
        width: 100%;
        max-width: 100%;
        height: 500px;
        background-color: #f0f0f0;
        margin-bottom: 20px;
        border-radius: 8px;
        overflow: hidden;
    }


</style>

<div class="full-screen-container">
    <div class="container">
        <!-- Blocco Dettagli -->
        <div class="details-block shadow-sm">
            <h3><?= htmlspecialchars($progetto['azienda'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($progetto['linea_prodotto'], ENT_QUOTES, 'UTF-8') . " #" . htmlspecialchars($progetto['numero_matricola'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p><strong>CIN:</strong> <?= htmlspecialchars($progetto['cin'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>STATE:</strong> <?= htmlspecialchars($progetto['stato'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>DELIVERY:</strong> <?= htmlspecialchars($progetto['consegna'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <!-- Griglia delle card per i setup -->
        <div class="row mt-4">
            <!-- Card per Outdoor Setup -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <img src="uploads/outdoor.png" alt="Outdoor Setup" class="card-img-top"
                         style="border-top-left-radius: 8px; border-top-right-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Outdoor Setup</h5>
                        <a href="outdoor_setup.php?progetto_id=<?= $progetto_id ?>"
                           class="btn btn-outline-primary btn-rounded mt-3">
                            Vai alle checklist
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card per Indoor Setup -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <img src="uploads/indoor.png" alt="Indoor Setup" class="card-img-top"
                         style="border-top-left-radius: 8px; border-top-right-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title">Indoor Setup</h5>
                        <a href="indoor_setup.php?progetto_id=<?= $progetto_id ?>"
                           class="btn btn-outline-primary btn-rounded mt-3">
                            Vai alle checklist
                        </a>
                    </div>
                </div>
            </div>
            <div class="container">
                <div id="3d-viewer" class="mb-3"></div>
            </div>

        </div>
        <a href="produzione_dashboard.php?progetto_id=<?= $progetto_id ?>&azienda_id=<?= $azienda_id ?>&linea_prodotto_id=<?= $linea_prodotto_id ?>"
           class="btn btn-primary btn-rounded">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>
</div>


<!-- Footer -->
<footer class="bg-white text-black text-center py-3">
    &copy; 2024 GENE.SYS. Tutti i diritti riservati.
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/mrdoob/three.js@r128/examples/js/loaders/TDSLoader.js"></script>

<script>
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / 500, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer();
    renderer.setSize(document.getElementById('3d-viewer').clientWidth, 500);
    document.getElementById('3d-viewer').appendChild(renderer.domElement);

    // Aggiungi una luce ambientale per illuminare tutta la scena
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.2);
    scene.add(ambientLight);

    // Aggiungi una luce direzionale
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(1, 1, 1).normalize();
    scene.add(directionalLight);

    // Variabile per memorizzare l'oggetto caricato
    let loadedObject;

    // Carica il modello 3DS
    const loader = new THREE.TDSLoader();
    loader.load('uploads/prova5.3ds', function (object) {
        loadedObject = object; // Salva il riferimento all'oggetto caricato
        scene.add(object);

        // Posiziona e scala il modello
        object.position.set(0, -50, 0); // Aggiusta l'altezza se necessario
        object.scale.set(0.1, 0.1, 0.1); // Riduci la scala per adattarlo alla scena

        // Centra il modello
        object.traverse(function (child) {
            if (child.isMesh) {
                child.geometry.center();
            }
        });
    }, undefined, function (error) {
        console.error(error);
    });

    // Posiziona la camera
    camera.position.set(0, -50, 100); // Posiziona la camera all'indietro rispetto al modello

    // Loop di animazione
    function animate() {
        requestAnimationFrame(animate);

        // Se l'oggetto è stato caricato, ruotalo e punta la camera su di esso
        if (loadedObject) {
            loadedObject.rotation.y += 0.005; // Ruota il modello

            // Punta la camera verso l'oggetto
            camera.lookAt(loadedObject.position);
        }

        renderer.render(scene, camera);
    }

    animate();


</script>

</body>
</html>

