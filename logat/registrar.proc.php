<?php
    require_once '../includes/db_connect.php';

    header("Content-Type: application/json");

    $input = json_decode(file_get_contents("php://input"), true);

    $nom = $input["nom"] ?? "";
    $mail = $input["mail"] ?? "";
    $barri = $input["barri"] ?? "";
    $contrassenya_plana = $input["contrassenya"] ?? "";

    if ($nom && $mail && $barri && $contrassenya_plana) {
        
        $stmtCheck = $db->prepare("SELECT * FROM usuaris WHERE usu_mail = :mail");
        $stmtCheck->bindValue(":mail", $mail, SQLITE3_TEXT);
        $resultCheck = $stmtCheck->execute();
        
        if ($resultCheck->fetchArray(SQLITE3_ASSOC)) {
            http_response_code(400);
            echo json_encode(["error" => "Aquest correu electrònic ja està registrat"]);
            $db->close();
            exit();
        }

        $stmt = $db->prepare("INSERT INTO usuaris (usu_nom, usu_contra, usu_rol, usu_mail, usu_barri) VALUES (:nom, :contra, 'user', :mail, :barri)");
        
        $stmt->bindValue(":nom", $nom, SQLITE3_TEXT);
        $stmt->bindValue(":contra", md5($contrassenya_plana), SQLITE3_TEXT);
        $stmt->bindValue(":mail", $mail, SQLITE3_TEXT);
        $stmt->bindValue(":barri", $barri, SQLITE3_TEXT);
        
        $resultat = $stmt->execute();

        if ($resultat) {
            echo json_encode(["status" => "success", "message" => "Usuari registrat correctament"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error de SQLite: " . $db->lastErrorMsg()]);
        }

    } else {
        http_response_code(400);
        echo json_encode(["error" => "Falten dades obligatòries per processar el registre"]);
    }

    $db->close();
?>