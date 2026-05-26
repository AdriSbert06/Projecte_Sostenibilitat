<?php
// api/apiController.php

function insertProducte($db, $nom, $imatge, $descripcio, $cat_id, $propietari_id) {
    $stmt = $db->prepare("INSERT INTO objectes (obj_nom, obj_imatge, obj_descripcio, cat_id, usu_propietari_id, obj_estat) VALUES (:nom, :imatge, :descripcio, :cat_id, :propietari_id, 'disponible')");
    $stmt->bindValue(':nom', $nom, SQLITE3_TEXT);
    $stmt->bindValue(':imatge', $imatge, SQLITE3_TEXT);
    $stmt->bindValue(':descripcio', $descripcio, SQLITE3_TEXT);
    $stmt->bindValue(':cat_id', $cat_id, SQLITE3_INTEGER);
    $stmt->bindValue(':propietari_id', $propietari_id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function modificarProducte($db, $id, $nom, $imatge, $descripcio, $cat_id) {
    $stmt = $db->prepare("UPDATE objectes SET obj_nom=:nom, obj_imatge=:imatge, obj_descripcio=:descripcio, cat_id=:cat_id WHERE obj_id=:id");
    $stmt->bindValue(':nom', $nom, SQLITE3_TEXT);
    $stmt->bindValue(':imatge', $imatge, SQLITE3_TEXT);
    $stmt->bindValue(':descripcio', $descripcio, SQLITE3_TEXT);
    $stmt->bindValue(':cat_id', $cat_id, SQLITE3_INTEGER);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function eliminarProducte($db, $id) {
    $stmt = $db->prepare("DELETE FROM objectes WHERE obj_id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function obtenirObjectes($db, $categoria = null) {
    $sql = "SELECT o.obj_id, o.obj_nom, o.obj_imatge, o.obj_descripcio, o.obj_estat, 
                   c.cat_nom, u.usu_barri 
            FROM objectes o
            JOIN categories c ON o.cat_id = c.cat_id
            JOIN usuaris u ON o.usu_propietari_id = u.usu_id";

    if ($categoria !== null) {
        $sql .= " WHERE c.cat_nom = :cat ORDER BY o.obj_id DESC";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':cat', $categoria, SQLITE3_TEXT);
        return $stmt->execute();
    } else {
        $sql .= " ORDER BY o.obj_id DESC";
        return $db->query($sql);
    }
}

function obtenirCategories($db) {
    return $db->query("SELECT cat_nom FROM categories");
}
?>