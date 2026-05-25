<?php
$db = new SQLite3('database/tools.db');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        if (isset($_GET['categories']) && $_GET['categories'] === 'all') {
            $results = $db->query("SELECT * FROM categories");
            $categories = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) { $categories[] = $row; }
            echo json_encode($categories);

        } elseif (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT o.*, c.cat_nom, u.usu_barri, u.usu_mail, u.usu_nom 
                                  FROM objectes o
                                  JOIN categories c ON o.cat_id = c.cat_id
                                  JOIN usuaris u ON o.usu_propietari_id = u.usu_id 
                                  WHERE o.obj_id = :id");
            $stmt->bindValue(':id', $_GET['id'], SQLITE3_INTEGER);
            $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
            if ($result) {
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'No trobat']);
            }

        } else {
            if (isset($_GET['category'])) {
                $stmt = $db->prepare("SELECT o.*, c.cat_nom, u.usu_barri 
                                      FROM objectes o 
                                      JOIN categories c ON o.cat_id = c.cat_id
                                      JOIN usuaris u ON o.usu_propietari_id = u.usu_id
                                      WHERE c.cat_nom = :cat ORDER BY o.obj_id DESC");
                $stmt->bindValue(':cat', $_GET['category'], SQLITE3_TEXT);
                $results = $stmt->execute();
            } else {
                $results = $db->query("SELECT o.*, c.cat_nom, u.usu_barri 
                                       FROM objectes o 
                                       JOIN categories c ON o.cat_id = c.cat_id
                                       JOIN usuaris u ON o.usu_propietari_id = u.usu_id
                                       ORDER BY o.obj_id DESC");
            }
            
            $resArray = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $resArray[] = $row;
            }
            echo json_encode($resArray);
        }
        break;

    case 'POST':
        if (!isset($input['obj_id'])) {
            $respostaInsert = insertProducte($db, $input['obj_nom'], $input['obj_imatge'], $input['obj_descripcio'], $input['cat_id'], $input['usu_propietari_id']);
            if ($respostaInsert) {
                echo json_encode(["status" => "success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error al insertar"]);
            }
        }
        break;

    case 'PUT': 
        if (isset($input['obj_id'])) {
            $respostaModificar = modificarProducte($db, $input['obj_id'], $input['obj_nom'], $input['obj_imatge'], $input['obj_descripcio'], $input['cat_id']);
            if ($respostaModificar) {
                echo json_encode(["status" => "success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error al ejecutar SQL"]);
            }
        }
        break;

    case 'DELETE': 
        if (isset($input['obj_id'])) {
            $respostaEliminar = eliminarProducte($db, $input['obj_id']);
            if ($respostaEliminar) {
                echo json_encode(["status" => "success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error"]);
            }
        }
        break;
}