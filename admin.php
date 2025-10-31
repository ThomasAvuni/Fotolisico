<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inserisci Barca</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #0077b6, #00b4d8);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px 0;
      min-height: 100vh;
      box-sizing: border-box; /* Aggiunto per un miglior layout */
    }

    .container {
      background-color: rgba(255, 255, 255, 0.1);
      padding: 30px 40px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 500px; /* Aggiunto per una migliore responsività */
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
      margin-top: 15px; /* Aumentato lo spazio */
      font-weight: bold;
      color: #fff;
    }

    input[type="text"],
    input[type="number"], /* Stile applicato anche a input number */
    textarea,
    input[type="file"] {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.3); /* Bordo sottile per visibilità */
      margin-top: 5px;
      font-size: 14px;
      background-color: rgba(255,255,255,0.2); /* Sfondo semi-trasparente */
      color: #fff; /* Colore testo bianco */
      box-sizing: border-box; /* Aggiunto per coerenza */
    }
    
    /* Placeholder color */
    input::placeholder, textarea::placeholder { color: rgba(255,255,255,0.7); }

    /* Stile per i radio button */
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
      resize: vertical; /* Permetti resize verticale */
    }

    .buttons {
      display: flex;
      justify-content: space-between;
      gap: 15px; /* Aumentato gap */
      margin-top: 30px; /* Aumentato spazio */
    }

    input[type="submit"],
    a.button {
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
    a.button:hover {
      background: linear-gradient(135deg, #0077b6, #023e8a);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Inserisci una nuova barca</h2>
    <!-- Assicurati che l'action punti al tuo file PHP e che l'enctype sia corretto -->
    <form action="form.php" method="post" enctype="multipart/form-data">
      
      <label for="marca">Marca:</label>
      <input type="text" id="marca" name="marca" required>

      <label for="modello">Modello:</label>
      <input type="text" id="modello" name="modello" required>

      <label for="lunghezza">Lunghezza (es. 7.50):</label>
      <input type="text" id="lunghezza" name="lunghezza" required>

      <label for="anno">Anno:</label>
      <input type="number" id="anno" name="anno" min="1900" max="<?php echo date('Y'); ?>" placeholder="Es. 2022">

      <label for="omologazione">Omologazione:</label>
      <input type="text" id="omologazione" name="omologazione">

      <label for="motore">Motore:</label>
      <input type="text" id="motore" name="motore">
      
      <label>Carburante:</label>
      <div class="radio-group">
        <label><input type="radio" name="carburante" value="Benzina" required> Benzina</label>
        <label><input type="radio" name="carburante" value="Diesel"> Diesel</label>
        <label><input type="radio" name="carburante" value="Elettrico"> Elettrico</label>
      </div>

      <label for="propulsione">Propulsione:</label>
      <input type="text" id="propulsione" name="propulsione">

      <!-- ================== AGGIUNTA QUI ================== -->
      <label>Stato:</label>
      <div class="radio-group">
        <!-- L'attributo 'checked' preseleziona un'opzione -->
        <label><input type="radio" name="stato" value="in vendita" required checked> In vendita</label>
        <label><input type="radio" name="stato" value="da noleggiare"> Da Noleggiare</label>
      </div>
      <!-- =================================================== -->

      <label for="prezzo">Prezzo (€):</label>
      <input type="number" id="prezzo" name="prezzo" min="0" step="1000" placeholder="Es. 150000">
      <style>
        /* Rimuovi le frecce spinbox da input number */
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

      <label for="descrizione">Descrizione:</label>
      <textarea id="descrizione" name="descrizione"></textarea>

      <label for="foto">Foto (puoi selezionarne più di una):</label>
      <input type="file" id="foto" name="foto[]" accept="image/*" multiple>

      <label for="video">Video (puoi selezionarne più di uno):</label>
      <input type="file" id="video" name="video[]" accept="video/*" multiple>

      <div class="buttons">
        <input type="submit" value="Salva Barca">
        <a href="lista.php" class="button">Mostra Tabella</a>
      </div>
    </form>
  </div>
</body>
</html>