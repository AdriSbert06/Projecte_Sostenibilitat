<?php

function insertProducte($db, $title, $price, $description, $category, $image) {
    $stmt = $db->prepare("INSERT INTO productes (title, price, description, category, image, [rating.rate], [rating.count]) VALUES (:title, :price, :description, :category, :image, 0, 0)");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $stmt->bindValue(':image', $image, SQLITE3_TEXT);
    return $stmt->execute();
}

function modificarProducte($db, $id, $title, $price, $description, $category, $image) {
    $stmt = $db->prepare("UPDATE productes SET title=:title, price=:price, description=:description, category=:category, image=:image WHERE id=:id");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':category', $category, SQLITE3_TEXT);
    $stmt->bindValue(':image', $image, SQLITE3_TEXT);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function eliminarProducte($db, $id) {
    $stmt = $db->prepare("DELETE FROM productes WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    return $stmt->execute();
}

function modificarParcial($db, $id, $dades) {
    $camps = [];
    foreach ($dades as $clau => $valor) {
        if ($clau !== 'id') {
            $camps[] = "$clau = :$clau";
        }
    }
    
    $sql = "UPDATE productes SET " . implode(", ", $camps) . " WHERE id = :id";
    $stmt = $db->prepare($sql);
    
    foreach ($dades as $clau => $valor) {
        $tipus = is_numeric($valor) ? SQLITE3_FLOAT : SQLITE3_TEXT;
        if ($clau === 'id') $tipus = SQLITE3_INTEGER;
        $stmt->bindValue(":$clau", $valor, $tipus);
    }
    
    return $stmt->execute();
}