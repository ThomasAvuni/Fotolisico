<?php
require_once "connection.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. QUERY AGGIORNATA: Aggiunto il campo b.stato
$query = "
    SELECT 
        b.id AS id_barca, b.marca, b.modello, b.lunghezza, b.motore, 
        b.carburante, b.descrizione, b.stato, b.prezzo,
        rm.id_media, rm.percorso_file, rm.tipo_media
    FROM 
        barche AS b
    LEFT JOIN 
        risorse_multimediali AS rm ON b.id = rm.id_barca_fk
    ORDER BY 
        b.id DESC
";

$result = $conn->query($query);

$barche = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $barcaId = $row['id_barca'];

        if (!isset($barche[$barcaId])) {
            // 2. ARRAY AGGIORNATO: Salviamo lo stato della barca
            $barche[$barcaId] = [
                'id'          => $barcaId,
                'marca'       => $row['marca'],
                'modello'     => $row['modello'],
                'lunghezza'   => $row['lunghezza'],
                'motore'      => $row['motore'],
                'carburante'  => $row['carburante'],
                'descrizione' => $row['descrizione'],
                'stato'       => $row['stato'],
                'prezzo'      => $row['prezzo'],
                'immagini'    => [],
                'video'       => []
            ];
        }

        if ($row['percorso_file']) {
            if ($row['tipo_media'] === 'immagine') {
                $barche[$barcaId]['immagini'][] = $row['percorso_file'];
            } elseif ($row['tipo_media'] === 'video') {
                $barche[$barcaId]['video'][] = $row['percorso_file'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista Barche</title>
  <style>
    /* ... il tuo CSS rimane invariato ... */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #caf0f8;
      margin: 0;
      padding: 20px;
      color: #03045e;
    }
    h1 { text-align: center; margin-bottom: 20px; font-size: 28px; }
    .table-container { overflow-x: auto; }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      font-size: 14px;
    }
    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; vertical-align: middle; } /* Aggiunto vertical-align */
    th { background-color: #00b4d8; color: white; }
    tr:hover { background-color: #e0f7fa; }
    .media-cell img {
        width: 100px;
        height: 70px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 5px;
        border: 1px solid #ddd;
    }
    .media-cell .video-link {
        display: block;
        margin-top: 5px;
    }
    a { color: #0077b6; text-decoration: none; font-weight: bold; }
    a:hover { text-decoration: underline; }
    .delete-link { color: #d00000 !important; }
    .back { display: block; width: fit-content; margin: 25px auto; text-align: center; background: linear-gradient(135deg, #023e8a, #0077b6); color: white; padding: 12px 25px; border-radius: 12px; text-decoration: none; font-size: 16px; font-weight: bold; transition: all 0.3s ease; }
    .back:hover { background: linear-gradient(135deg, #0077b6, #023e8a); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }

    /* NUOVO STILE PER IL BADGE DELLO STATO */
    .status-badge {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: bold;
      color: white;
      text-transform: capitalize;
    }
    .status-vendita { background-color: #0077b6; }
    .status-noleggiato { background-color: #e63946; }

  </style>
</head>
<body>
  <h1>Elenco delle Barche</h1>

  <?php if (!empty($barche)): ?>
    <div class="table-container">
      <table>
        <thead>
            <tr>
              <th>ID</th>
              <th>Marca</th>
              <th>Modello</th>
              <th>Stato</th>
              <th>Prezzo</th>
              <th>Lunghezza</th>
              <th>Foto Principale</th>
              <th>Altri Media</th>
              <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($barche as $barca): ?>
            <tr>
              <td><?= htmlspecialchars($barca['id']) ?></td>
              <td><?= htmlspecialchars($barca['marca']) ?></td>
              <td><?= htmlspecialchars($barca['modello']) ?></td>
              <td>
                <?php
                  $statusClass = $barca['stato'] === 'in vendita' ? 'status-vendita' : 'status-noleggiato';
                  $statusText = $barca['stato'] ? $barca['stato'] : 'Non definito';
                  echo '<span class="status-badge ' . $statusClass . '">' . htmlspecialchars($statusText) . '</span>';
                ?>
              </td>
              <td class="text-blue-600 font-semibold">
                <?= isset($barca['prezzo']) && $barca['prezzo'] !== '' ? 
                    '€' . number_format((float)$barca['prezzo'], 0, ',', '.') : 
                    '<span class="text-gray-600">Su richiesta</span>' ?>
              </td>
              <td><?= htmlspecialchars($barca['lunghezza']) ?> m</td>
              <td class="media-cell">
                <?php if (!empty($barca['immagini'])): ?>
                  <img src="<?= htmlspecialchars($barca['immagini'][0]) ?>" alt="Foto Barca">
                <?php else: ?>
                  Nessuna foto
                <?php endif; ?>
              </td>
              <td class="media-cell">
                <?php 
                    $altre_immagini = count($barca['immagini']) > 1 ? count($barca['immagini']) - 1 : 0;
                    if ($altre_immagini > 0) {
                        echo "<strong>+{$altre_immagini} altre foto</strong><br>";
                    }
                    if (!empty($barca['video'])) {
                        foreach ($barca['video'] as $index => $videoPath) {
                            echo '<a href="' . htmlspecialchars($videoPath) . '" target="_blank" class="video-link">Guarda video ' . ($index + 1) . '</a>';
                        }
                    }
                ?>
              </td>
              <td>
                <a href="modifica_barca.php?id=<?= $barca['id'] ?>" class="edit-link" style="margin-right: 15px;">Modifica</a>
                <a href="delete.php?id=<?= $barca['id'] ?>" onclick="return confirm('Sei sicuro di voler eliminare questa barca? Verranno cancellati anche tutti i file associati.');" class="delete-link">Elimina</a>
              </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="text-align:center;">Nessuna barca trovata nel database.</p>
  <?php endif; ?>

  <a href="admin.php" class="back">← Inserisci Nuova Barca</a>

</body>
</html>

<?php
$conn->close();
?>