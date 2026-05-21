<?php
// 1. Incloem l'auth.php utilitzant la ruta absoluta del servidor (__DIR__) 
// per assegurar-nos que troba el fitxer estiguem on estiguem
require_once __DIR__ . '/../logat/auth.php';

// 2. Cridem la teva funció per saber si l'usuari està logat
$usuariLoguejat = validarToken();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool Sharing - ODS 12</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/estils/css.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                🛠️  <strong>Tool Sharing</strong>
            </a>
            
            <div class="d-flex align-items-center gap-2">
                <?php if ($usuariLoguejat): ?>
                    <span class="text-white me-2 small">Hola, <strong><?php echo htmlspecialchars($usuariLoguejat['nom']); ?></strong></span>
                    <a href="gestionarProductes.php" class="btn btn-outline-success btn-sm">Gestionar productes</a>
                    <a href="logat/logout.php" class="btn btn-danger btn-sm">Log out</a>
                <?php else: ?>
                    <a href="logat/login.php" class="btn btn-outline-light btn-sm me-2">Iniciar Sessió</a>
                    <a href="logat/registrar.php" class="btn btn-light btn-sm">Registrar-se</a>
                <?php endif; ?>
            </div>
            
        </div>
    </nav>