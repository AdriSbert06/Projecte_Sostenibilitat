<?php
// 1. Connectar-se a la base de dades SQLite
$db = new SQLite3('database/biblioteca.db');

// 2. Consulta per obtenir els objectes
$query = "SELECT o.obj_id, o.obj_nom, o.obj_imatge, o.obj_descripcio, o.obj_estat, 
                 c.cat_nom, u.usu_barri 
          FROM objectes o
          JOIN categories c ON o.cat_id = c.cat_id
          JOIN usuaris u ON o.usu_propietari_id = u.usu_id";

$resultats = $db->query($query);

// 3. Incloure la capçalera i el menú
include 'includes/menu.php';
?>

<div class="container">
    
    <div class="hero-section text-center">
        <h1 class="display-5 fw-bold text-success">Comparteix en comptes de comprar</h1>
        <p class="lead text-muted">A la Biblioteca de les Coses de Barcelona fomentem l'<strong>ODS 12: Consum i Producció Responsables</strong>. Allarga la vida útil dels objectes de la teva comunitat.</p>
        <span class="badge bg-success p-2 fs-6">♻ Economia Circular</span>
    </div>

    <h2 class="mb-4 text-secondary">Catàleg d'Objectes Disponibles</h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        
        <?php while ($row = $resultats->fetchArray(SQLITE3_ASSOC)): ?>
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
                            
                            <a href="#" class="btn btn-sm btn-outline-success">Més detalls</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>

<?php
// 4. Tancar la connexió a la BD
$db->close();

// 5. Incloure el peu de pàgina
include 'includes/foot.php';
?>