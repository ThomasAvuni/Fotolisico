<?php
require_once "connection.php";

// Verifica se è stato fornito un ID
if (!isset($_GET['id'])) {
    die('ID barca non fornito');
}

$id = (int)$_GET['id'];

// Query per ottenere i dati della barca
$query = "SELECT * FROM barche WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Barca non trovata');
}

$barca = $result->fetch_assoc();

// Query per ottenere le risorse multimediali
$query_media = "SELECT * FROM risorse_multimediali WHERE id_barca_fk = ?";
$stmt_media = $conn->prepare($query_media);
$stmt_media->bind_param("i", $id);
$stmt_media->execute();
$result_media = $stmt_media->get_result();

$media = [
    'immagini' => [],
    'video' => []
];

while ($row = $result_media->fetch_assoc()) {
    if ($row['tipo_media'] === 'immagine') {
        $media['immagini'][] = $row;
    } else {
        $media['video'][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Barca</title>
    <style>
        /* Riutilizzo lo stesso stile di admin.php */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0077b6, #00b4d8);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            backdrop-filter: blur(10px);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 26px;
            color: #fff;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #fff;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.3);
            margin-top: 5px;
            font-size: 14px;
            background-color: rgba(255,255,255,0.2);
            color: #fff;
            box-sizing: border-box;
        }
        
        input::placeholder, textarea::placeholder { 
            color: rgba(255,255,255,0.7); 
        }

        .radio-group {
            background-color: rgba(255,255,255,0.2);
            padding: 10px;
            border-radius: 8px;
            margin-top: 5px;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .radio-group label {
            margin-top: 5px;
            font-weight: normal;
        }

        textarea {
            height: 80px;
            resize: vertical;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 30px;
        }

        input[type="submit"],
        .button {
            flex: 1;
            background: linear-gradient(135deg, #023e8a, #0077b6);
            color: white;
            text-align: center;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        input[type="submit"]:hover,
        .button:hover {
            background: linear-gradient(135deg, #0077b6, #023e8a);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .media-preview {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }

        .media-item {
            position: relative;
            aspect-ratio: 1;
        }

        .media-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .media-item .delete-media {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 14px;
            line-height: 20px;
            cursor: pointer;
            padding: 0;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        input[type="number"] {
            -moz-appearance: textfield;
            appearance: textfield;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Modifica Barca</h2>
        <form action="update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" value="<?= htmlspecialchars($barca['marca']) ?>" required>

            <label for="modello">Modello:</label>
            <input type="text" id="modello" name="modello" value="<?= htmlspecialchars($barca['modello']) ?>" required>

            <label for="lunghezza">Lunghezza (es. 7.50):</label>
            <input type="text" id="lunghezza" name="lunghezza" value="<?= htmlspecialchars($barca['lunghezza']) ?>" required>

            <label for="anno">Anno:</label>
            <input type="number" id="anno" name="anno" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($barca['anno']) ?>">

            <label for="omologazione">Omologazione:</label>
            <input type="text" id="omologazione" name="omologazione" value="<?= htmlspecialchars($barca['omologazione']) ?>">

            <label for="motore">Motore:</label>
            <input type="text" id="motore" name="motore" value="<?= htmlspecialchars($barca['motore']) ?>">
            
            <label>Carburante:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="carburante" value="Benzina" <?= $barca['carburante'] === 'Benzina' ? 'checked' : '' ?> required> Benzina
                </label>
                <label>
                    <input type="radio" name="carburante" value="Diesel" <?= $barca['carburante'] === 'Diesel' ? 'checked' : '' ?>> Diesel
                </label>
                <label>
                    <input type="radio" name="carburante" value="Elettrico" <?= $barca['carburante'] === 'Elettrico' ? 'checked' : '' ?>> Elettrico
                </label>
            </div>

            <label for="propulsione">Propulsione:</label>
            <input type="text" id="propulsione" name="propulsione" value="<?= htmlspecialchars($barca['propulsione']) ?>">

            <label>Stato:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="stato" value="in vendita" <?= $barca['stato'] === 'in vendita' ? 'checked' : '' ?> required> In vendita
                </label>
                <label>
                    <input type="radio" name="stato" value="da noleggiare" <?= $barca['stato'] === 'da noleggiare' ? 'checked' : '' ?>> Da Noleggiare
                </label>
            </div>

            <label for="prezzo">Prezzo (€):</label>
            <input type="number" id="prezzo" name="prezzo" min="0" step="1000" value="<?= htmlspecialchars($barca['prezzo']) ?>">

            <label for="descrizione">Descrizione:</label>
            <textarea id="descrizione" name="descrizione"><?= htmlspecialchars($barca['descrizione']) ?></textarea>

            <!-- Media esistenti -->
            <?php if (!empty($media['immagini'])): ?>
                <label>Immagini attuali:</label>
                <div class="media-preview">
                    <?php foreach ($media['immagini'] as $img): ?>
                        <div class="media-item">
                            <img src="<?= htmlspecialchars($img['percorso_file']) ?>" alt="Immagine barca">
                            <button type="button" class="delete-media" onclick="if(confirm('Eliminare questa immagine?')) deleteMedia(<?= $img['id_media'] ?>)">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($media['video'])): ?>
                <label>Video attuali:</label>
                <div class="media-preview">
                    <?php foreach ($media['video'] as $vid): ?>
                        <div class="media-item">
                            <video width="100" height="100" controls>
                                <source src="<?= htmlspecialchars($vid['percorso_file']) ?>" type="video/mp4">
                            </video>
                            <button type="button" class="delete-media" onclick="if(confirm('Eliminare questo video?')) deleteMedia(<?= $vid['id_media'] ?>)">×</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label for="foto">Aggiungi altre foto:</label>
            <input type="file" id="foto" name="foto[]" accept="image/*" multiple>

            <label for="video">Aggiungi altri video:</label>
            <input type="file" id="video" name="video[]" accept="video/*" multiple>

            <div class="buttons">
                <input type="submit" value="Salva Modifiche">
                <a href="lista.php" class="button">Torna alla Lista</a>
            </div>
        </form>
    </div>

    <script>
    function deleteMedia(mediaId) {
        fetch('delete_media.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_media=' + mediaId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Rimuovi l'elemento dal DOM
                const mediaElement = document.querySelector(`button[onclick*="${mediaId}"]`).parentNode;
                mediaElement.remove();
            } else {
                alert('Errore durante l\'eliminazione del media');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Errore durante l\'eliminazione del media');
        });
    }
    </script>
</body>
</html>
<?php
$conn->close();
?>