<?php
require_once 'includes/db_connect.php';
require_once 'api/apiController.php';

$categoria_filtrada = isset($_GET['category']) ? $_GET['category'] : null;

$query_exec = obtenirObjectes($db, $categoria_filtrada);

include 'includes/menu.php';
?>

<div class="container">

    <div class="row my-4 bg-white p-3 rounded shadow-sm mx-1">
        <div class="col-md-6 d-flex align-items-center">
            <h5 class="mb-0 me-3 text-secondary">Filtrar per categoria:</h5>
            <select class="form-select w-auto" onchange="location = this.value;">
                <option value="index.php">Totes les categories</option>
                <?php
                $cats = obtenirCategories($db);
                while($c = $cats->fetchArray(SQLITE3_ASSOC)) {
                    $selected = ($categoria_filtrada == $c['cat_nom']) ? 'selected' : '';
                    echo "<option value='index.php?category=".urlencode($c['cat_nom'])."' $selected>".$c['cat_nom']."</option>";                }
                ?>
            </select>
        </div>
    </div>

    <h2 class="mb-4 text-secondary">Catàleg d'Objectes Disponibles</h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        
        <?php 
        while ($row = $query_exec->fetchArray(SQLITE3_ASSOC)): 
        ?>
            <div class="col">
                <div class="card h-100 card-obj">
                    
                    <img src="<?php echo htmlspecialchars($row['obj_imatge']); ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($row['obj_nom']); ?>"
                         style="height: 220px; object-fit: cover;">
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['obj_nom']); ?></h5>
                            <span class="badge badge-gratis fs-6 text-white">GRATIS</span>
                        </div>
                        
                        <div class="mb-3">
                            <span class="badge badge-categoria me-1"><?php echo htmlspecialchars($row['cat_nom']); ?></span>
                            <span class="badge bg-info text-dark">📍 <?php echo htmlspecialchars($row['usu_barri']); ?></span>
                        </div>

                        <p class="card-text text-muted flex-grow-1">
                            <?php echo htmlspecialchars($row['obj_descripcio']); ?>
                        </p>
                        
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <?php if ($row['obj_estat'] == 'disponible'): ?>
                                <span class="text-success fw-bold">● Disponible</span>
                            <?php elseif ($row['obj_estat'] == 'prestat'): ?>
                                <span class="text-warning fw-bold">● Prestat actualment</span>
                            <?php else: ?>
                                <span class="text-danger fw-bold">● En manteniment</span>
                            <?php endif; ?>
                            
                            <a href="detalls.php?id=<?php echo $row['obj_id']; ?>" class="btn btn-sm btn-outline-success">Més detalls</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>

<?php
require_once 'includes/db_close.php';

include 'includes/foot.php';
?>