<?php
// Mostra errori per un facile debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "connection.php";

// Query ottimizzata per prendere le barche e la loro prima immagine
$query = "
    SELECT 
        b.*,  -- Prendi tutti i dati dalla tabella barche
        (SELECT rm.percorso_file 
         FROM risorse_multimediali rm 
         WHERE rm.id_barca_fk = b.id AND rm.tipo_media = 'immagine' 
         ORDER BY rm.id_media ASC LIMIT 1) AS foto_principale
    FROM 
        barche b
    ORDER BY 
        b.id DESC
    LIMIT 6; -- Mostra solo le ultime 6 barche per non appesantire la homepage
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<!-- Christian Schupffer Davide Opera Thomas -->
<html lang="it">
<head>
    <meta charset="UTF-8">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/svg" href="dreeam_day_bianco.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DreamDay:noleggia o acquista barche per escursioni ed eventi estivi in Campania e Italia. Scopri la nostra flottae vivi grandi esperienze per mare ">
    <meta name="keywords" content="noleggio, noleggio barche, vendita barche, barche, barca, gommoni, dreamday, gommoni, Napoli, Italia, yatch, barche in vendita">
    <meta name="robots" content="index,follow">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-traslucent">
    <meta property="og:title" content="Dreamday Napoli – Noleggio e Vendita Barche ">
    <meta property="og:description" content="Scopri la flotta Dreamday Napoli: noleggio barche, yacht e gommoni per vivere la Campania dal mare.">
    <meta property="og:image" content="https://forzanapolieferrari.altervista.org/logo-ufficiale.png">
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="500">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="Logo ufficiale Dreamday Napoli">
    <meta property="og:url" content="https://forzanapolieferrari.altervista.org/index.html">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="it_IT">
    <meta property="og:site_name" content="DreamDay">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>DreamDay - Esplora le tue barche</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="text-gray-800">

<!-- NAVBAR -->
<nav class="navbar">
    <a href="#home">
        <div class="navbar-brand">
            DreamDay
            <img src="logo-ufficiale.png" id="Logo" class="logo" alt="DreamDay Logo">
        </div>
    </a>
    <div class="relative">
        <button id="dropdown-toggle" class="flex items-center text-white hover:text-blue-300 focus:outline-none">
            <span class="mr-2">Menu</span>
            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div id="dropdown-menu" class="absolute right-0 mt-2 w-40 bg-blue-900 text-white rounded-lg shadow-lg hidden flex-col dropdown-anim">
            <a href="index.php" class="block px-4 py-2 hover:bg-blue-700 rounded-t-lg">Home</a>
            <div class="relative group">
                <button id="sub-toggle" class="block w-full text-left px-4 py-2 hover:bg-blue-700 flex justify-between items-center">
                    Barche
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                <div id="sub-menu" class="absolute right-full top-0 ml-1 w-40 bg-blue-900 rounded-lg shadow-lg hidden flex-col dropdown-anim">
                    <a href="catalogo_noleggio.php" class="block px-4 py-2 hover:bg-blue-700 rounded-t-lg">Noleggio</a>
                    <a href="catalogo.php" class="block px-4 py-2 hover:bg-blue-700 rounded-b-lg">Acquisto</a>
                </div>
            </div>
            <a href="messaggistica.html" class="block px-4 py-2 hover:bg-blue-700">Contatti</a>
            <a href="#about" class="block px-4 py-2 hover:bg-blue-700 rounded-b-lg">Chi Siamo</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section id="home" class="hero-bg flex flex-col items-center justify-center">
    <video id="video" autoplay muted loop playsinline>
        <source src="video_principale.mp4" type="video/mp4">
    </video>
    <div class="content">
        <h1 class="text-4xl sm:text-6xl font-extrabold mb-4">Scopri la barca dei tuoi sogni!</h1>
        <p class="text-lg sm:text-xl mb-6">Naviga nel lusso e trova l'imbarcazione perfetta per te.</p>
        <div class="container-vendesi-btn">
            <a href="catalogo.php"><button class="vendesi-btn"><svg class="svgIcon" viewBox="0 0 512 512" height="1em" xmlns="http://www.w3.org/2000/svg"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm50.7-186.9L162.4 380.6c-19.4 7.5-38.5-11.6-31-31l55.5-144.3c3.3-8.5 9.9-15.1 18.4-18.4l144.3-55.5c19.4-7.5 38.5 11.6 31 31L325.1 306.7c-3.2 8.5-9.9 15.1-18.4 18.4zM288 256a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"></path></svg> Acquista</button></a>
            <a href="catalogo_noleggio.php"><button class="vendesi-btn"><svg class="svgIcon" viewBox="0 0 512 512" height="1em" xmlns="http://www.w3.org/2000/svg"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm50.7-186.9L162.4 380.6c-19.4 7.5-38.5-11.6-31-31l55.5-144.3c3.3-8.5 9.9-15.1 18.4-18.4l144.3-55.5c19.4-7.5 38.5 11.6 31 31L325.1 306.7c-3.2 8.5-9.9 15.1-18.4 18.4zM288 256a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"></path></svg> Noleggia</button></a>
            <a href="messaggistica.html"><button class="vendesi-btn"><svg class="svgIcon" viewBox="0 0 24 24" height="1em" xmlns="http://www.w3.org/2000/svg"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"/></svg>Contatti</button></a>
        </div>
    </div>
