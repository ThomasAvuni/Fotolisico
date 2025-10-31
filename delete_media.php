<?php
require_once "connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito']);
    exit;
}

if (!isset($_POST['id_media'])) {
    echo json_encode(['success' => false, 'error' => 'ID media non fornito']);
    exit;
}

$id_media = (int)$_POST['id_media'];

// Prima otteniamo il percorso del file
$query = "SELECT percorso_file FROM risorse_multimediali WHERE id_media = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_media);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Media non trovato']);
    exit;
}

$media = $result->fetch_assoc();
$file_path = $media['percorso_file'];

// Eliminiamo il record dal database
$query = "DELETE FROM risorse_multimediali WHERE id_media = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_media);

if ($stmt->execute()) {
    // Se l'eliminazione dal database è riuscita, proviamo a eliminare il file
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Errore durante l\'eliminazione']);
}

$conn->close();
?>