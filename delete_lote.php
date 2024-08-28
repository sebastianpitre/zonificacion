<?php
include "conexion.php";

if (isset($_GET['id_lote'])) {
    $id_lote = $_GET['id_lote'];

    // Primero, elimina las muestras asociadas al lote para mantener la integridad referencial
    $sql_delete_muestras = "DELETE FROM muestras WHERE id_lote = $id_lote";
    if ($conn->query($sql_delete_muestras) === TRUE) {
        // Luego, elimina el lote
        $sql_delete_lote = "DELETE FROM lotes WHERE id_lote = $id_lote";
        if ($conn->query($sql_delete_lote) === TRUE) {
            echo "Lote eliminado correctamente.";
        } else {
            echo "Error al eliminar el lote: " . $conn->error;
        }
    } else {
        echo "Error al eliminar las muestras asociadas: " . $conn->error;
    }
} else {
    echo "ID de lote no proporcionado.";
}

// Cerrar conexión
$conn->close();
header("Location: admin.php"); // Redirige a la página principal después de la eliminación
exit;
?>
