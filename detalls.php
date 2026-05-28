<?php
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header('Location: index.php');
        exit;
    }

    $id_objecte = intval($_GET['id']);

    include_once 'includes/db_connect.php';

    // Obtenir totes les dades del producte
    $query = "SELECT o.*, c.cat_nom, u.usu_nom, u.usu_mail, u.usu_barri 
            FROM objectes o
            JOIN categories c ON o.cat_id = c.cat_id
            JOIN usuaris u ON o.usu_propietari_id = u.usu_id
            WHERE o.obj_id = $id_objecte";

    $resultat = $db->querySingle($query, true);

    // Si l'objecte no existeix a la bbdd redirigeix a index.php
    if (!$resultat) {
        include_once 'includes/db_close.php';
        header('Location: index.php');
        exit;
    }

    include 'includes/menu.php';
?>

<div class="container my-5">
    
    <?php if (isset($_GET['prestec']) && $_GET['prestec'] === 'ok'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Préstec registrat correctament!</strong> L'objecte s'ha reservat a nom teu.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'no_disponible'): ?>
        <div class="alert alert-danger" role="alert">
            ⚠️ <strong>Error:</strong> Aquest objecte ja ha estat prestat o es troba en manteniment.
        </div>
    <?php endif; ?>


    <a href="index.php" class="btn btn-secondary mb-4">← Tornar al catàleg</a>

    <div class="row bg-white p-4 rounded shadow-sm mx-1">
        <div class="col-md-6 text-center mb-4 mb-md-0">
            <img src="<?php echo htmlspecialchars($resultat['obj_imatge']); ?>" 
                 class="img-fluid rounded img-thumbnail" 
                 alt="<?php echo htmlspecialchars($resultat['obj_nom']); ?>"
                 style="max-height: 400px; width: 100%; object-fit: cover;">
        </div>

        <div class="col-md-6 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h1 class="h2 text-success fw-bold mb-0"><?php echo htmlspecialchars($resultat['obj_nom']); ?></h1>
                    <span class="badge bg-success p-2 fs-5">GRATIS</span>
                </div>
                
                <div class="mb-4">
                    <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($resultat['cat_nom']); ?></span>
                    <span class="badge bg-info text-dark">📍 <?php echo htmlspecialchars($resultat['usu_barri']); ?></span>
                </div>

                <h5 class="text-dark">Descripció de l'objecte:</h5>
                <p class="text-muted fs-5 mb-4"><?php echo htmlspecialchars($resultat['obj_descripcio']); ?></p>

                <hr>

                <div class="bg-light p-3 rounded border">
                    <h5 class="h6 text-secondary mb-2">Informació de Proximitat (Economia Circular)</h5>
                    <p class="mb-1"><strong>Cedit per:</strong> <?php echo htmlspecialchars($resultat['usu_nom']); ?></p>
                    <p class="mb-1"><strong>Barri de Barcelona:</strong> <?php echo htmlspecialchars($resultat['usu_barri']); ?></p>
                    <p class="mb-0"><strong>Correu de contacte:</strong> <a href="mailto:<?php echo $resultat['usu_mail']; ?>"><?php echo htmlspecialchars($resultat['usu_mail']); ?></a></p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <div>
                    <strong>Estat actual: </strong>
                    <?php if ($resultat['obj_estat'] == 'disponible'): ?>
                        <span class="badge bg-success">Disponible per a préstec</span>
                    <?php elseif ($resultat['obj_estat'] == 'prestat'): ?>
                        <span class="badge bg-warning text-dark">Prestat actualment</span>
                    <?php else: ?>
                        <span class="badge bg-danger">En manteniment</span>
                    <?php endif; ?>
                </div>
                
                <div>
                    <?php if ($resultat['obj_estat'] == 'disponible'): ?>
                        <?php if ($usuariLoguejat): ?>
                            <a href="productes/sollicitar_prestec.php?id=<?php echo $id_objecte; ?>" 
                                onclick="return confirm('Vols confirmar la sol·licitud de préstec durant 7 dies?');" 
                                class="btn btn-success fw-bold px-4 py-2">Sol·licitar Préstec
                            </a>
                        <?php else: ?>
                            <a href="logat/login.php" class="btn btn-outline-success btn-sm">
                                Inicia sessió per demanar
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>No disponible</button>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php
include_once 'includes/db_close.php';
include 'includes/foot.php';
?>