<?php
    header('Content-Type: application/json; charset=utf-8');

    $db = new SQLite3(__DIR__ . '/../database/tools.db');

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
            // Llista de gestió privada (Admin / Usuari)
            if (isset($_GET['gestion']) && $_GET['gestion'] === 'true') {
                $id_usuari_logat = intval($usuariLoguejat['id']);
                $es_admin = (isset($usuariLoguejat['rol']) && $usuariLoguejat['rol'] === 'admin');

                $results = obtenirObjectesGestio($db, $id_usuari_logat, $es_admin);
                $resArray = [];
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    $resArray[] = $row;
                }
                echo json_encode($resArray);
                break;
            }

            // Obtenir totes les categories
            if (isset($_GET['categories']) && $_GET['categories'] === 'all') {
                $results = obtenirCategoriesCompletes($db);
                $categories = [];
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) { 
                    $categories[] = $row; 
                }
                echo json_encode($categories);
                
            // Obtenir un objecte concret per ID
            } elseif (isset($_GET['id'])) {
                $result = obtenirObjectePerId($db, intval($_GET['id']));
                echo json_encode($result ? $result : ['error' => 'No trobat']);
                
            // Catàleg públic general (filtrat o no per categoria)
            } else {
                $categoria_filtre = isset($_GET['category']) ? $_GET['category'] : null;
                $results = obtenirObjectes($db, $categoria_filtre);
                $resArray = [];
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) { 
                    $resArray[] = $row; 
                }
                echo json_encode($resArray);
            }
            break;

        case 'POST': // Petició POST per inserir un producte
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

        case 'PUT': //Petició PUT per modificar un producte
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

        case 'DELETE': //Petició DELETE per eliminar un producte
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
?>