</section>

<!-- SEZIONE BARCHE -->
<section id="boats" class="py-16 px-4 sm:px-6 bg-white">
    <h2 class="text-3xl font-bold text-center text-blue-900 mb-12">Le nostre barche</h2>
    <div class="relative max-w-7xl mx-auto">
        <div id="carousel" class="carousel">
            <!-- Freccia destra -->
            <div onclick="scrollCarousel('right')" class="arrow-right">
                <div class="box-1"><div class="arrow right"></div></div>
            </div>
            <!-- Freccia sinistra -->
            <div onclick="scrollCarousel('left')" class="arrow-left">
                <div class="box-2"><div class="arrow left"></div></div>
            </div>

            <!-- INIZIO CICLO PHP PER GENERARE LE CARD -->
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        // Prepara i dati per una facile lettura, con fallback
                        $id_barca = $row['id'];
                        $nome_barca = htmlspecialchars($row['marca'] . ' ' . $row['modello']);
                        $immagine_src = !empty($row['foto_principale']) ? htmlspecialchars($row['foto_principale']) : 'placeholder.jpg'; // Assicurati di avere un'immagine placeholder.jpg
                        $prezzo = isset($row['prezzo']) && !empty($row['prezzo']) ? '€' . number_format($row['prezzo'], 0, ',', '.') : 'Prezzo su richiesta';
                        $link_dettaglio = 'dettaglio_barca.php?id=' . $id_barca;
                    ?>
                    <div class="card bg-white rounded-lg shadow-lg overflow-hidden cursor-pointer" onclick="window.location.href='<?= $link_dettaglio ?>'">
                        <img src="<?= $immagine_src ?>" alt="<?= $nome_barca ?>" class="w-full aspect-video object-cover">
                        <div class="p-5">
                            <h3 class="text-xl font-semibold text-blue-900"><?= $nome_barca ?></h3>
                            <p class="text-gray-600 mt-2"><?= htmlspecialchars($row['lunghezza']) ?></p>
                            <p class="text-blue-600 font-bold mt-4"><?= $prezzo ?></p>
                            <button onclick="event.stopPropagation();toggleDetails(<?= $id_barca ?>)" class="mt-3 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Dettagli</button>
                            <div id="details-<?= $id_barca ?>" class="boat-details mt-3 bg-blue-50 p-3 rounded">
                                <div class="flex items-center mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $row['stato'] === 'in vendita' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' ?>">
                                        <?php if ($row['stato'] === 'in vendita'): ?>
                                            🏷️ In Vendita
                                        <?php else: ?>
                                            🔄 Da Noleggiare
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <h4 class="font-semibold text-blue-900 mb-1">Dettagli Tecnici</h4>
                                <ul class="list-disc pl-4 text-gray-700 text-sm">
                                    <li>Lunghezza: <?= htmlspecialchars($row['lunghezza']) ?>m</li>
                                    <li>Motore: <?= htmlspecialchars($row['motore']) ?></li>
                                    <li>Capacità: <?= htmlspecialchars($row['omologazione']) ?> persone</li>
                                    <li>Anno: <?= htmlspecialchars($row['anno']) ?></li>
                                </ul>
                                <button onclick="event.stopPropagation();toggleDetails(<?= $id_barca ?>)" class="mt-2 text-blue-600 underline">Chiudi</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="w-full text-center text-gray-500">Nessuna barca in evidenza al momento.</p>
            <?php endif; ?>
            <!-- FINE CICLO PHP -->

        </div>
    </div>

    <div class="catalogo-btn">
        <a href="catalogo.php"><button class="learn-more">Catalogo Acquisti</button></a>
        <a href="catalogo_noleggio.php"><button class="learn-more">Catalogo Noleggi</button></a>
    </div>
</section>

