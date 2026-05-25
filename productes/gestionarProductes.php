<?php
include '../includes/menu.php';

if (!$usuariLoguejat) {
    header("Location: ../logat/login.php"); 
    exit;
}

include_once '../includes/db_connect.php';
$id_usuari_logat = intval($usuariLoguejat['id']);
$es_admin = (isset($usuariLoguejat['rol']) && $usuariLoguejat['rol'] === 'admin');

if ($es_admin) {
    $query = "SELECT o.*, c.cat_nom, u.usu_nom AS propietari_nom 
              FROM objectes o 
              JOIN categories c ON o.cat_id = c.cat_id
              JOIN usuaris u ON o.usu_propietari_id = u.usu_id
              ORDER BY o.obj_id DESC";
} else {
    // Si és un usuari normal, només veu les seves pròpies eines
    $query = "SELECT o.*, c.cat_nom, 'Jo' AS propietari_nom 
              FROM objectes o 
              JOIN categories c ON o.cat_id = c.cat_id 
              WHERE o.usu_propietari_id = $id_usuari_logat 
              ORDER BY o.obj_id DESC";
}

$meus_productes = $db->query($query);
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">
                <?php echo $es_admin ? '📦 Administració Total de Productes' : 'La meva Gestió de Productes'; ?>
            </h2>
            <?php if ($es_admin): ?>
                <span class="badge bg-danger px-3 py-2 fw-bold text-white">Mode Admin: Control de tot el catàleg</span>
            <?php endif; ?>
        </div>
        <a href="afegirProducte.php" class="btn btn-success fw-bold">+ Afegir Nou Objecte</a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'esborrat'): ?>
            <div class="alert alert-warning shadow-sm">Producte eliminat del sistema correctament.</div>
        <?php elseif ($_GET['status'] === 'afegit'): ?>
            <div class="alert alert-success shadow-sm">Nou producte publicat amb èxit!</div>
        <?php elseif ($_GET['status'] === 'modificat'): ?>
            <div class="alert alert-info shadow-sm">Producte actualitzat correctament a la base de dades.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="bg-white p-4 rounded shadow-sm border">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr class="text-secondary fw-bold">
                        <th style="width: 10%;">Imatge</th>
                        <th style="width: 30%;">Nom de l'Objecte</th>
                        <th style="width: 20%;">Categoria</th>
                        <?php if ($es_admin): ?>
                            <th style="width: 15%;">Propietari</th>
                        <?php endif; ?>
                        <th style="width: 10%;">Estat</th>
                        <th style="width: 15%;">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($meus_productes): ?>
                        <?php $hi_ha_productes = false; ?>
                        <?php while ($row = $meus_productes->fetchArray(SQLITE3_ASSOC)): ?>
                        <?php $hi_ha_productes = true; ?>
                        <tr>
                            <td>
                                <img src="../<?php echo htmlspecialchars($row['obj_imatge'] ?? ''); ?>" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='https://placehold.co/50'">
                            </td>
                            <td><span class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($row['obj_nom']); ?></span></td>
                            <td><span class="badge bg-secondary px-3 py-2 rounded-pill"><?php echo htmlspecialchars($row['cat_nom']); ?></span></td>
                            
                            <?php if ($es_admin): ?>
                                <td><span class="text-primary fw-bold">👤 <?php echo htmlspecialchars($row['propietari_nom']); ?></span></td>
                            <?php endif; ?>
                            
                            <td>
                                <?php $badge_color = ($row['obj_estat'] === 'disponible') ? 'bg-success' : 'bg-danger'; ?>
                                <span class="badge <?php echo $badge_color; ?> px-3 py-2 rounded"><?php echo htmlspecialchars($row['obj_estat']); ?></span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="modificarProducte.php?id=<?php echo $row['obj_id']; ?>" class="btn btn-primary btn-sm px-3">Editar</a>
                                    <a href="eliminarProducte.php?id=<?php echo $row['obj_id']; ?>" class="btn btn-danger btn-sm px-3" onclick="return confirm('Segur que vols eliminar aquest objecte del sistema?');">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if (!$hi_ha_productes): ?>
                            <tr>
                                <td colspan="<?php echo $es_admin ? '6' : '5'; ?>" class="text-center text-muted py-4">No s'ha trobat cap producte.</td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
include_once '../includes/db_close.php'; 
include '../includes/foot.php'; 
?>