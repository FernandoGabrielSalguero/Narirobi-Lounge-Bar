<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nairobi Lounge | Carta</title>

  <!-- CSS del framework (CDN) -->
  <link rel="preload" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" as="style" />
  <link rel="stylesheet" href="https://www.fernandosalguero.com/cdn/assets/css/framework.css" />

  <!-- Material Icons (necesario para <span class="material-icons">menu</span>) -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

  <!-- JS del framework (CDN) -->
  <script defer src="https://www.fernandosalguero.com/cdn/assets/javascript/framework.js"></script>
</head>
<body>
  <?php require __DIR__ . '/views/public/header_view.php'; ?>
  <?php require __DIR__ . '/views/public/body_view.php'; ?>

  <!-- JS del header (controla login + reservas y carga modales por fetch) -->
  <script defer src="/controllers/header_controller.php?action=script"></script>
</body>
</html>
