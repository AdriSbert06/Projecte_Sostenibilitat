<?php
    include '../includes/menu.php';

    if (!$usuariLoguejat) {
        header("Location: ../logat/login.php"); 
        exit;
    }

    include_once '../includes/db_connect.php';

    $objecteId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($objecteId <= 0) {
        header("Location: gestionarProductes.php");
        exit;
    }

    // Recuperem les dades actuals de l'objecte (assegurem que sigui del propietari logat)
    $stmt = $db->prepare("SELECT * FROM objectes WHERE obj_id = :id AND usu_propietari_id = :usu_id");
    $stmt->bindValue(':id', $objecteId, SQLITE3_INTEGER);
    $stmt->bindValue(':usu_id', $usuariLoguejat['id'], SQLITE3_INTEGER);
    $obj = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$obj) {
        // Si l'objecte no existeix o no és seu, el fem fora
        header("Location: gestionarProductes.php");
        exit;
    }

    $categories = $db->query("SELECT * FROM categories ORDER BY cat_nom ASC");
?>

<div class="container my-5" style="max-width: 650px;">
    <div class="card p-4 shadow-sm border-0 bg-white rounded">
        <h2 class="fw-bold text-dark mb-4">Modificar Objecte</h2>
        
        <form id="formModificarObjecte">
            <input type="hidden" id="obj_id" value="<?php echo $obj['obj_id']; ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Nom de l'objecte</label>
                <input type="text" id="nom" class="form-control" value="<?php echo htmlspecialchars($obj['obj_nom']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Descripció de l'estat i ús</label>
                <textarea id="descripcio" class="form-control" rows="4" required><?php echo htmlspecialchars($obj['obj_descripcio']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Categoria</label>
                <select id="categoria" class="form-select" required>
                    <?php while ($c = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $c['cat_id']; ?>" <?php echo ($c['cat_id'] == $obj['cat_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['cat_nom']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Ruta de la Imatge</label>
                <input type="text" id="imatge" class="form-control" value="<?php echo htmlspecialchars($obj['obj_imatge']); ?>">
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success w-100 fw-bold py-2">Desar Canvis</button>
                <a href="gestionarProductes.php" class="btn btn-secondary w-100 fw-bold py-2">Cancel·lar</a>
            </div>
        </form>

        <div id="missatgeFeedback" class="mt-3"></div>
    </div>
</div>

<script>
document.getElementById("formModificarObjecte").addEventListener("submit", function(e) {
    e.preventDefault();
    const feedback = document.getElementById("missatgeFeedback");

    const dades = {
        obj_id: parseInt(document.getElementById("obj_id").value),
        obj_nom: document.getElementById("nom").value.trim(),
        obj_descripcio: document.getElementById("descripcio").value.trim(),
        cat_id: parseInt(document.getElementById("categoria").value),
        obj_imatge: document.getElementById("imatge").value.trim()
    };

    // Petició PUT
    fetch('../api/eines.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dades)
    })
    .then(res => {
        if (!res.ok) throw new Error('Error al servidor');
        return res.json();
    })
    .then(data => {
        if (data.status === "success") {
            feedback.innerHTML = `<div class="alert alert-success m-0"> Canvis guardats correctament!</div>`;
            setTimeout(() => window.location.href = "gestionarProductes.php?status=modificat", 1500);
        } else {
            feedback.innerHTML = `<div class="alert alert-danger m-0">⚠️ Error: ${data.message || 'No s\'han pogut desar els canvis.'}</div>`;
        }
    })
    .catch(error => {
        console.error("Error:", error);
        feedback.innerHTML = `<div class="alert alert-danger m-0">⚠️ Error de connexió amb l'API.</div>`;
    });
});
</script>

<?php 
include_once '../includes/db_close.php'; 
include '../includes/foot.php'; 
?>