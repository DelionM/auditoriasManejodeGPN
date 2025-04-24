<?php
session_start();

// Verificar si el usuario está logueado
function check_login() {
    // Si no hay sesión de número de empleado ni tipo, redirigir al login
    if (!isset($_SESSION["numero_empleado"]) || !isset($_SESSION["tipo"])) {
        // Intentar redirigir a la ruta relativa al directorio padre
        $login_url = 'auditoria/../login.php';

        // Intentar la redirección principal
        header("Location: $login_url");

        // Verificar si las cabeceras ya se enviaron (por ejemplo, por un error o salida previa)
        if (headers_sent()) {
            // Si las cabeceras fallaron, intentar la ruta alternativa (login.php en el mismo directorio)
            $fallback_url = 'auditoria/login.php';
            header("Location: $fallback_url");
        }

        exit();
    }

    // Establecer que el usuario ya ha iniciado sesión (solo si no se ha hecho antes)
    if (!isset($_SESSION["logged_in"])) {
        $_SESSION["logged_in"] = true;
    }
}

// Verificar permisos según el tipo de usuario
function check_permission($allowed_roles) {
    check_login(); // Primero verifica que esté logueado (esto redirigirá si no lo está)

    // Si el tipo de usuario no está en los roles permitidos, redirigir según el rol
    if (!in_array($_SESSION["tipo"], $allowed_roles)) {
        switch ($_SESSION["tipo"]) {
            case "superadmin":
                header("Location: index.php");  // Vista para superadmin
                break;
            case "admin":
                header("Location: index.php");  // Vista para administradores
                break;
            case "auditor":
                header("Location: Vista/ver_auditorias_programadas.php");  // Vista para auditores
                break;
            case "usuario":
                header("Location: Vista/mis_auditorias.php");  // Vista para usuarios normales (corregido)
                break;
            default:
                // Si el rol no es válido, redirigir al login
                header("Location: index.php");
                break;
        }
        exit();
    }
}

// Llamar a check_login al inicio para asegurarse de que solo los autenticados continúen
check_login();

// Ahora el resto de tu código puede ejecutarse sabiendo que el usuario está autenticado

// Ahora el resto de tu código puede ejecutarse sabiendo que el usuario está autenticado


// session_start();

// // Verificar si el usuario está logueado
// function check_login() {
//     if (!isset($_SESSION["numero_empleado"]) || !isset($_SESSION["tipo"])) {
//         // Si no está logueado, redirigir al login
//         header("Location: index.php");
//         exit();
//     }
// }

// // Verificar permisos según el tipo de usuario
// function check_permission($allowed_roles) {
//     check_login(); // Primero verifica que esté logueado
    
//     // Si el tipo de usuario no está en los roles permitidos, redirigir
//     if (!in_array($_SESSION["tipo"], $allowed_roles)) {
//         // Redirigir según el tipo de usuario a una página por defecto
        


//         switch ($_SESSION["tipo"]) {
//             case "superadmin":
//                 header("Location: index.php");  // Vista para superadmin
//                 break;
//             case "admin":
//                 header("Location: index.php");  // Vista para administradores
//                 break;
//             case "auditor":
//                 header("Location: Vista/ver_auditorias_programadas.php");  // Vista para auditores
//                 break;
//             case "usuario":
//                 header("Location: Vista/mis_audtorias.php");  // Vista para usuarios normales
//                 break;
//             default:
//                 $mensaje = "Tipo de usuario no válido.";
//                 $tipo_mensaje = "danger";
//                 break;
//         }
//         exit();
//     }
// }



?>



