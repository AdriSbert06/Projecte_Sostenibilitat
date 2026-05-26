<?php
// api/eines.php
$db = new SQLite3('../database/tools.db');
header('Content-Type: application/json');

require_once __DIR__ . '/apiController.php';
require_once __DIR__ . '/../logat/auth.php';
$usuariLoguejat = validarToken();

if (!$usuariLoguejat) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Sessió no vàlida."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch($method) {
    case 'GET':
        // 🚀 AQUÍ TENIM EL NOU SQL APARTAT DE LA VISTA PRIVADA
        if (isset($_GET['gestion']) && $_GET['gestion'] === 'true') {
            $id_usuari_logat = intval($usuariLoguejat['id']);
            $es_admin = (isset($usuariLoguejat['rol']) && $usuariLoguejat['rol'] === 'admin');

            if ($es_admin) {
                $query = "SELECT o.*, c.cat_nom, u.usu_nom AS proprietari_nom 
                          FROM objectes o 
                          JOIN categories c ON o.cat_id = c.cat_id
                          JOIN usuaris u ON o.usu_propietari_id = u.usu_id
                          ORDER BY o.obj_id DESC";
            } else {
                $query = "SELECT o.*, c.cat_nom, 'Jo' AS proprietari_nom 
                          FROM objectes o 
                          JOIN categories c ON o.cat_id = c.cat_id 
                          WHERE o.usu_propietari_id = $id_usuari_logat 
                          ORDER BY o.obj_id DESC";
            }

            $results = $db->query($query);
            $resArray = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $resArray[] = $row;
            }
            echo json_encode($resArray);
            break;
        }

        // (La teva lògica restant de GET públic es manté intacta de la següent manera)
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
            echo json_encode($result ? $result : ['error' => 'No trobat']);
        } else {
            $categoria_filtre = isset($_GET['category']) ? $_GET['category'] : null;
            $results = obtenirObjectes($db, $categoria_filtre);
            $resArray = [];
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) { $resArray[] = $row; }
            echo json_encode($resArray);
        }
        break;

    case 'POST':
        $nom = isset($input['nom']) ? trim($input['nom']) : '';
        $imatge = isset($input['imatge']) ? trim($input['imatge']) : '';
        $descripcio = isset($input['descripcio']) ? trim($input['descripcio']) : '';
        $cat_id = isset($input['categoria']) ? intval($input['categoria']) : 0;
        $propietari_id = intval($usuariLoguejat['id']);

        if (!empty($nom) && !empty($descripcio) && $cat_id > 0) {
            $respostaInsert = insertProducte($db, $nom, $imatge, $descripcio, $cat_id, $propietari_id);
            echo json_encode(["status" => $respostaInsert ? "success" : "error"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Dades incompletes."]);
        }
        break;

    case 'PUT': 
        $obj_id = isset($input['obj_id']) ? intval($input['obj_id']) : 0;
        $nom = isset($input['obj_nom']) ? trim($input['obj_nom']) : '';
        $imatge = isset($input['obj_imatge']) ? trim($input['obj_imatge']) : '';
        $descripcio = isset($input['obj_descripcio']) ? trim($input['obj_descripcio']) : '';
        $cat_id = isset($input['cat_id']) ? intval($input['cat_id']) : 0;

        if ($obj_id > 0 && !empty($nom) && !empty($descripcio) && $cat_id > 0) {
            $respostaModificar = modificarProducte($db, $obj_id, $nom, $imatge, $descripcio, $cat_id);
            echo json_encode(["status" => $respostaModificar ? "success" : "error"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Dades incorrectes."]);
        }
        break;

    case 'DELETE': 
        $obj_id = isset($input['obj_id']) ? intval($input['obj_id']) : 0;
        if ($obj_id > 0) {
            $respostaEliminar = eliminarProducte($db, $obj_id);
            echo json_encode(["status" => $respostaEliminar ? "success" : "error"]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "ID no vàlid."]);
        }
        break;
}

$db->close();