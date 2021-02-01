<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "parkathome_mobile";

// Cria a ligação à BD
$conn = mysqli_connect($servername, $dbUsername, $dbPassword, $dbName);
// Verifica se a ligação falhou (ou teve sucesso)
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//
// params: id
//


$json = file_get_contents('php://input');
$obj = json_decode($json);

$id = $obj->id;

$query = "DELETE FROM park WHERE id=$id;";
$result = mysqli_query($conn, $query);

if ($result) {
    $finalObj = (object) ['message' => "success"];
} else {
    $finalObj = (object) ['message' => "delete_failed"];
}

$response = json_encode($finalObj, JSON_PRETTY_PRINT);
echo $response;

mysqli_close($conn);
