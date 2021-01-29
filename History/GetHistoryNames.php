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
// params: userId
//

$json = file_get_contents('php://input');

$obj = json_decode($json);

$userId = $obj->userId;

$query = "SELECT * FROM history WHERE idUser=$userId";
$result = mysqli_query($conn, $query);

$response = array();

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $idSpace = $row["idSpace"];
            $idVehicule = $row["idVehicule"];
            $idPaymentMethod = $row["idPaymentMethod"];
            $idUser = $row["idUser"];

            $row["vehicule"] = getVehiculeNameById($conn, $idVehicule);
            $row["paymentMethod"] = getPaymentMethodNameById($conn, $idPaymentMethod);
            $row["park"] = getParkNameByIdSpace($conn, $idSpace);

            unset($row["idUser"]);
            unset($row["idSpace"]);
            unset($row["idVehicule"]);
            unset($row["idPaymentMethod"]);

            $row["idUser"] = $idUser;

            $response[$i] = $row;
            $i++;
        }
        $finalObj = (object) ['message' => "success", 'history' => $response];
    } else {
        $finalObj = (object) ['message' => "no_history"];
    }
} else {
    $finalObj = (object) ['message' => "error"];
}

echo json_encode($finalObj, JSON_PRETTY_PRINT);

mysqli_close($conn);

function getVehiculeNameById($conn, $idVehicule)
{
    $vehiculeName = "";

    $query = "SELECT name FROM vehicule WHERE id=$idVehicule";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            if ($row = mysqli_fetch_assoc($result)) {
                $vehiculeName = $row["name"];
            }
        }
    }

    return $vehiculeName;
}

function getPaymentMethodNameById($conn, $idPaymentMethod)
{
    $paymentMethodName = "";

    $query = "SELECT name FROM paymentMethod WHERE id=$idPaymentMethod";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            if ($row = mysqli_fetch_assoc($result)) {
                $paymentMethodName = $row["name"];
            }
        }
    }

    return $paymentMethodName;
}

function getParkNameByIdSpace($conn, $idSpace)
{
    $parkName = "";

    $query = "SELECT idPark FROM space WHERE id=$idSpace";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            if ($row = mysqli_fetch_assoc($result)) {
                $idPark = $row["idPark"];

                $queryPark = "SELECT name FROM park WHERE id=$idPark";
                $resultPark = mysqli_query($conn, $queryPark);

                if ($result) {
                    if (mysqli_num_rows($resultPark) > 0) {
                        if ($row = mysqli_fetch_assoc($resultPark)) {
                            $parkName = $row["name"];
                        }
                    }
                }
            }
        }
    }

    return $parkName;
}
