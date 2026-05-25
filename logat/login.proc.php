<?php
$db = new SQLite3('../database/tools.db'); 

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$nom = $input["nom"] ?? "";
$contrassenya_plana = $input["contrassenya"] ?? "";

if ($nom && $contrassenya_plana) {
    $stmt = $db->prepare("SELECT * FROM usuaris WHERE usu_nom = :nom");
    $stmt->bindValue(":nom", $nom, SQLITE3_TEXT);
    $result = $stmt->execute();
    $usuari = $result->fetchArray(SQLITE3_ASSOC);

    if ($usuari && $usuari["usu_contra"] === md5($contrassenya_plana)) {
        
        $header = base64_encode(json_encode([
            "alg" => "HS256", 
            "typ" => "JWT"
        ]));
        
        $payload = base64_encode(json_encode([
            "id" => $usuari["usu_id"],
            "nom" => $usuari["usu_nom"],
            "rol" => $usuari["usu_rol"],
            "exp" => time() + 3600
        ]));

        $clau_secreta = "clauSuperSecreta123";
        
        $signatura = base64_encode(hash_hmac(
            "sha256", 
            "$header.$payload", 
            $clau_secreta, 
            true
        ));

        $token = "$header.$payload.$signatura";

        setcookie("token", $token, time() + 3600, "/");
        
        echo json_encode(["status" => "success", "token" => $token]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Usuari o contrasenya incorrectes"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Falten dades (nom o contrasenya)"]);
}

$db->close();
?>