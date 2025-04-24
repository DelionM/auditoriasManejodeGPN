<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "auditoria");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]));
}

$id_auditoria = $_GET['id_auditoria'] ?? null;
if (!$id_auditoria) {
    die(json_encode(["success" => false, "error" => "No se proporcionó id_auditoria"]));
}

$sql = "SELECT * FROM auditoria_proceso WHERE id_auditoria = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die(json_encode(["success" => false, "error" => "Error al preparar la consulta: " . $conn->error]));
}

$stmt->bind_param("s", $id_auditoria);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die(json_encode(["success" => false, "error" => "Error al ejecutar la consulta: " . $stmt->error]));
}

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "data" => $row]);
} else {
    echo json_encode(["success" => false, "error" => "No se encontraron datos para id_auditoria: $id_auditoria"]);
}

$stmt->close();
$conn->close();
?>