<?php 
include("../includes/menu.php");
?>

<div class="container" style="max-width: 400px; margin-top: 50px;">
    <div class="card p-4 shadow-sm bg-white">
        <h3 class="text-center text-success mb-4">INICIAR SESSIÓ</h3>
        <form id="formLogin">
            <div class="form-group mb-3">
                <label class="form-label">Usuari:</label>
                <input type="text" id="nom" class="form-control" required>
            </div>
            <div class="form-group mb-4">
                <label class="form-label">Contrasenya:</label>
                <input type="password" id="contrassenya" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success" style="width: 100%;">Entrar</button>
        </form>
        <div id="missatge" style="margin-top: 20px; text-align: center;"></div>
    </div>
</div>

<script>
document.getElementById("formLogin").addEventListener("submit", function(e) {
    e.preventDefault();

    const dades = {
        nom: document.getElementById("nom").value,
        contrassenya: document.getElementById("contrassenya").value
    };

    fetch('login.proc.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dades)
    })
    .then(res => {
        if (!res.ok) return res.json().then(err => { throw err; });
        return res.json();
    })
    .then(data => {
        window.location.href = "../productes/gestionarProductes.php";
    })
    .catch(error => {
        document.getElementById("missatge").innerHTML = `<p style="color:red; font-weight:bold">${error.error || "Error en el login"}</p>`;
    });
});
</script>

<?php include("../includes/foot.php"); ?>