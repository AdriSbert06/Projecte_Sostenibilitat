<?php
$path = "../"; // Indicamos al menú que suba un nivel para encontrar los estilos y enlaces
include '../includes/menu.php';

// 🔒 CANDADO DE SEGURIDAD: Solo entra el admin
if (!$usuariLoguejat || !isset($usuariLoguejat['rol']) || $usuariLoguejat['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit("Accés denegat.");
}

include_once '../includes/db_connect.php';

$mensaje = "";

// ==========================================
// 🛠️ ACCIONES DEL ADMINISTRADOR (POST y GET)
// ==========================================

// A) Eliminar un producto globalmente
if (isset($_GET['eliminar_producto'])) {
    $id_prod = intval($_GET['eliminar_producto']);
    $db->exec("DELETE FROM objectes WHERE obj_id = $id_prod");
    header("Location: index.php?status=prod_deleted");
    exit;
}

// B) Eliminar un usuario
if (isset($_GET['eliminar_usuario'])) {
    $id_usu = intval($_GET['eliminar_usuario']);
    if ($id_usu !== intval($usuariLoguejat['id'])) { // No eliminarse a sí mismo
        $db->exec("DELETE FROM usuaris WHERE usu_id = $id_usu");
        header("Location: index.php?status=user_deleted");
        exit;
    }
}

// C) Añadir una nueva categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_categoria'])) {
    $nom_cat = trim($_POST['nom_categoria']);
    if (!empty($nom_cat)) {
        $stmt = $db->prepare("INSERT INTO categories (cat_nom) VALUES (:nom)");
        $stmt->bindValue(':nom', $nom_cat, SQLITE3_TEXT);
        $stmt->execute();
        header("Location: index.php?status=cat_added");
        exit;
    }
}

// D) Eliminar una categoría
if (isset($_GET['eliminar_categoria'])) {
    $id_cat = intval($_GET['eliminar_categoria']);
    $db->exec("DELETE FROM categories WHERE cat_id = $id_cat");
    header("Location: index.php?status=cat_deleted");
    exit;
}

