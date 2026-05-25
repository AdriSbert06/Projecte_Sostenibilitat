<?php
include '../includes/menu.php';

if (!$usuariLoguejat) {
    header("Location: ../logat/login.php"); 
    exit;
}

include_once '../includes/db_connect.php';
$id_usuari_logat = intval($usuariLoguejat['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $descripcio = trim($_POST['descripcio']);
    $categoria = intval($_POST['categoria']);
    $imatge = trim($_POST['imatge']);

    if (!empty($nom) && !empty($descripcio) && $categoria > 0) {
        $stmt = $db->prepare("INSERT INTO objectes (obj_nom, obj_descripcio, obj_imatge, obj_estat, cat_id, usu_propietari_id) 
                             VALUES (:nom, :desc, :imatge, 'disponible', :cat_id, :usu_id)");
        $stmt->bindValue(':nom', $nom, SQLITE3_TEXT);
        $stmt->bindValue(':desc', $descripcio, SQLITE3_TEXT);
        $stmt->bindValue(':imatge', $imatge, SQLITE3_TEXT);
        $stmt->bindValue(':cat_id', $categoria, SQLITE3_INTEGER);
        $stmt->bindValue(':usu_id', $id_usuari_logat, SQLITE3_INTEGER);
        $stmt->execute();

        header("Location: gestionarProductes.php?status=afegit");
        exit;
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY cat_nom ASC");
?>

<div class="container my-5" style="max-width: 650px;">
    <div class="card p-4 shadow-sm border-0 bg-white rounded">
        <h2 class="fw-bold text-dark mb-4">🛠️ Publicar un Nou Objecte</h2>
        
        <form method="POST" action="afegirProducte.php">
            <div class="mb-3">
                <label class="form-label fw-bold">Nom de l'objecte</label>
                <input type="text" name="nom" class="form-control" placeholder="Ex: Serra elèctrica" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Descripció de l'estat i ús</label>
                <textarea name="descripcio" class="form-control" rows="4" placeholder="Explica com funciona..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Categoria</label>
                <select name="categoria" class="form-select" required>
                    <option value="">Selecciona una categoria...</option>
                    <?php while ($c = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $c['cat_id']; ?>"><?php echo htmlspecialchars($c['cat_nom']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Ruta de la Imatge</label>
                <input type="text" name="imatge" class="form-control" placeholder="Ex: estils/imatges/eina.jpg">
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success w-100 fw-bold py-2">Desar i Publicar</button>
                <a href="gestionarProductes.php" class="btn btn-secondary w-100 fw-bold py-2">Cancel·lar</a>
            </div>
        </form>
    </div>
</div>

<?php 
include_once '../includes/db_close.php'; 
include '../includes/foot.php'; 
?>