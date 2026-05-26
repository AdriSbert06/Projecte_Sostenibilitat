<?php
include '../includes/menu.php';

if (!$usuariLoguejat) {
    header("Location: ../logat/login.php"); 
    exit;
}

// Passem el rol d'administrador a una variable JS per saber si pintem la columna "Propietari"
$es_admin = (isset($usuariLoguejat['rol']) && $usuariLoguejat['rol'] === 'admin') ? 1 : 0;
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark" id="titol-gestio">
                La meva Gestió de Productes
            </h2>
            <?php if ($es_admin): ?>
                <span class="badge bg-danger px-3 py-2 fw-bold text-white">Mode Admin: Control de tot el catàleg</span>
            <?php endif; ?>
        </div>
        <a href="afegirProducte.php" class="btn btn-success fw-bold">+ Afegir Nou Objecte</a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'afegit'): ?>
            <div class="alert alert-success shadow-sm">Nou producte publicat amb èxit!</div>
        <?php elseif ($_GET['status'] === 'modificat'): ?>
            <div class="alert alert-info shadow-sm">Producte actualitzat correctament a la base de dades.</div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div id="status-ajax"></div>

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
                <tbody id="taula-productes">
                    <tr>
                        <td colspan="<?php echo $es_admin ? '6' : '5'; ?>" class="text-center text-muted py-4">Carregant productes...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const esAdmin = <?php echo $es_admin; ?>;

if (esAdmin) {
    document.getElementById("titol-gestio").innerText = "📦 Administració Total de Productes";
}

// 1. CARREGAR PRODUCTES DES DE L'API (Distingeix el contingut segons la sessió de l'API)
fetch('../api/eines.php?gestion=true')
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById("taula-productes");
        tbody.innerHTML = "";

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${esAdmin ? '6' : '5'}" class="text-center text-muted py-4">No s'ha trobat cap producte.</td></tr>`;
            return;
        }

        data.forEach(p => {
            const tr = document.createElement("tr");
            tr.id = "fila-" + p.obj_id;

            const badgeColor = (p.obj_estat === 'disponible') ? 'bg-success' : 'bg-danger';
            const nomEscapat = p.obj_nom.replace(/'/g, "\\'");

            let columnaPropietari = "";
            if (esAdmin) {
                columnaPropietari = `<td><span class="text-primary fw-bold">👤 ${p.propietari_nom || 'Anònim'}</span></td>`;
            }

            tr.innerHTML = `
                <td>
                    <img src="../${p.obj_imatge || ''}" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='https://placehold.co/50'">
                </td>
                <td><span class="fw-bold text-dark fs-5">${p.obj_nom}</span></td>
                <td><span class="badge bg-secondary px-3 py-2 rounded-pill">${p.cat_nom}</span></td>
                ${columnaPropietari}
                <td>
                    <span class="badge ${badgeColor} px-3 py-2 rounded">${p.obj_estat}</span>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="modificarProducte.php?id=${p.obj_id}" class="btn btn-primary btn-sm px-3">Editar</a>
                        <button class="btn btn-danger btn-sm px-3" onclick="eliminarProducte(${p.obj_id}, '${nomEscapat}')">Eliminar</button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    })
    .catch(err => {
        console.error("Error carregant els productes:", err);
        document.getElementById("taula-productes").innerHTML = `<tr><td colspan="${esAdmin ? '6' : '5'}" class="text-center text-danger py-4">Error en carregar les dades de l'API.</td></tr>`;
    });

// 2. ELIMINAR PRODUCTE ASÍNCRONAMENT (DELETE)
function eliminarProducte(id, nom) {
    if (!confirm(`Segur que vols eliminar "${nom}" del sistema?`)) return;

    fetch('../api/eines.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ obj_id: parseInt(id) })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            document.getElementById("fila-" + id).remove();
            document.getElementById("status-ajax").innerHTML = `<div class="alert alert-warning shadow-sm">Producte "${nom}" eliminat correctament.</div>`;
            
            // Si la taula es queda buida, pintem el missatge de buit
            const tbody = document.getElementById("taula-productes");
            if (tbody.children.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${esAdmin ? '6' : '5'}" class="text-center text-muted py-4">No s'ha trobat cap producte.</td></tr>`;
            }
        } else {
            alert("⚠️ Error: " + (data.message || "No s'ha pogut eliminar."));
        }
    })
    .catch(error => {
        console.error("Error eliminant:", error);
        alert("⚠️ Error de connexió amb l'API.");
    });
}
</script>

<?php 
include '../includes/foot.php'; 
?>