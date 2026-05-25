<?php
require_once __DIR__ . '/../logat/auth.php';
$usuariLoguejat = validarToken();

if (!$usuariLoguejat) {
    header("Location: ../logat/login.php"); 
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    include_once __DIR__ . '/../includes/db_connect.php';
    
    $id_objecte = intval($_GET['id']);
    $id_usuari_logat = intval($usuariLoguejat['id']);
    $es_admin = (isset($usuariLoguejat['rol']) && $usuariLoguejat['rol'] === 'admin');

    if ($es_admin) {
        $stmt = $db->prepare("DELETE FROM objectes WHERE obj_id = :obj_id");
    } else {
        $stmt = $db->prepare("DELETE FROM objectes WHERE obj_id = :obj_id AND usu_propietari_id = :usu_id");
        $stmt->bindValue(':usu_id', $id_usuari_logat, SQLITE3_INTEGER);
    }
    
    $stmt->bindValue(':obj_id', $id_objecte, SQLITE3_INTEGER);
    $stmt->execute();
    
    include_once __DIR__ . '/../includes/db_close.php';
}

header("Location: gestionarProductes.php?status=esborrat");
exit;
?>