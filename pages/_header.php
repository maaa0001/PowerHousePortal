<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/static/app.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="/static/app.js" defer></script>
    <?php if (!empty($head_extras)) echo $head_extras; ?>
<title><?php echo $title ?? "PowerHousePortal"; ?></title>
</head>
<body<?php if (!empty($body_class)) echo ' class="'.htmlspecialchars($body_class).'"'; ?>>
<nav>
  <a href="/" class="nav-brand">Power<span>House</span>Portal</a>
  <div class="nav-actions">
    <ul class="nav-links">
      <li><a href="/" class="active primary-btn">Map</a></li>
    </ul>
  </div>
</nav>