// E) Modificar el estado de cualquier producto directamente desde aquí (Evita romper rutas)
if (isset($_GET['cambiar_estado']) && isset($_GET['prod_id'])) {
    $nuevo_estat = trim($_GET['cambiar_estado']);
    $p_id = intval($_GET['prod_id']);
    $stmt = $db->prepare("UPDATE objectes SET obj_estat = :estat WHERE obj_id = :id");
    $stmt->bindValue(':estat', $nuevo_estat, SQLITE3_TEXT);
    $stmt->bindValue(':id', $p_id, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: index.php?status=prod_updated");
    exit;
}


// ==========================================
// 📊 CONSULTAS PARA RELLENAR LAS 3 TABLAS
// ==========================================
$tots_productes = $db->query("SELECT o.*, c.cat_nom, u.usu_nom FROM objectes o 
                              JOIN categories c ON o.cat_id = c.cat_id 
                              JOIN usuaris u ON o.usu_propietari_id = u.usu_id ORDER BY o.obj_id DESC");

$totes_categories = $db->query("SELECT * FROM categories ORDER BY cat_id ASC");

$tots_usuaris = $db->query("SELECT * FROM usuaris ORDER BY usu_id DESC");
?>

<div class="container my-5">
    <h1 class="text-danger fw-bold mb-4">👑 Panell de Control de l'Administrador</h1>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'prod_deleted'): ?><div class="alert alert-warning">Producte eliminat globalment.</div><?php endif; ?>
        <?php if ($_GET['status'] === 'user_deleted'): ?><div class="alert alert-warning">Usuari expulsat del sistema.</div><?php endif; ?>
        <?php if ($_GET['status'] === 'cat_added'): ?><div class="alert alert-success">Nova categoria guardada.</div><?php endif; ?>
        <?php if ($_GET['status'] === 'cat_deleted'): ?><div class="alert alert-warning">Categoria eliminada del sistema.</div><?php endif; ?>
        <?php if ($_GET['status'] === 'prod_updated'): ?><div class="alert alert-info">Estat del producte actualitzat.</div><?php endif; ?>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold text-dark" id="prod-tab" data-bs-toggle="tab" data-bs-target="#tab-productos" type="button" role="tab">📦 Tots els Productes</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-dark" id="cat-tab" data-bs-toggle="tab" data-bs-target="#tab-categorias" type="button" role="tab">📂 Categories</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-dark" id="user-tab" data-bs-toggle="tab" data-bs-target="#tab-usuarios" type="button" role="tab">👥 Usuaris Registrats</button>
        </li>
    </ul>

    <div class="tab-content bg-white p-4 rounded shadow-sm">
        
        <div class="tab-pane fade show active" id="tab-productos" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Gestió Global de Productes</h3>
                <a href="../afegirProducte.php" class="btn btn-success btn-sm">+ Afegir Nou Objecte</a>
            </div>
            <div class="table-responsive">
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
                            <td><img src="<?php echo htmlspecialchars($row['obj_imatge']); ?>" style="width: 45px; height: 45px; object-fit: cover;" class="rounded img-thumbnail" onerror="this.src='https://placehold.co/50'"></td>
                            <td><strong><?php echo htmlspecialchars($row['obj_nom']); ?></strong></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars($row['usu_nom']); ?></small></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['cat_nom']); ?></span></td>
                            <td>
                                <form method="GET" action="index.php" class="d-inline">
                                    <input type="hidden" name="prod_id" value="<?php echo $row['obj_id']; ?>">
                                    <select name="cambiar_estado" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="disponible" <?php echo ($row['obj_estat']=='disponible')?'selected':''; ?>>Disponible</option>
                                        <option value="prestat" <?php echo ($row['obj_estat']=='prestat')?'selected':''; ?>>Prestat</option>
                                        <option value="manteniment" <?php echo ($row['obj_estat']=='manteniment')?'selected':''; ?>>Manteniment</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="../modificarProducte.php?id=<?php echo $row['obj_id']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                <a href="index.php?eliminar_producto=<?php echo $row['obj_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Segur que vols eliminar aquest producte de tota la plataforma?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-categorias" role="tabpanel">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card p-3 bg-light border">
                        <h5>Afegir Nova Categoria</h5>
                        <form method="POST" action="index.php">
                            <div class="mb-3">
                                <input type="text" name="nom_categoria" class="form-control form-control-sm" placeholder="Nom (Ex: Joguines)" required>
                            </div>
                            <button type="submit" name="crear_categoria" class="btn btn-danger btn-sm w-100">Crear</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <h3>Categories Existents</h3>
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom de la Categoria</th>
                                <th>Acció</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($c = $totes_categories->fetchArray(SQLITE3_ASSOC)): ?>
                            <tr>
                                <td><?php echo $c['cat_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($c['cat_nom']); ?></strong></td>
                                <td>
                                    <a href="index.php?eliminar_categoria=<?php echo $c['cat_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Atenció: Esborrar la categoria pot afectar els productes associats. Continuar?');">Eliminar</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-usuarios" role="tabpanel">
            <h3>Usuaris del Sistema</h3>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Correu Electrònic</th>
                            <th>Barri</th>
                            <th>Rol</th>
                            <th>Acció</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $tots_usuaris->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><?php echo $u['usu_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($u['usu_nom']); ?></strong></td>
                            <td><?php echo htmlspecialchars($u['usu_mail']); ?></td>
                            <td><?php echo htmlspecialchars($u['usu_barri']); ?></td>
                            <td>
                                <span class="badge <?php echo ($u['usu_rol'] === 'admin') ? 'bg-danger' : 'bg-primary'; ?>">
                                    <?php echo htmlspecialchars($u['usu_rol']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($u['usu_id'] != $usuariLoguejat['id']): ?>
                                    <a href="index.php?eliminar_usuario=<?php echo $u['usu_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Segur que vols eliminar completament aquest compte d\'usuari?');">Eliminar Usuari</a>
                                <?php else: ?>
                                    <span class="text-muted small">Ets tu (Sessió activa)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php 
include_once '../includes/db_close.php'; 
include '../includes/foot.php'; 
?>