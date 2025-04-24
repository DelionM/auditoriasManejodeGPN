<?php 
include '../conexion.php'; // Archivo con la conexión a la BD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $numero_empleado = $_POST['numero_empleado'];
    $nombre = $_POST['nombre'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encripta la contraseña
    $correo = $_POST['correo'];
    $tipo = $_POST['tipoUsuario'];


    //if (empty($numero_empleado) || empty($nombre) || empty($usuario) || empty($password) || empty($tipo)) {
      //  echo "Todos los campos son obligatorios.";
       // exit;
    //}
    // Validar que los campos no estén vacíos
    if (empty($numero_empleado) || empty($nombre) || empty($password) || empty($tipo) || empty($correo) ) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    // Insertar en la tabla empleados
    $stmt = $conexion->prepare("INSERT INTO empleados (numero_empleado, nombre, contraseña, tipo, correo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $numero_empleado, $nombre, $password, $tipo, $correo);

    if ($stmt->execute()) {
        echo "<script> window.location.href = '../Vista/ver_usuarios_registrados.php';</script>";
    } else {
        echo "<script>alert('Error al registrar el empleado.'); window.history.back();</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
