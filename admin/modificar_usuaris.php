<?php
// 1. Validem que l'usuari estigui logat com a admin abans de fer res amb fitxers absoluts
require_once __DIR__ . '/../logat/auth.php';
$usuariLoguejat = validarToken();

if (!$usuariLoguejat || !isset($usuariLoguejat['rol']) || $usuariLoguejat['rol'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}

// 2. Controlem que només s'hi pugui accedir si ens envien dades per formulari POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accio'])) {
    
    include_once __DIR__ . '/../includes/db_connect.php';
    $id_usuari = intval($_POST['id_usuari']);
    $accio = $_POST['accio'];

    // 🛠️ ACCIÓ A: CAMBIAR EL ROL DE L'USUARI
    if ($accio === 'canviar_rol' && isset($_POST['rol_actual'])) {
        // Si és admin passa a user, si és user passa a admin
        $nou_rol = ($_POST['rol_actual'] === 'admin') ? 'user' : 'admin';

        $stmt = $db->prepare("UPDATE usuaris SET usu_rol = :nou_rol WHERE usu_id = :id");
        $stmt->bindValue(':nou_rol', $nou_rol, SQLITE3_TEXT);
        $stmt->bindValue(':id', $id_usuari, SQLITE3_INTEGER);
        $stmt->execute();

        include_once __DIR__ . '/../includes/db_close.php';
        header("Location: usuaris.php?status=rol_canviat");
        exit;
    }

    // 🛠️ ACCIÓ B: ELIMINAR L'USUARI COMPLETAMENT
    if ($accio === 'eliminar_usuari') {
        // Seguretat extra: comprovem que l'admin no s'estigui esborrant a si mateix
        if ($id_usuari !== intval($usuariLoguejat['id'])) {
            
            // 1. Primer esborrem els seus productes vinculats per no deixar dades penjades (Cascada manual)
            $stmt_prod = $db->prepare("DELETE FROM objectes WHERE usu_propietari_id = :id");
            $stmt_prod->bindValue(':id', $id_usuari, SQLITE3_INTEGER);
            $stmt_prod->execute();

            // 2. Ara esborrem l'usuari real de la taula usuaris
            $stmt_usu = $db->prepare("DELETE FROM usuaris WHERE usu_id = :id");
            $stmt_usu->bindValue(':id', $id_usuari, SQLITE3_INTEGER);
            $stmt_usu->execute();
        }

        include_once __DIR__ . '/../includes/db_close.php';
        header("Location: usuaris.php?status=eliminat");
        exit;
    }
}

// Si algú intenta entrar a aquest fitxer directament des del navegador escrivint la URL, el tirem enrere
header("Location: usuaris.php");
exit;
?>