<?php
$host = 'localhost';
$db   = 'c2231876_miweb';
$user = 'c2231876_miweb';
$pass = 'Jesus2025';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
