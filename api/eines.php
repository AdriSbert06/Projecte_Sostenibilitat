<?php
include_once("apiController.php");
$db = new SQLite3('toolsharing.db');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        if (isset($_GET['categories']) && $_GET['categories'] === 'all') {
            $results = $db->query("SELECT DISTINCT category FROM productes");
            $categories = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) { $categories[] = $row['category']; }
            echo json_encode($categories);

        } elseif (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM productes WHERE id = :id");
            $stmt->bindValue(':id', $_GET['id'], SQLITE3_INTEGER);
            $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
            if ($result) {
                $result['rating'] = [
                    'rate' => isset($result['rating.rate']) ? floatval($result['rating.rate']) : 0,
                    'count' => isset($result['rating.count']) ? intval($result['rating.count']) : 0
                ];
                unset($result['rating.rate'], $result['rating.count']);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'No trobat']);
            }

        } else {
            $sql = isset($_GET['category']) 
                ? "SELECT * FROM productes WHERE category = :cat ORDER BY id DESC" 
                : "SELECT * FROM productes ORDER BY id DESC";
            
            $stmt = $db->prepare($sql);
            if(isset($_GET['category'])) $stmt->bindValue(':cat', $_GET['category'], SQLITE3_TEXT);
            $results = $stmt->execute();
            
            $resArray = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $row['rating'] = [
                    'rate' => (isset($row['rating.rate'])) ? floatval($row['rating.rate']) : 0,
                    'count' => (isset($row['rating.count'])) ? intval($row['rating.count']) : 0
                ];
                unset($row['rating.rate'], $row['rating.count']);
                $resArray[] = $row;
            }
            echo json_encode($resArray);
        }
        break;

    case 'POST': //Per crear el producte
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['id'])) {
            
            $respostaInsert = insertProducte($db, $input['title'], $input['price'], $input['description'], $input['category'], $input['image']);
            
            if ($respostaInsert) {
                echo json_encode(["status" => "success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error al insertar"]);
            }
        }
        break;

    case 'PUT': 
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['id'])) {
            $respostaModificar = modificarProducte($db, $input['id'], $input['title'], $input['price'], $input['description'], $input['category'], $input['image']);

            if ($respostaModificar) {
                echo json_encode(["status" => "success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error al ejecutar SQL"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "ID no proporcionado"]);
        }
        break;


    case 'DELETE': //Per eliminar el producte
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['id'])) {


        $respostaEliminar = eliminarProducte($db, $input['id']);

            if ($respostaEliminar) {
                echo json_encode(["status" => "success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error"]);
            }
        }
        break;
    
    case 'PATCH': 
        if (isset($input['id'])) {
            // Pasamos el array completo $input a la función dinámica
            $resposta = modificarProducteParcial($db, $input['id'], $input);

            if ($resposta) {
                echo json_encode(["status" => "success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error en l'actualització parcial"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Falta ID"]);
        }
        break;
}
?>