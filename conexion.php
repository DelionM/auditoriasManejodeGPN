<?php
// Configuración de la conexión a la base de datos
$host = "localhost"; // Servidor (usualmente localhost en desarrollo)
$usuario = "root";   // Usuario de la base de datos
$password = "";      // Contraseña del usuario (deja en blanco si usas XAMPP o WAMP)
$baseDatos = "auditoria"; // Nombre de la base de datos

// Crear la conexión
$conexion = new mysqli($host, $usuario, $password, $baseDatos);

// Verificar si la conexión es exitosa
if ($conexion->connect_error) {
    die("Error en la conexión: " . $conexion->connect_error);
}

// Opcional: Mensaje de éxito para pruebas (puedes eliminarlo en producción)
?>
