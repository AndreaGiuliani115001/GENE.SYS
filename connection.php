<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gene.sys";

// Creazione connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica connessione
if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

