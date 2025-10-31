<?php
// Aumenta i limiti di upload
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '300'); // 5 minuti
ini_set('max_input_time', '300');

require_once "connection.php"; // La tua connessione al DB

// Controllo invio del form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Inizia una transazione per garantire l'integrità dei dati
    $conn->begin_transaction();

    try {
        // --- FASE 1: INSERISCI I DATI DELLA BARCA ---

        // Recupero dati testuali
        $marca = $_POST['marca'] ?? '';
        $modello = $_POST['modello'] ?? '';
        $lunghezza = $_POST['lunghezza'] ?? '';
        $omologazione = $_POST['omologazione'] ?? '';
        $motore = $_POST['motore'] ?? '';
        $carburante = $_POST['carburante'] ?? '';
        $propulsione = $_POST['propulsione'] ?? '';
        $descrizione = $_POST['descrizione'] ?? '';
        $anno = $_POST['anno'] ?? '';
        
        // 1. RECUPERA IL NUOVO DATO "STATO" DAL FORM
        $stato = $_POST['stato'] ?? ''; // Sarà 'in vendita' o 'noleggiato'

        // Recupera il prezzo dal form e convertilo in float se presente
        $prezzo = isset($_POST['prezzo']) && $_POST['prezzo'] !== '' ? (float)$_POST['prezzo'] : null;

        // Query SQL aggiornata per includere il prezzo
        $sql_barca = "INSERT INTO barche 
            (marca, modello, lunghezza, omologazione, motore, carburante, propulsione, descrizione, anno, stato, prezzo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_barca = $conn->prepare($sql_barca);
        if (!$stmt_barca) {
            throw new Exception("Errore nella preparazione della query per la barca.");
        }
        
        // Aggiornato bind_param con il nuovo campo prezzo (d per decimal/double)
        $stmt_barca->bind_param("ssssssssiss", 
            $marca, $modello, $lunghezza, $omologazione, $motore, 
            $carburante, $propulsione, $descrizione, $anno,
            $stato, $prezzo
        );
        $stmt_barca->execute();
        $stmt_barca->close();

        // --- FASE 2: RECUPERA L'ID DELLA BARCA APPENA INSERITA ---
        // Questa parte non cambia
        $last_barca_id = $conn->insert_id;

        // --- FASE 3: GESTISCI I FILE MULTIMEDIALI ---
        // Anche questa parte rimane identica
        $sql_media = "INSERT INTO risorse_multimediali (id_barca_fk, percorso_file, tipo_media) VALUES (?, ?, ?)";
        $stmt_media = $conn->prepare($sql_media);
        if (!$stmt_media) {
            throw new Exception("Errore nella preparazione della query per i media.");
        }

        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        function handle_file_upload($file_info, $barca_id, $tipo_media, $stmt, $uploadDir) {
            // Controlli di sicurezza
            if (empty($file_info['name']) || empty($file_info['tmp_name'])) {
                error_log("File info incompleto: " . print_r($file_info, true));
                return false;
            }
            
            // Controlla la dimensione del file (128MB in bytes)
            $max_size = 128 * 1024 * 1024;
            if ($file_info['size'] > $max_size) {
                error_log("File troppo grande: " . $file_info['name'] . " (" . ($file_info['size']/1024/1024) . "MB)");
                throw new Exception("Il file " . $file_info['name'] . " supera il limite di 128MB");
            }

            // Sanitizza il nome del file
            $fileName = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "", basename($file_info["name"]));
            $filePath = $uploadDir . $fileName;
            
            // Verifica che sia un upload valido
            if (!is_uploaded_file($file_info["tmp_name"])) {
                error_log("File non valido: " . $file_info["name"]);
                return false;
            }

            // Sposta il file e inserisci nel DB
            if (move_uploaded_file($file_info["tmp_name"], $filePath)) {
                try {
                    $stmt->bind_param("iss", $barca_id, $filePath, $tipo_media);
                    $success = $stmt->execute();
                    if (!$success) {
                        error_log("Errore nell'inserimento del file nel DB: " . $stmt->error);
                        unlink($filePath); // Rimuove il file se l'inserimento nel DB fallisce
                        return false;
                    }
                    return true;
                } catch (Exception $e) {
                    error_log("Errore durante l'upload: " . $e->getMessage());
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    return false;
                }
            }
            
            error_log("Fallimento nel move_uploaded_file per: " . $filePath);
            return false;
        }
        
        // Gestione upload multiple immagini
        if (isset($_FILES['foto'])) {
            // Debug info
            error_log("Processing photos: " . print_r($_FILES['foto'], true));
            
            if (is_array($_FILES['foto']['name'])) {
                foreach ($_FILES['foto']['name'] as $key => $name) {
                    // Salta file vuoti o con errori
                    if ($_FILES['foto']['error'][$key] !== UPLOAD_ERR_OK || empty($_FILES['foto']['name'][$key])) {
                        continue;
                    }
                    
                    $file_info = [
                        'name' => $_FILES['foto']['name'][$key],
                        'type' => $_FILES['foto']['type'][$key],
                        'tmp_name' => $_FILES['foto']['tmp_name'][$key],
                        'error' => $_FILES['foto']['error'][$key],
                        'size' => $_FILES['foto']['size'][$key]
                    ];
                    
                    if ($file_info['size'] > 0) {
                        handle_file_upload($file_info, $last_barca_id, 'immagine', $stmt_media, $uploadDir);
                    }
                }
            }
        }

        // Gestione upload multiple video
        if (isset($_FILES['video'])) {
            // Debug info
            error_log("Processing videos: " . print_r($_FILES['video'], true));
            
            if (is_array($_FILES['video']['name'])) {
                foreach ($_FILES['video']['name'] as $key => $name) {
                    // Salta file vuoti o con errori
                    if ($_FILES['video']['error'][$key] !== UPLOAD_ERR_OK || empty($_FILES['video']['name'][$key])) {
                        continue;
                    }
                    
                    $file_info = [
                        'name' => $_FILES['video']['name'][$key],
                        'type' => $_FILES['video']['type'][$key],
                        'tmp_name' => $_FILES['video']['tmp_name'][$key],
                        'error' => $_FILES['video']['error'][$key],
                        'size' => $_FILES['video']['size'][$key]
                    ];
                    
                    if ($file_info['size'] > 0) {
                        handle_file_upload($file_info, $last_barca_id, 'video', $stmt_media, $uploadDir);
                    }
                }
            }
        }
        
        $stmt_media->close();

        // Se tutto è andato bene, conferma la transazione
        $conn->commit();

        // Messaggio di successo (invariato)
        echo "
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#caf0f8; text-align:center; padding:20px; }
            .btn-back { display:inline-block; margin-top:20px; padding:12px 25px; font-size:16px; font-weight:bold; color:white; text-decoration:none; border-radius:12px; background: linear-gradient(135deg, #023e8a, #0077b6); transition: all 0.3s ease; }
            .btn-back:hover { background: linear-gradient(135deg, #0077b6, #023e8a); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        </style>
        <h2 style='color: green;'>✅ Barca e file inseriti con successo!</h2>
        <a href='admin.php' class='btn-back'>← Torna al modulo</a>
        ";

    } catch (Exception $e) {
        // Se qualcosa è andato storto, annulla la transazione
        $conn->rollback();
        echo "<h2 style='color: red; text-align:center;'>❌ Errore durante l'inserimento: " . $e->getMessage() . "</h2>";
    
    } finally {
        // Chiudi la connessione
        $conn->close();
    }
}
?>