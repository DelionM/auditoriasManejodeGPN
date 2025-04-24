<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado y es superadmin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'superadmin') {
    header("Location: ../login.php"); // Redirigir si no es superadmin
    exit();
}

// Incluir la conexión a la base de datos
require '../conexion.php';

// Obtener el ID de la auditoría desde la URL
$id_auditoria = isset($_GET['id']) ? $_GET['id'] : null;

if ($id_auditoria) {
    try {
        // Preparar la consulta para eliminar la auditoría
        $sql = "DELETE FROM programar_auditoria WHERE id_auditoria = ?";
        
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("s", $id_auditoria);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Éxito al eliminar
                header("Location: ../Vista/ver_auditorias_programadas.php?success=1"); // Redirigir con mensaje de éxito
                exit();
            } else {
                // Error al ejecutar la consulta
                header("Location: ../Vista/ver_auditorias_programadas.php?error=1"); // Redirigir con mensaje de error
                exit();
            }

            $stmt->close();
        } else {
            // Error al preparar la consulta
            header("Location: ../Vista/ver_auditorias_programadas.php?error=2"); // Redirigir con mensaje de error
            exit();
        }
    } catch (Exception $e) {
        // Manejo de errores más detallado
        error_log("Error al eliminar auditoría: " . $e->getMessage());
        header("Location: ../Vista/ver_auditorias_programadas.php?error=3"); // Redirigir con mensaje de error
        exit();
    }
} else {
    // Si no se proporciona ID
    header("Location: ../Vista/ver_auditorias_programadas.php?error=4"); // Redirigir con mensaje de error
    exit();
}

// Cerrar conexión
$conexion->close();
?>
