<?php 
include("../includes/menu.php");
?>

<div class="container" style="max-width: 500px; margin-top: 40px; margin-bottom: 40px;">
    <div class="card p-4 shadow-sm bg-white">
        <h3 class="text-center text-success mb-4">CREAR COMPTE</h3>
        
        <form id="formRegistre">
            <div class="form-group mb-3">
                <label class="form-label">Nom complet:</label>
                <input type="text" id="nom" class="form-control" placeholder="Ex: Joan Garcia" required>
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label">Correu electrònic:</label>
                <input type="email" id="mail" class="form-control" placeholder="Ex: joal@gmail.com" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Barri de Barcelona:</label>
                <select id="barri" class="form-select" required>
                    <option value="" disabled selected>Tria el teu barri...</option>
                    <option value="Poblenou">Poblenou</option>
                    <option value="Sants">Sants</option>
                    <option value="Eixample">Eixample</option>
                    <option value="Gràcia">Gràcia</option>
                    <option value="Sant Andreu">Sant Andreu</option>
                    <option value="Les Corts">Les Corts</option>
                    <option value="Ciutat Vella">Ciutat Vella</option>
                    <option value="Horta-Guinardó">Horta-Guinardó</option>
                    <option value="Nou Barris">Nou Barris</option>
                    <option value="Sarrià-Sant Gervasi">Sarrià-Sant Gervasi</option>
                </select>
            </div>
            
            <div class="form-group mb-4">
                <label class="form-label">Contrasenya:</label>
                <input type="password" id="contrassenya" class="form-control" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn btn-success w-100 mb-3">Registrar-me</button>
            
            <div class="text-center">
                <a href="login.php" class="text-decoration-none text-secondary small">Ja tens un compte? Inicia sessió</a>
            </div>
        </form>
        
        <div id="missatge" style="margin-top: 20px; text-align: center;"></div>
    </div>
</div>

<script>
document.getElementById("formRegistre").addEventListener("submit", function(e) {
    e.preventDefault();

    const dades = {
        nom: document.getElementById("nom").value,
        mail: document.getElementById("mail").value,
        barri: document.getElementById("barri").value,
        contrassenya: document.getElementById("contrassenya").value
    };

    fetch('registrar.proc.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(dades)
    })
    .then(res => {
        if (!res.ok) return res.json().then(err => { throw err; });
        return res.json();
    })
    .then(data => {
        document.getElementById("missatge").innerHTML = `<p style="color:green; font-weight:bold;">¡Registre correcte! Redirigint al login...</p>`;
        setTimeout(() => {
            window.location.href = "login.php";
        }, 2000);
    })
    .catch(error => {
        document.getElementById("missatge").innerHTML = `<p style="color:red; font-weight:bold;">${error.error || "Error en el registre"}</p>`;
    });
});
</script>

<?php include("../includes/foot.php"); ?> 