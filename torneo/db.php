<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "torneo_scolastico";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $password, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "<!DOCTYPE html><html lang='it'><head><meta charset='UTF-8'><title>Errore</title>";
    echo "<link rel='stylesheet' href='style.css'></head><body>";
    echo "<div class='error-page'>";
    echo "<h1>⚠️ Errore di Connessione</h1>";
    echo "<p>Impossibile connettersi al database. Verifica che XAMPP sia avviato e che il database <strong>torneo_scolastico</strong> esista.</p>";
    echo "<p class='error-detail'>Dettaglio: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='index.html' class='btn-back'>Torna alla Home</a>";
    echo "</div></body></html>";
    exit;
}

?>