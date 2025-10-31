<?php
// dettaglio_barca.php
// Mostra immagini, video e specifiche della singola barca
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "connection.php";

// Prendi l'id dalla querystring e validalo
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo "<p>ID barca non valido. <a href=\"index.php\">Torna alla home</a></p>";
    exit;
}

// Recupera i dettagli della barca in modo sicuro
$stmt = $conn->prepare("SELECT * FROM barche WHERE id = ? LIMIT 1");
if (!$stmt) {
    echo "Errore DB: " . htmlspecialchars($conn->error);
    exit;
}
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$barca = $res->fetch_assoc();
$stmt->close();

if (!$barca) {
    http_response_code(404);
    echo "<p>Barca non trovata. <a href=\"index.php\">Torna alla home</a></p>";
    exit;
}

// Recupera media (immagini e video) associati alla barca
$mediaStmt = $conn->prepare("SELECT percorso_file, tipo_media FROM risorse_multimediali WHERE id_barca_fk = ? ORDER BY id_media ASC");
if (!$mediaStmt) {
    echo "Errore DB: " . htmlspecialchars($conn->error);
    exit;
}
$mediaStmt->bind_param('i', $id);
$mediaStmt->execute();
$mediaRes = $mediaStmt->get_result();
$immagini = [];
$video = [];
while ($m = $mediaRes->fetch_assoc()) {
    if (isset($m['tipo_media']) && $m['tipo_media'] === 'video') {
        $video[] = $m['percorso_file'];
    } else {
        $immagini[] = $m['percorso_file'];
    }
}
$mediaStmt->close();

