<?php
include '../includes/menu.php';

if (!$usuariLoguejat) {
    header("Location: ../logat/login.php"); 
    exit;
}

include_once '../includes/db_connect.php';
$categories = $db->query("SELECT * FROM categories ORDER BY cat_nom ASC");
?>

<div class="container my-5" style="max-width: 650px;">
    <div class="card p-4 shadow-sm border-0 bg-white rounded">
        <h2 class="fw-bold text-dark mb-4">Publicar un Nou Objecte</h2>
        
        <form id="formAfegirObjecte">
            <div class="mb-3">
                <label class="form-label fw-bold">Nom de l'objecte</label>
                <input type="text" id="nom" class="form-control" placeholder="Ex: Serra elèctrica" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Descripció de l'estat i ús</label>
                <textarea id="descripcio" class="form-control" rows="4" placeholder="Explica com funciona..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Categoria</label>
                <select id="categoria" class="form-select" required>
                    <option value="">Selecciona una categoria...</option>
                    <?php while ($c = $categories->fetchArray(SQLITE3_ASSOC)): ?>
                        <option value="<?php echo $c['cat_id']; ?>"><?php echo htmlspecialchars($c['cat_nom']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Ruta de la Imatge</label>
                <input type="text" id="imatge" class="form-control" placeholder="Ex: estils/imatges/eina.jpg">
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success w-100 fw-bold py-2">Desar i Publicar</button>
                <a href="gestionarProductes.php" class="btn btn-secondary w-100 fw-bold py-2">Cancel·lar</a>
            </div>
        </form>

        <div id="missatgeFeedback" class="mt-3"></div>
    </div>
</div>

<script>
document.getElementById("formAfegirObjecte").addEventListener("submit", function(e) {
    e.preventDefault();
    const feedback = document.getElementById("missatgeFeedback");

    const dades = {
        nom: document.getElementById("nom").value.trim(),
        descripcio: document.getElementById("descripcio").value.trim(),
        categoria: parseInt(document.getElementById("categoria").value),
        imatge: document.getElementById("imatge").value.trim()
    };

    // Petició POST
    fetch('../api/eines.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dades)
    })
    .then(res => {
        if (!res.ok) throw new Error('Error al servidor');
        return res.json();
    })
    .then(data => {
        if (data.status === "success") {
            feedback.innerHTML = `<div class="alert alert-success m-0">Objecte creat correctament!</div>`;
            setTimeout(() => window.location.href = "gestionarProductes.php?status=afegit", 1500);
        } else {
            feedback.innerHTML = `<div class="alert alert-danger m-0">⚠️ Error: ${data.message || 'No s\'ha pogut desar.'}</div>`;
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