# 🛠️ Tool Sharing

Plataforma web d'economia circular (ODS 12) per compartir i prestar eines entre veïns de forma gratuïta.

---

## Objectiu

Permetre als usuaris registrar objectes propis en un catàleg públic perquè altres membres de la comunitat els puguin demanar prestats, optimitzant el consum local.

---


## Estructura


```text
├── admin/
│   ├── categories.php            # Panell d'administració per gestionar categories
│   └── usuaris.php               # Panell d'administració per gestionar usuaris
├── api/
│   ├── apiController.php         # Controlador central de l'API REST (gestiona GET, POST, PUT, DELETE)
│   └── eines.php                 # Funcions lògiques pures per a la persistència de productes
├── database/
│   └── tools.db                  # Base de dades relacional en SQLite3
├── estils/
│   └── css.css                   # Fitxer d'estils personalitzats
├── includes/
│   ├── db_close.php              # Tancament de la connexió a la base de dades
│   ├── db_connect.php            # Connexió inicial a la base de dades SQLite3
│   ├── foot.php                  # Peu de pàgina comú del lloc
│   └── menu.php                  # Barra de navegació superior amb lògica de rutes relatives
├── logat/
│   ├── auth.php                  # Funcions de validació i generació de tokens de sessió
│   ├── login.php                 # Formulari i inici de sessió d'usuaris
│   ├── logout.php                # Destrucció de la sessió activa
│   └── registrar.php             # Formulari i registre de nous usuaris
├── productes/
│   └── gestionarProductes.php    # Panell privat on cada usuari gestiona les seves eines
├── detalls.php                   # Vista ampliada i fitxa tècnica d'un objecte específic
├── index.php                     # Catàleg principal i pàgina d'inici de l'aplicació
└── sollicitar_prestec.php        # Processament i registre de les sol·licituds de préstec
