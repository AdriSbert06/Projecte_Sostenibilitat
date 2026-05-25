<?php
include '../includes/menu.php';

if (!$usuariLoguejat) {
    header("Location: ../logat/login.php"); 
    exit;
}

include_once '../includes/db_connect.php';
$id_usuari_logat = intval($usuariLoguejat['id']);
$es_admin = (isset($usuariLoguejat['rol']) && $usuariLoguejat['rol'] === 'admin');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: gestionarProductes.php");
    exit;
}
$id_objecte = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $descripcio = trim($_POST['descripcio']);
    $categoria = intval($_POST['categoria']);
    $estat = trim($_POST['estat']);
    $imatge = trim($_POST['imatge']);

    if (!empty($nom) && !empty($descripcio) && $categoria > 0) {
        if ($es_admin) {
            $stmt = $db->prepare("UPDATE objectes SET obj_nom = :nom, obj_descripcio = :desc, obj_imatge = :img, obj_estat = :estat, cat_id = :cat WHERE obj_id = :obj_id");
        } else {
            $stmt = $db->prepare("UPDATE objectes SET obj_nom = :nom, obj_descripcio = :desc, obj_imatge = :img, obj_estat = :estat, cat_id = :cat WHERE obj_id = :obj_id AND usu_propietari_id = :usu_id");
            $stmt->bindValue(':usu_id', $id_usuari_logat, SQLITE3_INTEGER);
        }
        
        $stmt->bindValue(':nom', $nom, SQLITE3_TEXT);
        $stmt->bindValue(':desc', $descripcio, SQLITE3_TEXT);
        $stmt->bindValue(':img', $imatge, SQLITE3_TEXT);
        $stmt->bindValue(':estat', $estat, SQLITE3_TEXT);
        $stmt->bindValue(':cat', $categoria, SQLITE3_INTEGER);
        $stmt->bindValue(':obj_id', $id_objecte, SQLITE3_INTEGER);
        $stmt->execute();

        header("Location: gestionarProductes.php?status=modificat");
        exit;
    }
}

if ($es_admin) {
    $objecte = $db->querySingle("SELECT * FROM objectes WHERE obj_id = $id_objecte", true);
} else {
    $objecte = $db->querySingle("SELECT * FROM objectes WHERE obj_id = $id_objecte AND usu_propietari_id = $id_usuari_logat", true);
}

if (!$objecte) {
    header("Location: gestionarProductes.php");
    exit;
}

$categories = $db->query("SELECT * FROM categories ORDER BY cat_nom ASC");
?>

<div class="container my-5" style="max-width: 650px;">
    <div class="card p-4 shadow-sm border-0 bg-white rounded">
        <h2 class="fw-bold text-dark mb-4">
            ✏️ Modificar Objecte <?php echo $es_admin ? '<span class="badge bg-warning text-dark fs-6 align-middle">Mode Admin</span>' : ''; ?>
        </h2>
        
        <form method="POST" action="modificarProducte.php?id=<?php echo $id_objecte; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Nom del Producte</label>
                <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($objecte['obj_nom']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Modificar Descripció</label>
                <textarea name="descripcio" class="form-control" rows="4" required><?php echo htmlspecialchars($objecte['obj_descripcio']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Canviar Categoria</label>
                <select name="categoria" class="form-select" required>
                    <?php while ($c = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $c['cat_id']; ?>" <?php echo ($c['cat_id'] == $objecte['cat_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['cat_nom']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Estat actual</label>
                <select name="estat" class="form-select">
                    <option value="disponible" <?php echo ($objecte['obj_estat'] === 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                    <option value="prestat" <?php echo ($objecte['obj_estat'] === 'prestat') ? 'selected' : ''; ?>>Prestat</option>
                    <option value="manteniment" <?php echo ($objecte['obj_estat'] === 'manteniment') ? 'selected' : ''; ?>>Manteniment</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Ruta de la Imatge</label>
                <input type="text" name="imatge" class="form-control" value="<?php echo htmlspecialchars($objecte['obj_imatge']); ?>">
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Guardar Canvis</button>
                <a href="gestionarProductes.php" class="btn btn-secondary w-100 fw-bold py-2">Tornar enrere</a>
            </div>
        </form>
    </div>
</div>

<?php 
include_once '../includes/db_close.php'; 
include '../includes/foot.php'; 
?>