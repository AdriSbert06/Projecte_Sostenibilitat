<?php
// Connectar-se a la base de dades (crearà el fitxer 'biblioteca.db' a la mateixa carpeta)
$db = new SQLite3('biblioteca.db');

// 1. Crear la taula categories si no existeix
$db->exec("CREATE TABLE IF NOT EXISTS 'categories' (
    'cat_id'          INTEGER,
    'cat_nom'         TEXT NOT NULL UNIQUE,
    'cat_descripcio'  TEXT,
    PRIMARY KEY('cat_id' AUTOINCREMENT)
);");

// Inserir Categories de mostra (només si vols omplir-la inicialment)
// Nota: Si executes el fitxer molts cops, recorda que tenen la restricció UNIQUE per no duplicar-se.
$db->exec("INSERT OR IGNORE INTO 'categories' ('cat_nom', 'cat_descripcio') VALUES 
('Bricolatge', 'Eines per a reparacions de la llar i construcció (trepants, serres...)'),
('Neteja', 'Electrodomèstics d’ús esporàdic com netejadores de vapor'),
('Càmping i Aventura', 'Tendes de campanya, sacs de dormir i material de muntanya');");


// 2. Crear la taula usuaris si no existeix
$db->exec("CREATE TABLE IF NOT EXISTS 'usuaris' (
    'usu_id'      INTEGER,
    'usu_nom'     TEXT NOT NULL,
    'usu_contra'  TEXT NOT NULL, -- Guardarem el hash MD5 tal com fas a l'IKEA
    'usu_rol'     TEXT NOT NULL DEFAULT 'user', -- 'user' o 'admin' per als permisos JWT
    'usu_mail'    TEXT NOT NULL UNIQUE,
    'usu_barri'   TEXT NOT NULL, -- Filtre de Barcelona (Ex: 'Poblenou', 'Sants')
    PRIMARY KEY('usu_id' AUTOINCREMENT)
);");

// Inserir Usuaris de mostra amb contrasenyes encriptades en MD5
$db->exec("INSERT OR IGNORE INTO 'usuaris' ('usu_id', 'usu_nom', 'usu_contra', 'usu_rol', 'usu_mail', 'usu_barri') VALUES 
(1, 'Joan Pérez', '" . md5('1234') . "', 'user', 'joan@email.com', 'Poblenou'),
(2, 'Laia Gómez', '" . md5('5678') . "', 'user', 'laia@email.com', 'Sants'),
(3, 'Admin Sostenible', '" . md5('admin2026') . "', 'admin', 'admin@biblioteca.cat', 'Eixample');");


// 3. Crear la taula objectes si no existeix (Tots gratuïts)
$db->exec("CREATE TABLE IF NOT EXISTS 'objectes' (
    'obj_id'          INTEGER,
    'obj_nom'         TEXT NOT NULL,
    'obj_imatge'      TEXT, -- Ruta local o URL de la imatge de l'eina
    'obj_descripcio'  TEXT NOT NULL,
    'obj_estat'       TEXT NOT NULL DEFAULT 'disponible', -- 'disponible', 'prestat', 'manteniment'
    'cat_id'          INTEGER NOT NULL,
    'usu_propietari_id' INTEGER NOT NULL, -- L'usuari que cedeix l'objecte gratis
    PRIMARY KEY('obj_id' AUTOINCREMENT),
    FOREIGN KEY('cat_id') REFERENCES categories('cat_id'),
    FOREIGN KEY('usu_propietari_id') REFERENCES usuaris('usu_id') ON DELETE CASCADE
);");

// Inserir Objectes de mostra
$db->exec("INSERT OR IGNORE INTO 'objectes' ('obj_id', 'obj_nom', 'obj_imatge', 'obj_descripcio', 'obj_estat', 'cat_id', 'usu_propietari_id') VALUES 
(1, 'Trepant percutor Bosch', 'https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80', 'Trepant de 800W amb cable. Inclou estoig amb broques.', 'disponible', 1, 1),
(2, 'Netejadora de vapor Kärcher', 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?q=80', 'Ideal per a netejar sofàs, catifes i matalassos en profunditat.', 'disponible', 2, 2),
(3, 'Tenda de campanya (4 pers.)', 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?q=80', 'Tenda tipus iglú de fàcil muntatge. Impermeable.', 'prestat', 3, 1);");


// 4. Crear la taula prestecs si no existeix (Trazabilitat de l'economia circular)
$db->exec("CREATE TABLE IF NOT EXISTS 'prestecs' (
    'pre_id'           INTEGER,
    'obj_id'           INTEGER NOT NULL,
    'usu_id'           INTEGER NOT NULL, -- L'usuari que demana el préstec
    'pre_fecha_inici'  TEXT NOT NULL,    -- SQLite guarda les dates com TEXT (YYYY-MM-DD)
    'pre_fecha_fi'     TEXT NOT NULL,
    'pre_estat'        TEXT NOT NULL DEFAULT 'actiu', -- 'actiu', 'retornat', 'retrassat'
    PRIMARY KEY('pre_id' AUTOINCREMENT),
    FOREIGN KEY('obj_id') REFERENCES objectes('obj_id') ON DELETE CASCADE,
    FOREIGN KEY('usu_id') REFERENCES usuaris('usu_id') ON DELETE CASCADE
);");

// Inserir un Préstec de mostra (Laia es queda la tenda d'en Joan durant 7 dies)
$db->exec("INSERT OR IGNORE INTO 'prestecs' ('obj_id', 'usu_id', 'pre_fecha_inici', 'pre_fecha_fi', 'pre_estat') VALUES 
(3, 2, '2026-05-20', '2026-05-27', 'actiu');");

// Tancar la connexió de manera segura
$db->close();

echo "Base de dades 'biblioteca.db' creada i configurada correctament a la teva carpeta api!";
?>