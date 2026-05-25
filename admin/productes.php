<?php
$path = "../"; // Subimos un nivel porque estamos dentro de la carpeta admin/
include '../includes/menu.php';

// 🔒 CANDADO DE SEGURIDAD
if (!$usuariLoguejat || !isset($usuariLoguejat['rol']) || $usuariLoguejat['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit("Accés denegat.");
}

include_once '../includes/db_connect.php';

// El administrador puede eliminar cualquier producto
if (isset($_GET['eliminar'])) {
    $id_del = intval($_GET['eliminar']);
    $stmt = $db->prepare("DELETE FROM objectes WHERE obj_id = :id");
    $stmt->bindValue(':id', $id_del, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: productes.php?status=esborrat");
    exit;
}

// Obtener TODOS los productos del sistema sin filtrar por usuario
$query = "SELECT o.*, c.cat_nom, u.usu_nom FROM objectes o 
          JOIN categories c ON o.cat_id = c.cat_id 
          JOIN usuaris u ON o.usu_propietari_id = u.usu_id 
          ORDER BY o.obj_id DESC";
$tots_productes = $db->query($query);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-danger">👑 Admin: Gestionar TOTS els Productes</h2>
        <a href="../afegirProducte.php" class="btn btn-success">+ Afegir Nou Objecte</a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'esborrat'): ?>
        <div class="alert alert-warning">Producte eliminat del sistema de forma global.</div>
    <?php endif; ?>

    <div class="bg-white p-3 rounded shadow-sm">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Imatge</th>
                    <th>Nom</th>
                    <th>Propietari</th>
                    <th>Categoria</th>
                    <th>Estat</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $tots_productes->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($row['obj_imatge']); ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded"></td>
                    <td><strong><?php echo htmlspecialchars($row['obj_nom']); ?></strong></td>
                    <td><span class="text-muted"><?php echo htmlspecialchars($row['usu_nom']); ?></span></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['cat_nom']); ?></span></td>
                    <td><span class="badge bg-info"><?php echo htmlspecialchars($row['obj_estat']); ?></span></td>
                    <td>
                        <a href="../modificarProducte.php?id=<?php echo $row['obj_id']; ?>" class="btn btn-sm btn-primary">Modificar</a>
                        <a href="productes.php?eliminar=<?php echo $row['obj_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Segur que vols eliminar aquest producte de la plataforma?');">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../includes/db_close.php'; include '../includes/foot.php'; ?>