<?php
// 1. Detectar si estem dins de la carpeta 'logat/' o a l'arrel per incloure la ruta correcta de l'auth.php
$ruta_actual = $_SERVER['REQUEST_URI'];
$es_subcarpeta = (strpos($ruta_actual, '/logat/') !== false);

$ruta_auth = $es_subcarpeta ? 'auth.php' : 'logat/auth.php';
require_once($ruta_auth);

// 2. Comprovem si l'usuari està logat (ens retornarà el payload o false)
$usuari_actiu = validarToken();

// 3. Definim els enllaços correctes depenent d'on es trobi el fitxer en aquell moment
$link_index = $es_subcarpeta ? '../index.php' : 'index.php';
$link_gestio = $es_subcarpeta ? '../gestionarProductes.php' : 'gestionarProductes.php';
$link_login = $es_subcarpeta ? 'login.php' : 'logat/login.php';
$link_register = $es_subcarpeta ? 'registrar.php' : 'logat/registrar.php';
$link_logout = $es_subcarpeta ? 'logout.php' : 'logat/logout.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $link_index; ?>">🛠️ Biblioteca de les Coses</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $link_index; ?>">Catàleg</a>
                </li>
                <?php if ($usuari_actiu): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $link_gestio; ?>">La meva Gestió</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if ($usuari_actiu): ?>
                    <li class="nav-item me-3 text-white">
                        <span>Hola, <strong class="text-warning"><?php echo htmlspecialchars($usuari_actiu['nom']); ?></strong> (📍 <?php echo htmlspecialchars($usuari_actiu['rol'] ?? 'user'); ?>)</span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3" href="<?php echo $link_logout; ?>">Tancar Sessió (Logout)</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="<?php echo $link_login; ?>">Iniciar Sessió</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm px-3 text-dark fw-bold" href="<?php echo $link_register; ?>">Registrar-me</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>