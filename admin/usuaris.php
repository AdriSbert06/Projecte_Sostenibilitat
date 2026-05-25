<?php
include '../includes/menu.php';

if (!$usuariLoguejat || !isset($usuariLoguejat['rol']) || $usuariLoguejat['rol'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}

include_once '../includes/db_connect.php';

// 🛠️ PROCESSAMENT DE FORMULARIS (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accio'])) {
    $id_usuari = intval($_POST['id_usuari']);
    $accio = $_POST['accio'];

    // ACCIÓ 1: MODIFICAR EL NOM DEL USUARI
    if ($accio === 'modificar_nom' && isset($_POST['nou_nom'])) {
        $nou_nom = trim($_POST['nou_nom']);
        
        if (!empty($nou_nom)) {
            $stmt = $db->prepare("UPDATE usuaris SET usu_nom = :nom WHERE usu_id = :id");
            $stmt->bindValue(':nom', $nou_nom, SQLITE3_TEXT);
            $stmt->bindValue(':id', $id_usuari, SQLITE3_INTEGER);
            $stmt->execute();
            
            header("Location: usuaris.php?status=nom_modificat");
            exit;
        }
    }

    // ACCIÓ 2: ELIMINAR USUARI
    if ($accio === 'eliminar_usuari') {
        if ($id_usuari !== intval($usuariLoguejat['id'])) {
            // Borrem productes de l'usuari primer
            $stmt_prod = $db->prepare("DELETE FROM objectes WHERE usu_propietari_id = :id");
            $stmt_prod->bindValue(':id', $id_usuari, SQLITE3_INTEGER);
            $stmt_prod->execute();

            // Borrem l'usuari
            $stmt_usu = $db->prepare("DELETE FROM usuaris WHERE usu_id = :id");
            $stmt_usu->bindValue(':id', $id_usuari, SQLITE3_INTEGER);
            $stmt_usu->execute();
            
            header("Location: usuaris.php?status=eliminat");
            exit;
        }
    }
}

$llistat_usuaris = $db->query("SELECT * FROM usuaris ORDER BY usu_id ASC");
?>

<div class="container my-4">
    <h2 class="fw-bold text-dark mb-4">👥 Gestió d'Usuaris (Modificar Noms i Eliminar)</h2>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'nom_modificat'): ?>
            <div class="alert alert-success">El nom de l'usuari s'ha actualitzat correctament.</div>
        <?php elseif ($_GET['status'] === 'eliminat'): ?>
            <div class="alert alert-warning">L'usuari ha estat eliminat correctament.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="bg-white p-4 rounded shadow-sm border">
        <div class="table-responsive">
            <table class="table align-middle table-striped table-hover">
                <thead>
                    <tr class="text-secondary fw-bold">
                        <th style="width: 10%;">ID</th>
                        <th style="width: 50%;">Nom de l'Usuari (Editable)</th>
                        <th style="width: 15%;">Rol</th>
                        <th style="width: 25%;" class="text-end">Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($llistat_usuaris): ?>
                        <?php while ($u = $llistat_usuaris->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><span class="text-muted fw-bold">#<?php echo $u['usu_id']; ?></span></td>
                            <td>
                                <form method="POST" action="usuaris.php" class="d-flex gap-2">
                                    <input type="hidden" name="accio" value="modificar_nom">
                                    <input type="hidden" name="id_usuari" value="<?php echo $u['usu_id']; ?>">
                                    <input type="text" name="nou_nom" class="form-control form-control-sm fw-bold fs-5" value="<?php echo htmlspecialchars($u['usu_nom']); ?>" required>
                                    <button type="submit" class="btn btn-sm btn-primary px-3">Guardar</button>
                                </form>
                            </td>
                            <td>
                                <?php if ($u['usu_rol'] === 'admin'): ?>
                                    <span class="badge bg-warning text-dark px-3 py-1">👑 Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-secondary border px-3 py-1">👤 User</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if (intval($u['usu_id']) !== intval($usuariLoguejat['id'])): ?>
                                    <form method="POST" action="usuaris.php" onsubmit="return confirm('Segur que vols eliminar aquest usuari?');" class="d-inline">
                                        <input type="hidden" name="accio" value="eliminar_usuari">
                                        <input type="hidden" name="id_usuari" value="<?php echo $u['usu_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm px-3">Eliminar</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm px-3" disabled>Ets tu</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
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