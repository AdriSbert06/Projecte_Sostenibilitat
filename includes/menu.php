<?php
$ruta_actual = $_SERVER['REQUEST_URI'];
$es_subcarpeta = (strpos($ruta_actual, '/logat/') !== false || strpos($ruta_actual, '/productes/') !== false || strpos($ruta_actual, '/admin/') !== false);

$ruta_auth = $es_subcarpeta ? '../logat/auth.php' : 'logat/auth.php';
require_once($ruta_auth);

$usuariLoguejat = validarToken();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool Sharing - ODS 12</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $es_subcarpeta ? '../estils/css.css' : 'estils/css.css'; ?>">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow mb-5 py-3">
        <div class="container">
            
            <a class="navbar-brand d-flex align-items-center gap-2 fs-4" href="<?php echo $es_subcarpeta ? '../index.php' : 'index.php'; ?>">
                <span class="p-2 bg-success rounded-3 text-white d-inline-flex shadow-sm">🛠️</span>
                <span class="fw-bold text-white">Tool<span class="text-success">Sharing</span></span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMainContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarMainContent">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
                
                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 mt-3 mt-lg-0">
                    
                    <?php if ($usuariLoguejat): ?>
                        
                        <div class="d-flex align-items-center gap-2 px-3 py-1 bg-secondary bg-opacity-25 rounded-pill me-lg-2 mb-2 mb-lg-0 border border-secondary border-opacity-50">
                            <span class="text-white-50 small">Hola,</span>
                            <strong class="text-white small"><?php echo htmlspecialchars($usuariLoguejat['nom']); ?></strong>
                        </div>
                        
                        <a href="<?php echo $es_subcarpeta ? '../productes/gestionarProductes.php' : 'productes/gestionarProductes.php'; ?>" class="btn btn-success btn-sm px-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-box-seam"></i> Els meus productes
                        </a>
                        
                        <?php if (isset($usuariLoguejat['rol']) && $usuariLoguejat['rol'] === 'admin'): ?>
                            <div class="d-flex gap-1 bg-warning bg-opacity-10 p-1 rounded-2 border border-warning border-opacity-25 shadow-sm">
                                <a href="<?php echo $es_subcarpeta ? '../admin/usuaris.php' : 'admin/usuaris.php'; ?>" class="btn btn-warning btn-sm text-dark fw-bold px-3 d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-people-fill"></i> Usuaris
                                </a>
                                <a href="<?php echo $es_subcarpeta ? '../admin/categories.php' : 'admin/categories.php'; ?>" class="btn btn-warning btn-sm text-dark fw-bold px-3 d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-tags-fill"></i> Categories
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?php echo $es_subcarpeta ? '../logat/logout.php' : 'logat/logout.php'; ?>" class="btn btn-danger btn-sm px-3 fw-medium d-inline-flex align-items-center gap-1 border-opacity-50">
                            <i class="bi bi-box-arrow-right"></i> Sortir
                        </a>
                        
                    <?php else: ?>
                        
                        <a href="<?php echo $es_subcarpeta ? '../logat/login.php' : 'logat/login.php'; ?>" class="btn btn-outline-light btn-sm px-3 fw-medium">
                            Iniciar Sessió
                        </a>
                        <a href="<?php echo $es_subcarpeta ? '../logat/registrar.php' : 'logat/registrar.php'; ?>" class="btn btn-light btn-sm px-3 fw-bold shadow-sm">
                            Registrar-se
                        </a>
                        
                    <?php endif; ?>
                    
                </div>
                
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>