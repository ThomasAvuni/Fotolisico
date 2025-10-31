<?php
require_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    
    // Preparazione della query di aggiornamento
    $query = "UPDATE barche SET 
        marca = ?, 
        modello = ?, 
        lunghezza = ?, 
        anno = ?, 
        omologazione = ?, 
        motore = ?, 
        carburante = ?, 
        propulsione = ?, 
        stato = ?, 
        prezzo = ?, 
        descrizione = ? 
        WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssssi", 
        $_POST['marca'],
        $_POST['modello'],
        $_POST['lunghezza'],
        $_POST['anno'],
        $_POST['omologazione'],
        $_POST['motore'],
        $_POST['carburante'],
        $_POST['propulsione'],
        $_POST['stato'],
        $_POST['prezzo'],
        $_POST['descrizione'],
        $id
    );
    
    // Esecuzione dell'aggiornamento dei dati principali
    if (!$stmt->execute()) {
        die("Errore nell'aggiornamento dei dati: " . $stmt->error);
    }

    // Gestione dei nuovi file caricati
    if (!empty($_FILES['foto']['name'][0]) || !empty($_FILES['video']['name'][0])) {
        $uploadDir = "uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Funzione per il caricamento dei file
        function uploadFiles($files, $type, $barcaId, $conn, $uploadDir) {
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];
            
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] == 0) {
                    $tmpName = $files['tmp_name'][$i];
                    $originalName = $files['name'][$i];
                    $fileType = $files['type'][$i];
                    
                    // Verifica del tipo di file
                    if ($type === 'immagine' && !in_array($fileType, $allowedImageTypes)) {
                        continue;
                    }
                    if ($type === 'video' && !in_array($fileType, $allowedVideoTypes)) {
                        continue;
                    }
                    
                    // Generazione nome file univoco
                    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                    $newFileName = uniqid() . '_' . time() . '.' . $extension;
                    $destination = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($tmpName, $destination)) {
                        // Inserimento nel database
                        $query = "INSERT INTO risorse_multimediali (id_barca_fk, tipo_media, percorso_file) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("iss", $barcaId, $type, $destination);
                        $stmt->execute();
                    }
                }
            }
        }

        // Caricamento delle nuove foto
        if (!empty($_FILES['foto']['name'][0])) {
            uploadFiles($_FILES['foto'], 'immagine', $id, $conn, $uploadDir);
        }
        
        // Caricamento dei nuovi video
        if (!empty($_FILES['video']['name'][0])) {
            uploadFiles($_FILES['video'], 'video', $id, $conn, $uploadDir);
        }
    }

    // Redirect alla lista con messaggio di successo
    header("Location: lista.php");
    exit();
} else {
    die("Metodo non consentito");
}
?>