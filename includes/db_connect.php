<?php
// __DIR__ és la carpeta 'includes'. 
// Pugem un nivell i entrem a 'database'.
$ruta_db = __DIR__ . '/../database/tools.db';
$db = new SQLite3($ruta_db);