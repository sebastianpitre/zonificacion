<?php
include "conexion.php";

// Obtener los datos del formulario
$id_lote = $_POST['id_lote'];
$muestra1 = $_POST['muestra1'];
$muestra2 = $_POST['muestra2'];
$muestra3 = $_POST['muestra3'];
$fecha_hora = date("Y-m-d H:i:s");

// Insertar los datos en la base de datos
$sql = "INSERT INTO muestras (id_lote, muestra1, muestra2, muestra3, fecha_hora) VALUES ('$id_lote', '$muestra1', '$muestra2', '$muestra3', '$fecha_hora')";

if ($conn->query($sql) === TRUE) {
    header("Location: muestras.php?id_lote=$id_lote");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
