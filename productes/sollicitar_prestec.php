<?php
    require_once __DIR__ . '/../logat/auth.php';
    $usuari = validarToken();

    // Si no està logat es redirigeix a login.php
    if (!$usuari) {
        header("Location: ../logat/login.php");
        exit;
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: ../index.php');
        exit;
    }

    $objecteId = intval($_GET['id']);
    $usuariId = intval($usuari['id']);

    include_once '../includes/db_connect.php';

    // 4. Comprovar si l'objecte està realment disponible abans de fer la reserva
    $estat_actual = $db->querySingle("SELECT obj_estat FROM objectes WHERE obj_id = $objecteId");

    if ($estat_actual === 'disponible') {
        
        // Calculem les dates (7 dies de marge per tornar-lo)
        $fecha_inici = date('Y-m-d');
        $fecha_fi = date('Y-m-d', strtotime('+7 days'));

        // INSERIR EL REGISTRE DE PRÉSTEC A LA TAULA 'prestecs'
        $stmtPrestec = $db->prepare("INSERT INTO prestecs (obj_id, usu_id, pre_fecha_inici, pre_fecha_fi, pre_estat) VALUES (:obj_id, :usu_id, :inici, :fi, 'actiu')");
        $stmtPrestec->bindValue(':obj_id', $objecteId, SQLITE3_INTEGER);
        $stmtPrestec->bindValue(':usu_id', $usuariId, SQLITE3_INTEGER);
        $stmtPrestec->bindValue(':inici', $fecha_inici, SQLITE3_TEXT);
        $stmtPrestec->bindValue(':fi', $fecha_fi, SQLITE3_TEXT);
        $stmtPrestec->execute();

        // MODIFICAR L'ESTAT DE L'OBJECTE A 'PRESTAT'
        $stmtObjecte = $db->prepare("UPDATE objectes SET obj_estat = 'prestat' WHERE obj_id = :obj_id");
        $stmtObjecte->bindValue(':obj_id', $objecteId, SQLITE3_INTEGER);
        $stmtObjecte->execute();

        include_once '../includes/db_close.php';
        
        header("Location: ../detalls.php?id=$objecteId&prestec=ok");
        exit;

    } else {
        include_once '../includes/db_close.php';
        header("Location: ../detalls.php?id=$objecteId&error=no_disponible");
        exit;
    }
?>