<?php
// Incluir la conexión a la base de datos
include('../conexion.php');  // Asegúrate de que la ruta sea la correcta

// Comprobar si el ID está presente en la URL
if (isset($_GET['id'])) {
    // Sanitizar el valor del ID para evitar inyecciones SQL
    $id = htmlspecialchars($_GET['id']);

    // Crear la consulta SQL para eliminar el registro
    $sql = "DELETE FROM auditorias WHERE id = ?";

    // Preparar la consulta
    if ($stmt = $conexion->prepare($sql)) {
        // Vincular el parámetro y ejecutar la consulta
        $stmt->bind_param('i', $id); // 'i' indica que el parámetro es un entero
        if ($stmt->execute()) {
            // Redirigir a la página de éxito o a la lista después de eliminar
            header("Location: ../index.php"); 
            exit;
        } else {
            // Si no se pudo ejecutar, mostrar un mensaje de error
            echo "Error al ejecutar la consulta: " . $stmt->error;
        }
        // Cerrar la declaración
        $stmt->close();
    } else {
        // Si la preparación de la consulta falla, mostrar el error
        echo "Error al preparar la consulta: " . $conexion->error;
    }
} else {
    echo "ID no proporcionado.";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
