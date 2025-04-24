<?php
// Controlador/eliminarRegistroUsuario.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";

$conexion = new mysqli($servername, $username, $password, $database);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if (isset($_GET['numero_empleado'])) {
    $numero_empleado = $_GET['numero_empleado'];
    $sql = "DELETE FROM empleados WHERE numero_empleado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numero_empleado);
    
    if ($stmt->execute()) {
        header("Location: ../ver_usuarios_registrados.php?success=Usuario eliminado correctamente");
        exit();
    } else {
        header("Location: ../ver_usuarios_registrados.php?error=Error al eliminar usuario");
        exit();
    }
} else {
    header("Location: ../ver_usuarios_registrados.php?error=Número de empleado no proporcionado");
    exit();
}

$conexion->close();
?>