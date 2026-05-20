<?php
function validarToken() {
    if (!isset($_COOKIE['token'])) {
        return false;
    }

    $token = $_COOKIE['token'];
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    list($header64, $payload64, $signature64) = $parts;
    $secret = "clauSuperSecreta123";
    $expectedSignature = base64_encode(hash_hmac("sha256", "$header64.$payload64", $secret, true));

    if ($signature64 !== $expectedSignature) return false;

    $payload = json_decode(base64_decode($payload64), true);
    if (!$payload || time() > $payload['exp']) {
        // Token expirat → esborrem cookie
        setcookie('token', '', time() - 3600, '/');
        return false;
    }

    return $payload;
}
?>