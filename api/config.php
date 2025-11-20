<?php
// config.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

// Este archivo parece tener funciones duplicadas o lógica extra
// Se mantiene la conexión para que funcione si lo usas como punto de entrada
// ... (Resto de tu lógica original funcionará bien con $db conectado a MySQL)
?>