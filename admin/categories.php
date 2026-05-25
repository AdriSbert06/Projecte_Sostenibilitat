<?php
include '../includes/menu.php';

if (!$usuariLoguejat || !isset($usuariLoguejat['rol']) || $usuariLoguejat['rol'] !== 'admin') {
    header("Location: ../index.php"); 
    exit;
}

include_once '../includes/db_connect.php';

// 🛠️ PROCESSAMENT D'AFEGIR CATEGORIA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accio']) && $_POST['accio'] === 'afegir_categoria') {
    $nom_categoria = trim($_POST['cat_nom']);

    if (!empty($nom_categoria)) {
        $stmt = $db->prepare("INSERT INTO categories (cat_nom) VALUES (:nom)");
        $stmt->bindValue(':nom', $nom_categoria, SQLITE3_TEXT);
        $stmt->execute();
        
        header("Location: categories.php?status=afegida");
        exit;
    }
}

$llistat_categories = $db->query("SELECT * FROM categories ORDER BY cat_id DESC");
?>

<div class="container my-4" style="max-width: 750px;">
    <h2 class="fw-bold text-dark mb-4">🗂️ Gestió de Categories</h2>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'afegida'): ?>
        <div class="alert alert-success">Nova categoria afegida correctament.</div>
    <?php endif; ?>

    <div class="card p-3 mb-4 shadow-sm border-0 bg-white">
        <form method="POST" action="categories.php" class="row g-3 align-items-center">
            <input type="hidden" name="accio" value="afegir_categoria">
            <div class="col-sm-8">
                <input type="text" name="cat_nom" class="form-control" placeholder="Escriu el nom de la nova categoria (Ex: Jardineria)" required>
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-success w-100 fw-bold">+ Crear Categoria</button>
            </div>
        </form>
    </div>

    <div class="bg-white p-4 rounded shadow-sm border">
        <table class="table align-middle table-striped">
            <thead>
                <tr class="text-secondary fw-bold">
                    <th style="width: 20%;">ID Categoria</th>
                    <th style="width: 80%;">Nom de la Categoria</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($llistat_categories): ?>
                    <?php while ($c = $llistat_categories->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <td><span class="text-muted fw-bold">#<?php echo $c['cat_id']; ?></span></td>
                        <td><span class="badge bg-secondary px-3 py-2 rounded-pill fs-6"><?php echo htmlspecialchars($c['cat_nom']); ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include_once '../includes/db_close.php'; 
include '../includes/foot.php'; 
?>