<!-- IL RESTO DELLA PAGINA (Contattaci, Chi Siamo, Footer) RESTA INVARIATO -->
<!-- Contattaci -->
<section id="contact" class="py-16 px-4 sm:px-6 bg-blue-900 text-white">
    <div class="max-w-4xl mx-auto text-center">
        <div class="flex items-center justify-center mb-12 gap-4">
            <h2 class="text-3xl font-bold">Contatti</h2>
            <!-- SVG email -->
        </div>
        <div class="contact-text mb-8"><p class="text-xl">Hai domande o vuoi informazioni su una barca? Scrivici!</p></div>
        <a href="messaggistica.html" class="cta"><span>Contatti&nbsp;</span><svg viewBox="0 0 13 10" height="10px" width="15px" xmlns="http://www.w3.org/2000/svg"><path d="M1,5 L11,5"></path><polyline points="8 1 12 5 8 9"></polyline></svg></a>
    </div>
</section>
<!-- LINEA -->
<div class="section-divider"></div>
<!-- CHI SIAMO -->
<section id="about" class="section-title">
    <h2 class="section-title">Chi Siamo</h2>
    <p class="about-text">DreamDay è una piattaforma professionale per la compravendita di barche. La nostra missione è connettere i migliori costruttori e rivenditori con appassionati di mare da tutto il mondo.</p>
</section>
<!-- FOOTER -->
<footer class="bg-blue-900 text-white py-6 px-4 text-center">
    <div class="copyright">
        © <span id="year"></span> Daydream <br> Riproduzione, distribuzione e utilizzo dei contenuti <br> sono vietati senza previa autorizzazione.
        <div class="footer-links">
            <a href="privacy-policy.html">Privacy Policy</a> |
            <a href="term-of-service.html">Termini di Servizio</a>
        </div>
    </div>
</footer>

<!-- SCRIPT -->
<script>
    // Funzione per gestire i link esterni
    document.addEventListener('DOMContentLoaded', function() {
        // Ottiene tutti i link
        const links = document.getElementsByTagName('a');
        
        for (let link of links) {
            link.addEventListener('click', function(e) {
                // Controlla se il link è esterno
                if (!link.href.includes(window.location.hostname) && !link.href.startsWith('#') && !link.href.startsWith('javascript:')) {
                    if (!confirm('Stai per lasciare il sito DreamDay. Vuoi continuare?')) {
                        e.preventDefault();
                    }
                }
            });
        }
    });

    // Questo è l'UNICO JavaScript necessario per le funzionalità delle card e del menu.
    // Tutta la parte di creazione delle card è stata rimossa.

    function toggleDetails(id) {
        // Usiamo l'ID numerico passato dal PHP
        document.getElementById(`details-${id}`).classList.toggle("active");
    }

    function scrollCarousel(dir) {
        const carousel = document.getElementById('carousel');
        const card = carousel.querySelector('.card');
        if (card) {
            const scrollAmount = card.offsetWidth + 16; // 16px è il gap nel tuo CSS (1rem)
            carousel.scrollLeft += (dir === 'left' ? -scrollAmount : scrollAmount);
        }
    }

    // --- Logica per il menu dropdown (invariata) ---
    const dropdownToggle = document.getElementById("dropdown-toggle");
    const dropdownMenu = document.getElementById("dropdown-menu");
    const dropdownIcon = dropdownToggle.querySelector("svg");
    dropdownToggle.addEventListener("click", () => {
        const hidden = dropdownMenu.classList.contains("hidden");
        dropdownMenu.classList.toggle("hidden");
        dropdownIcon.classList.toggle("rotate-180");
        if (hidden) setTimeout(() => dropdownMenu.classList.add("show"), 10);
        else dropdownMenu.classList.remove("show");
    });
    document.addEventListener("click", (e) => {
        if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.add("hidden");
            dropdownMenu.classList.remove("show");
            dropdownIcon.classList.remove("rotate-180");
            subMenu.classList.add("hidden");
            subIcon.classList.remove("rotate-90");
        }
    });
    const subToggle = document.getElementById("sub-toggle");
    const subMenu = document.getElementById("sub-menu");
    const subIcon = subToggle.querySelector("svg");
    subToggle.addEventListener("click", (e) => {
        e.stopPropagation();
        subMenu.classList.toggle("hidden");
        subIcon.classList.toggle("rotate-90");
        if (!subMenu.classList.contains("hidden")) setTimeout(() => subMenu.classList.add("show"), 10);
        else subMenu.classList.remove("show");
    });

    // Imposta l'anno corrente e la logica anti-inspect
    document.getElementById("year").textContent = new Date().getFullYear();
    document.addEventListener("contextmenu", function(event) {
        alert("Inspect Elements Not Allowed");
        event.preventDefault();
    });
</script>

</body>
</html>
<?php
// Chiudi la connessione al database alla fine della pagina
if ($conn) {
    $conn->close();
}
?>