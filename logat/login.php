<?php 
include("includes/head.html"); 
include("includes/menu.php");
?>

<div class="container" style="max-width: 400px; margin-top: 50px;">
    <h3>INICIAR SESSIÓ</h3>
    <form id="formLogin">
        <div class="form-group">
            <label>Usuari:</label>
            <input type="text" id="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Contrasenya:</label>
            <input type="password" id="contrassenya" class="form-control" required>
        </div>
        <br>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Entrar</button>
    </form>
    <div id="missatge" style="margin-top: 20px; text-align: center;"></div>
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
        window.location.href = "gestionarProductes.php";
    })
    .catch(error => {
        document.getElementById("missatge").innerHTML = `<p style="color:red">${error.error || "Error en el login"}</p>`;
    });
});
</script>

<?php include("includes/foot.html"); ?>