// Helper per stampare in modo sicuro
function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dettaglio - <?= h($barca['marca'] . ' ' . $barca['modello']) ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #f8fafc;
            font-family: 'Inter', -apple-system, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .back {
            display: inline-flex;
            align-items: center;
            margin: 1rem 0;
            color: #1e40af;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .back:hover {
            color: #2563eb;
            transform: translateX(-4px);
        }
        .header {
            background: linear-gradient(to right, #1e40af, #3b82f6);
            padding: 2rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .title {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .meta {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }
        .specs {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        .specs ul {
            list-style: none;
            padding: 0;
        }
        .specs li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
        }
        .specs li:last-child {
            border-bottom: none;
        }
        .label {
            color: #1e40af;
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding: 0.5rem;
            background: rgba(0,0,0,0.03);
            border-radius: 0.75rem;
        }
        .gallery img, .gallery video {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            opacity: 0.7;
            border: 2px solid transparent;
        }
        .gallery img:hover, .gallery video:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 1;
        }
        .gallery img.active, .gallery video.active {
            border-color: #3b82f6;
            opacity: 1;
        }
        .main-media {
            background: white;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        .main-media img, .main-media video {
            width: 100%;
            max-height: 600px;
            object-fit: contain;
            border-radius: 0.5rem;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }
        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: 1fr;
            }
            .header {
                padding: 1.5rem;
            }
            .title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <a class="back" href="javascript:history.back()">← Torna indietro</a>
    <div class="header">
        <div class="title"><?= h($barca['marca'] . ' ' . $barca['modello']) ?></div>
        <div class="meta">
            <?php if (isset($barca['stato'])): ?>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold <?= $barca['stato'] === 'in vendita' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                    <?php if ($barca['stato'] === 'in vendita'): ?>
                        🏷️ In Vendita
                    <?php else: ?>
                        🔄 Da Noleggiare
                    <?php endif; ?>
                </span>
                <?php if (isset($barca['prezzo']) && $barca['prezzo'] !== ''): ?>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800 ml-3">
                        💰 <?= '€' . number_format((float)$barca['prezzo'], 0, ',', '.') ?>
                    </span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid">
        <div>
            <div class="main-media" id="mainMedia">
                <?php if (!empty($immagini)): ?>
                    <img id="current" src="<?= h($immagini[0]) ?>" alt="<?= h($barca['marca'] . ' ' . $barca['modello']) ?>" class="shadow-lg">
                <?php elseif (!empty($video)): ?>
                    <video id="currentVideo" controls src="<?= h($video[0]) ?>" class="shadow-lg"></video>
                <?php else: ?>
                    <div class="flex items-center justify-center h-96 bg-gray-100 rounded-lg">
                        <p class="text-gray-500">Nessuna immagine disponibile</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($immagini) || !empty($video)): ?>
            <div class="gallery" id="gallery">
                <?php foreach($immagini as $img): ?>
                    <img src="<?= h($img) ?>" data-type="image" data-src="<?= h($img) ?>" alt="img">
                <?php endforeach; ?>
                <?php foreach($video as $v): ?>
                    <video data-type="video" data-src="<?= h($v) ?>" muted><source src="<?= h($v) ?>"></video>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <p>Nessun media associato a questa barca.</p>
            <?php endif; ?>

        </div>

        <aside>
            <div class="specs">
                <div class="label">Specifiche tecniche</div>
                <ul>
                    <li>
                        <span>Lunghezza</span>
                        <span class="font-medium"><?= h($barca['lunghezza'] ?? 'N/D') ?> m</span>
                    </li>
                    <li>
                        <span>Motore</span>
                        <span class="font-medium"><?= h($barca['motore'] ?? 'N/D') ?></span>
                    </li>
                    <li>
                        <span>Propulsione</span>
                        <span class="font-medium"><?= h($barca['propulsione'] ?? 'N/D') ?></span>
                    </li>
                    <li>
                        <span>Carburante</span>
                        <span class="font-medium"><?= h($barca['carburante'] ?? 'N/D') ?></span>
                    </li>
                    <li>
                        <span>Anno</span>
                        <span class="font-medium"><?= h($barca['anno'] ?? 'N/D') ?></span>
                    </li>
                    <li>
                        <span>Capacità</span>
                        <span class="font-medium"><?= h($barca['omologazione'] ?? 'N/D') ?> persone</span>
                    </li>
                    <li>
                        <span>Prezzo</span>
                        <span class="font-semibold text-blue-600">
                            <?= isset($barca['prezzo']) && $barca['prezzo'] !== '' ? 
                                '€' . number_format((float)$barca['prezzo'], 0, ',', '.') : 
                                'Prezzo su richiesta' ?>
                        </span>
                    </li>
                </ul>
            </div>

            <?php if (!empty($barca['descrizione'])): ?>
                <div class="specs mt-6">
                    <div class="label">Descrizione</div>
                    <div class="mt-3 text-gray-700 leading-relaxed">
                        <?= nl2br(h($barca['descrizione'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </aside>
    </div>

</div>

<script>
    // Semplice script per cambiare il media principale quando si clicca nelle miniature
    document.addEventListener('DOMContentLoaded', function() {
        // Imposta come attiva la prima miniatura
        const firstThumb = document.querySelector('.gallery img, .gallery video');
        if (firstThumb) firstThumb.classList.add('active');

        // Gestisce i click sulle miniature
        document.addEventListener('click', function(e){
            const t = e.target;
            if (!t) return;
            const type = t.getAttribute && t.getAttribute('data-type');
            const src = t.getAttribute && t.getAttribute('data-src');
            if (!type || !src) return;

            // Rimuove active da tutte le miniature
            document.querySelectorAll('.gallery img, .gallery video').forEach(el => {
                el.classList.remove('active');
            });
            // Aggiunge active alla miniatura cliccata
            t.classList.add('active');

            // Aggiorna il media principale
            const main = document.getElementById('mainMedia');
            if (type === 'image') {
                main.innerHTML = '<img id="current" src="'+src+'" alt="media" class="shadow-lg">';
            } else if (type === 'video') {
                main.innerHTML = '<video id="currentVideo" controls src="'+src+'" class="shadow-lg"></video>';
            }
        });
    });
</script>

</body>
</html>

<?php
$conn->close();
?>
