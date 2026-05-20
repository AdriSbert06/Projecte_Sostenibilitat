<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de les Coses BCN - ODS 12</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --verde-sostenible:rgb(255, 123, 0);
            --verde-claro: #e8f5e9;
        }
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: var(--verde-sostenible) !important;
        }
        .hero-section {
            background-color: var(--verde-claro);
            padding: 40px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .card-obj {
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-obj:hover {
            transform: translateY(-5px);
        }
        .badge-categoria {
            background-color: #6c757d;
        }
        .badge-gratis {
            background-color: var(--verde-sostenible);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                🌱 <strong>Biblioteca de les Coses</strong>
            </a>
            <div class="d-flex">
    <a href="logat/login.php" class="btn btn-outline-light btn-sm me-2">Iniciar Sessió</a>
    <a href="#" class="btn btn-light btn-sm">Registrar-se</a>
</div>
        </div>
    </nav>