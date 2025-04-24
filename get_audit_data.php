<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "auditoria");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]));
}

$id_auditoria = $_GET['id_auditoria'];
$sql = "SELECT * FROM auditorias WHERE id_auditoria = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_auditoria);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "data" => $row]);
} else {
    echo json_encode(["success" => false, "error" => "No se encontraron datos"]);
}

$stmt->close();
$conn->close();
?>